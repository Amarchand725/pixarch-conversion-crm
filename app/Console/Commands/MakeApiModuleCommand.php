<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;

class MakeApiModuleCommand extends Command
{
    protected $signature = 'make:api {name} {--fields=}';
    protected $description = 'Generate a new API CRUD module with model, migration, repository, controller, resource, and routes';

    protected Filesystem $files;
    protected string $basePath = 'app/Modules';

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle(): void
    {
        $module = Str::studly($this->argument('name'));
        $table  = Str::snake(Str::pluralStudly($module));
        $fields = $this->prepareFields($this->option('fields'));

        $this->info("🚀 Creating API module: {$module}");

        $this->createDirectories($module);
        $this->createModel($module, $fields);
        $this->createMigration($module, $table, $fields);
        $this->createRepositoryContract($module);
        $this->createRepository($module);
        $this->createRequest($module, $fields);
        $this->createController($module);
        $this->createResource($module, $fields);
        $this->createRoute($module);
        $this->createConfig($module, $fields);
        $this->createSeeder($module);
        $this->registerPermissions($module);

        $this->info("✅ API Module [{$module}] generated successfully!");
    }

    // ------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------

    protected function prepareFields(?string $input): array
    {
        $raw = $input ? explode(',', $input) : [];

        $base = ['id:increments', 'uuid:uuid'];

        if (empty($raw)) {
            $dynamic = ['name:string', 'status:boolean'];
        } else {
            $dynamic = $raw;
            $hasStatus = collect($dynamic)->contains(fn($f) => Str::startsWith($f, 'status:'));
            if (! $hasStatus) {
                $dynamic[] = 'status:boolean';
            }
        }

        return collect($base)
            ->merge($dynamic)
            ->unique(fn($f) => explode(':', $f)[0])
            ->values()
            ->toArray();
    }

    protected function createDirectories($module)
    {
        $paths = [
            "{$this->basePath}/{$module}/Http/Controllers/Api",
            "{$this->basePath}/{$module}/Http/Requests",
            "{$this->basePath}/{$module}/Models",
            "{$this->basePath}/{$module}/Repositories/Contracts",
            "{$this->basePath}/{$module}/Repositories/Eloquent",
            "{$this->basePath}/{$module}/Http/Resources",
            "config/modules",
            "database/seeders",
        ];

        foreach ($paths as $path) {
            $this->files->ensureDirectoryExists(base_path($path));
        }
    }

    protected function createModel($module, $fields)
    {
        $path = "{$this->basePath}/{$module}/Models/{$module}.php";
        $fillable = implode("', '", $fields ?: ['name', 'status']);
        $table = strtolower($module) . 's';

        $stub = <<<PHP
        <?php

        namespace App\Modules\\{$module}\Models;

        use Illuminate\Database\Eloquent\Model;
        use Illuminate\Database\Eloquent\SoftDeletes;
        use Spatie\Activitylog\Traits\LogsActivity;
        use Spatie\Activitylog\LogOptions;

        class {$module} extends Model
        {
            use SoftDeletes, LogsActivity;

            protected \$fillable = ['{$fillable}'];

            /**
             * Configure Spatie Activity Log options.
             */
            public function getActivityLogOptions(): LogOptions
            {
                return LogOptions::defaults()
                    ->useLogName(strtolower('{$module}'))
                    ->logFillable()
                    ->logOnlyDirty();
            }
        }
        PHP;

        $this->files->put(base_path($path), $stub);
    }

    protected function createMigration($module, $table, $fields)
    {
        $migrationName = date('Y_m_d_His') . "_create_{$table}_table.php";
        $migrationPath = base_path("database/migrations/{$migrationName}");

        $columns = collect($fields)
            ->map(function ($field) {
                [$name, $type] = explode(':', $field);
                if ($name === 'id') return "\$table->id();";
                if ($name === 'uuid') return "\$table->uuid('uuid')->unique();";
                if ($name === 'status') return "\$table->boolean('status')->default(true);";
                return "\$table->{$type}('{$name}')->nullable();";
            })
            ->implode("\n            ");

        $stub = <<<PHP
        <?php

        use Illuminate\\Database\\Migrations\\Migration;
        use Illuminate\\Database\\Schema\\Blueprint;
        use Illuminate\\Support\\Facades\\Schema;

        return new class extends Migration {
            public function up(): void
            {
                Schema::create('{$table}', function (Blueprint \$table) {
                    {$columns}
                    \$table->morphs('author');
                    \$table->softDeletes();
                    \$table->timestamps();
                });
            }

            public function down(): void
            {
                Schema::dropIfExists('{$table}');
            }
        };
        PHP;

        $this->files->put($migrationPath, $stub);
    }

    protected function createRepositoryContract($module)
    {
        $path = base_path("app/Modules/{$module}/Repositories/Contracts/{$module}Contract.php");

        $stub = <<<PHP
        <?php

        namespace App\Modules\\{$module}\Repositories\Contracts;

        use App\Repositories\Contracts\BaseContract;

        interface {$module}Contract extends BaseContract
        {
            // Add {$module}-specific methods if needed
        }
        PHP;

        File::put($path, $stub);
        $this->info("📄 Contract created: {$path}");
    }

    protected function createRepository($module)
    {
        $path = base_path("app/Modules/{$module}/Repositories/Eloquent/{$module}Repository.php");

        $stub = <<<PHP
        <?php

        namespace App\Modules\\{$module}\Repositories\Eloquent;

        use App\Repositories\Eloquent\BaseRepository;
        use App\Modules\\{$module}\Repositories\Contracts\\{$module}Contract;
        use App\Modules\\{$module}\Models\\{$module};

        class {$module}Repository extends BaseRepository implements {$module}Contract
        {
            public function __construct({$module} \$model)
            {
                parent::__construct(\$model);
            }
        }
        PHP;

        File::put($path, $stub);
        $this->info("📚 Repository created: {$path}");
    }

    protected function createRequest(string $module, array $fields): void
    {
        // build rules array lines
        $rulesLines = [];

        foreach ($fields as $f) {
            // $f expected like: 'name:string'
            [$name, $type] = array_pad(explode(':', $f), 2, 'string');

            // skip system columns
            if (in_array($name, ['id', 'uuid', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            // map type -> rule
            $type = strtolower($type);
            switch ($type) {
                case 'string':
                case 'varchar':
                    $rule = "required|string|max:255";
                    break;
                case 'text':
                    $rule = "nullable|string";
                    break;
                case 'integer':
                case 'int':
                    $rule = "nullable|integer";
                    break;
                case 'boolean':
                case 'bool':
                    $rule = "nullable|boolean";
                    break;
                case 'decimal':
                case 'float':
                case 'double':
                case 'numeric':
                    $rule = "nullable|numeric";
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $rule = "nullable|date";
                    break;
                case 'uuid':
                    $rule = "nullable|uuid";
                    break;
                default:
                    // fallback to string
                    $rule = "nullable|string|max:255";
                    break;
            }

            $rulesLines[] = "            '{$name}' => '{$rule}',";
        }

        $rulesBlock = implode("\n", $rulesLines);

        $path = "{$this->basePath}/{$module}/Http/Requests/{$module}Request.php";

        $stub = <<<PHP
    <?php

    namespace App\Modules\\{$module}\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class {$module}Request extends FormRequest
    {
        public function authorize(): bool
        {
            return true;
        }

        public function rules(): array
        {
            return [
    {$rulesBlock}
            ];
        }
    }
    PHP;

        // ensure directory exists
        $dir = dirname(base_path($path));
        if (!\Illuminate\Support\Facades\File::isDirectory($dir)) {
            \Illuminate\Support\Facades\File::makeDirectory($dir, 0755, true);
        }

        $this->files->put(base_path($path), $stub);
    }

    protected function createController($module)
    {
        $variable = Str::camel($module);
        $path = base_path("app/Modules/{$module}/Http/Controllers/Api/{$module}Controller.php");

        $stub = <<<PHP
        <?php

        namespace App\Modules\\{$module}\Http\Controllers\Api;

        use App\Http\Controllers\Controller;
        use App\Modules\\{$module}\Repositories\Eloquent\\{$module}Repository;
        use App\Modules\\{$module}\Http\Requests\\{$module}Request;
        use App\Modules\\{$module}\Http\Resources\\{$module}Resource;
        use Illuminate\Http\JsonResponse;
        use Exception;

        class {$module}Controller extends Controller
        {
            protected \${$variable}Repo;

            public function __construct({$module}Repository \${$variable}Repo)
            {
                \$this->{$variable}Repo = \${$variable}Repo;
            }

            public function index(): JsonResponse
            {
                \$data = \$this->{$variable}Repo->getAll();
                return response()->json({$module}Resource::collection(\$data));
            }

            public function store({$module}Request \$request): JsonResponse
            {
                \$payload = \$request->validated();

                try {
                    \$model = \$this->{$variable}Repo->storeModel(\$payload);
                    return response()->json(new {$module}Resource(\$model), 201);
                } catch (Exception \$e) {
                    return response()->json(['error' => \$e->getMessage()], 500);
                }
            }

            public function show(\$id): JsonResponse
            {
                try {
                    \$model = \$this->{$variable}Repo->showModel(\$id);
                    return response()->json(new {$module}Resource(\$model));
                } catch (Exception \$e) {
                    return response()->json(['error' => \$e->getMessage()], 404);
                }
            }

            public function update({$module}Request \$request, \$id): JsonResponse
            {
                \$payload = \$request->validated();

                try {
                    \$model = \$this->{$variable}Repo->updateModel(\$id, \$payload);
                    return response()->json(new {$module}Resource(\$model));
                } catch (Exception \$e) {
                    return response()->json(['error' => \$e->getMessage()], 500);
                }
            }

            public function destroy(\$id): JsonResponse
            {
                try {
                    \$this->{$variable}Repo->softDeleteModel(\$id);
                    return response()->json(['message' => '{$module} deleted successfully.']);
                } catch (Exception \$e) {
                    return response()->json(['error' => \$e->getMessage()], 500);
                }
            }

            public function restore(\$id): JsonResponse
            {
                try {
                    \$this->{$variable}Repo->restoreModel(\$id);
                    return response()->json(['message' => '{$module} restored successfully.']);
                } catch (Exception \$e) {
                    return response()->json(['error' => \$e->getMessage()], 500);
                }
            }

            public function forceDelete(\$id): JsonResponse
            {
                try {
                    \$this->{$variable}Repo->permanentlyDeleteModel(\$id);
                    return response()->json(['message' => '{$module} permanently deleted.']);
                } catch (Exception \$e) {
                    return response()->json(['error' => \$e->getMessage()], 500);
                }
            }
        }
        PHP;

        File::put($path, $stub);
        $this->info("🧠 API Controller created: {$path}");
    }

    protected function createResource($module, $fields)
    {
        $path = "{$this->basePath}/{$module}/Http/Resources/{$module}Resource.php";
        $fieldArray = collect($fields)
            ->filter(fn($f) => !Str::startsWith($f, ['id:', 'uuid:']))
            ->map(fn($f) => "'" . explode(':', $f)[0] . "' => \$this->" . explode(':', $f)[0])
            ->implode(",\n            ");

        $stub = <<<PHP
        <?php

        namespace App\Modules\\{$module}\Http\Resources;

        use Illuminate\Http\Resources\Json\JsonResource;

        class {$module}Resource extends JsonResource
        {
            public function toArray(\$request): array
            {
                return [
                    'id' => \$this->id,
                    'uuid' => \$this->uuid,
                    {$fieldArray},
                    'created_at' => \$this->created_at,
                    'updated_at' => \$this->updated_at,
                ];
            }
        }
        PHP;

        $this->files->put(base_path($path), $stub);
    }

    protected function createRoute($module)
    {
        $snake = Str::snake($module);
        $controller = "{$module}Controller";
        $routeFile = base_path("routes/{$snake}.php");

        $content = <<<PHP
        <?php

        use Illuminate\Support\Facades\Route;
        use App\Modules\\{$module}\Http\Controllers\\{$controller};

        // 🧩 {$module} Module Routes
        Route::middleware(['web', 'auth'])
            // ->prefix('{$snake}')
            ->name('{$snake}.')
            ->group(function () {

                // 🧱 Resource CRUD
                Route::resource('{$snake}', {$controller}::class)
                    ->parameters(['{$snake}' => '{$snake}']);

                // 🧩 Extra Actions (Grouped by Controller)
                Route::controller({$controller}::class)->group(function () {
                    Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
                    Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
                    Route::post('{id}/restore', 'restore')->name('restore');
                    Route::delete('{id}/force-delete', 'forceDelete')->name('forceDelete');
                });
            });
        PHP;

        File::put($routeFile, $content);

        $this->info("✅ Route file created: routes/{$snake}.php");
    }
    protected function createConfig($module, $fields)
    {
        $configPath = "config/modules/" . strtolower($module) . ".php";
        $config = [
            'fields' => collect($fields)->map(function ($f) {
                [$name, $type] = explode(':', $f);
                return [
                    'name' => $name,
                    'type' => $type,
                    'show_in_list' => true,
                    'show_in_form' => true,
                    'show_in_create' => true,
                    'show_in_edit' => true,
                    'show_in_show' => true,
                ];
            })->values()->toArray(),
        ];
        $this->files->put(config_path(strtolower($module) . '.php'), '<?php return ' . var_export($config, true) . ';');
    }

    protected function createSeeder($module)
    {
        $path = "database/seeders/{$module}Seeder.php";
        $stub = <<<PHP
        <?php

        namespace Database\\Seeders;

        use Illuminate\\Database\\Seeder;
        use App\Modules\\{$module}\Models\\{$module};

        class {$module}Seeder extends Seeder
        {
            public function run(): void
            {
                {$module}::factory()->count(5)->create();
            }
        }
        PHP;

        $this->files->put(base_path($path), $stub);
    }

    protected function registerPermissions($module)
    {
        $actions = ['view', 'create', 'edit', 'delete', 'restore', 'force_delete'];

        foreach ($actions as $action) {
            Permission::firstOrCreate(['name' => strtolower($module) . '.' . $action]);
        }
    }
}
