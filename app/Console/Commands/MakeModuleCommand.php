<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name} {--fields=}';
    protected $description = 'Generate a new Blade CRUD module with model, migration, repository, controller, and views';

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

        $this->info("🚀 Creating module: {$module}");

        $this->createDirectories($module);
        $this->createModel($module, $fields);
        $this->createMigration($module, $table, $fields);
        $this->createController($module);
        $this->createRequest($module, $fields);
        $this->createRepositoryContract($module);
        $this->createRepository($module);
        $this->createViews($module, $fields);
        $this->createRoute($module);
        // $this->registerPermissions($module);

        $this->info("✅ Module [{$module}] generated successfully!");
    }

    // -----------------------------
    // Helpers
    // -----------------------------

    protected function prepareFields(?string $input): array
    {
        $raw = $input ? explode(',', $input) : [];

        $base = ['id:increments', 'uuid:uuid'];

        if (empty($raw)) {
            $dynamic = ['status_id:integer', 'name:string'];
        } else {
            $dynamic = $raw;
            $hasStatus = collect($dynamic)->contains(fn($f) => Str::startsWith($f, 'status:'));
            if (! $hasStatus) {
                $dynamic[] = ['author_id:integer', 'status_id:integer'];
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
            "{$this->basePath}/{$module}/Http/Controllers",
            "{$this->basePath}/{$module}/Http/Requests",
            "{$this->basePath}/{$module}/Models",
            "{$this->basePath}/{$module}/Repositories/Contracts",
            "{$this->basePath}/{$module}/Repositories/Eloquent",
        ];

        foreach ($paths as $path) {
            $this->files->ensureDirectoryExists(base_path($path));
        }
    }

    protected function createModel($module, $fields)
    {
        $path = "{$this->basePath}/{$module}/Models/{$module}.php";
        $fillable = implode("', '", ['name', 'status_id']);

        $stub = <<<PHP
        <?php

        namespace App\Modules\\{$module}\Models;

        use Illuminate\Database\Eloquent\Model;
        use App\Models\Traits\ModelTrait;
        use Illuminate\Database\Eloquent\SoftDeletes;
        use Spatie\Activitylog\Traits\LogsActivity;
        use Spatie\Activitylog\LogOptions;
        use Illuminate\Database\Eloquent\Factories\HasFactory;
        use App\Models\Status;
        use App\Models\User;

        class {$module} extends Model
        {
            use SoftDeletes, LogsActivity, ModelTrait, HasFactory;

            protected \$fillable = ['{$fillable}'];

            protected static function booted()
            {
                static::creating(function (\$model) {
                    if (empty(\$model->status_id)) {
                        \$model->status_id = Status::where('model', '{$module}')
                            ->where('name', 'active')
                            ->value('id');
                    }
                });
            }

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

            public function status()
            {
                return \$this->belongsTo(Status::class, 'status_id');
            }

            public function author()
            {
                return \$this->belongsTo(User::class, 'author_id');
            }
        }
        PHP;

        $this->files->put(base_path($path), $stub);
    }

    protected function createMigration($module, $table, $fields)
    {
        $migrationDir = base_path('database/migrations');

        // Check if migration already exists
        $existing = collect(glob($migrationDir . '/*_create_' . $table . '_table.php'))
            ->isNotEmpty();

        if ($existing) {
            return; // skip creation
        }

        $migrationName = date('Y_m_d_His') . "_create_{$table}_table.php";
        $migrationPath = $migrationDir . '/' . $migrationName;

        $columns = collect($fields)
            ->map(function ($field) {
                [$name, $type] = explode(':', $field);
                if ($name === 'id') return "\$table->id();";
                if ($name === 'uuid') return "\$table->uuid('uuid')->unique();";
                if ($name === 'status_id') return "\$table->foreignId('status_id')->nullable()->constrained('statuses')->nullOnDelete();";
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
                    \$table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
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

    protected function createController($module)
    {
        $variable = Str::camel($module);
        $pluralVariable = Str::plural($variable);
        $pluralRoute = Str::snake(Str::pluralStudly($module));
        $path = base_path("app/Modules/{$module}/Http/Controllers/{$module}Controller.php");

        $stub = <<<PHP
        <?php

        namespace App\Modules\\{$module}\Http\Controllers;

        use App\Http\Controllers\BackOffice\BaseModuleController;
        use App\Modules\\{$module}\Repositories\Contracts\\{$module}Contract;
        use App\Modules\\{$module}\Http\Requests\\{$module}Request;
        use App\Modules\\{$module}\Models\\{$module};
        use Exception;
        use Illuminate\Support\Facades\DB;
        use Illuminate\Http\Request;
        use App\Models\Status;

        class {$module}Controller extends BaseModuleController
        {
            protected \$status;
            
            public function __construct(
                protected {$module}Contract \${$variable}Repo
            ){
                \$this->status = new Status();
                // Initialize common module variables automatically
                \$this->autoInit();
            }

            public function index(Request \$request)
            {
                \$columns = [
                    'name'      => ['label' => 'name', 'searchable' => 'name'],
                    'status'     => ['label' => 'Status', 'html' => true, 'searchable' => false],
                    'author_id'     => ['label' => 'Author', 'html' => true, 'searchable' => false],
                    'created_at' => ['label' => 'Created At', 'searchable' => 'created_at'],
                    'action'     => ['label' => 'Action', 'html' => true, 'searchable' => false],
                ];

                \$query = \$this->{$variable}Repo->getAll();
                \$total_count = \$query->count();

                \$dataTable = new \\App\\Services\\DataTableService(
                    model: \$query,
                    columns: \$columns,
                    rowFormatter: [\$this, 'formatRow']
                );

                if (\$request->ajax() && \$request->loaddata == "yes") {
                    return \$dataTable->ajax();
                }

                return view(strtolower(\$this->pathInitialize.'.index'), \$this->viewWithVars(get_defined_vars()));
            }

            public function formatRow(\$row)
            {
                \$status = \$row->status?->name ?? 'de-active';
                \$row->status = '<span class="badge rounded-pill px-3 py-2 '. badgeClass(\$status) .'">'
                            . strtoupper(\$status) .
                            '</span>';
                
                \$row->author_id = \$row->author
                        ? view('back-office.partials.avatar', ['user' => \$row->author])->render()
                        : '-';

                \$row->action = view('back-office.partials.actions', [
                    'model'            => \$row,
                    'permissionPrefix' => \$this->permissionPrefix,
                    'routeInitialize'  => \$this->routePrefix,
                    'singularLabel'    => \$this->singularLabel,
                ])->render();

                return \$row;
            }

            public function create()
            {
                return (string) view(\$this->pathInitialize.'.create_content', get_defined_vars());
            }

            public function store({$module}Request \$request)
            {
                \$payload = \$request->validated();
                try {
                    \$response = null;
                    DB::transaction(function () use (&\$response, \$payload) {
                        \$this->{$variable}Repo->storeModel(\$payload);
                    });
                    return successResponse(\$response, module_message('created', \$this->singularLabel));
                } catch (Exception \$e) {
                    return response()->json([
                        'status' => false,
                        'error' => \$e->getMessage()
                    ]);
                }
            }

            public function edit({$module} \${$variable})
            {
                \$statuses = \$this->status->where('model', '{$module}')->get();
                \$model = \$this->{$variable}Repo->showModel(\${$variable});
                return (string) view(\$this->pathInitialize.'.edit_content', get_defined_vars());
            }

            public function update({$module}Request \$request, {$module} \${$variable})
            {
                \$payload = \$request->validated();
                try {
                    \$response = null;
                    DB::transaction(function () use (&\$response, \$payload, \${$variable}) {
                        \$this->{$variable}Repo->updateModel(\${$variable}, \$payload);
                    });
                    return successResponse(\$response, module_message('updated', \$this->singularLabel));
                } catch (Exception \$e) {
                    return response()->json([
                        'status' => false,
                        'error' => \$e->getMessage()
                    ]);
                }
            }

            public function show({$module} \${$variable})
            {
                \$model = \$this->{$variable}Repo->showModel(\${$variable});
                return (string) view(\$this->pathInitialize.'.show_content', get_defined_vars());
            }

            public function destroy({$module} \${$variable})
            {
                try {
                    if(\$this->{$variable}Repo->softDeleteModel(\${$variable})) {
                        return response()->json([
                            'status' => true,
                            'message' => module_message('deleted', \$this->singularLabel)
                        ]);
                    } else{
                        return response()->json([
                            'status' => false,
                            'error' => \$this->singularLabel.' not deleted try again.'
                        ]);
                    }
                } catch (Exception \$e) {
                    return response()->json([
                        'status' => false,
                        'error' => \$e->getMessage()
                    ]);
                }
            }

            public function restore({$module} \${$variable})
            {
                try {
                    if(\$this->{$variable}Repo->restoreModel(\${$variable})) {
                        return redirect()->back()->with('message', module_message('restored', \$this->singularLabel));
                    } else {
                        return false;
                    }
                } catch (Exception \$e) {
                    return response()->json([
                        'status' => false,
                        'error' => \$e->getMessage()
                    ]);
                }
            }

            public function forceDelete({$module} \${$variable})
            {
                try {
                    if (\$this->{$variable}Repo->permanentlyDeleteModel(\${$variable})) {
                        return response()->json([
                            'status' => true,
                            'message' => module_message('permanently-deleted', \$this->singularLabel)
                        ]);
                    } else{
                        return response()->json([
                            'status' => true,
                            'error' => \$this->singularLabel.' not deleted try again.'
                        ]);
                    }
                } catch (Exception \$e) {
                    return response()->json([
                        'status' => false,
                        'error' => \$e->getMessage()
                    ]);
                }
            }

            public function bulkDelete()
            {
                try {
                    \$this->{$variable}Repo->bulkDelete();
                    return redirect()->route('back-office.{$pluralRoute}.index')->with('success', value: module_message('bulk-deleted', \$this->singularLabel));
                } catch (Exception \$e) {
                    return back()->withErrors(['error' => \$e->getMessage()]);
                }
            }

            public function bulkRestore()
            {
                try {
                    \$this->{$variable}Repo->bulkRestore();
                    return redirect()->route('back-office.{$pluralRoute}.index')->with('success', module_message('bulk-restored', \$this->singularLabel));
                } catch (Exception \$e) {
                    return back()->withErrors(['error' => \$e->getMessage()]);
                }
            }
        }
        PHP;

        File::put($path, $stub);
        $this->info("🧠 Controller created: {$path}");
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
                    $rule = ['required', 'string', 'max:255'];
                    break;
                case 'text':
                    $rule = ['nullable', 'string', 'max:255'];
                    break;
                case 'integer':
                case 'int':
                    $rule = ['nullable', 'integer'];
                    break;
                case 'boolean':
                case 'bool':
                    $rule = ["nullable", "boolean"];
                    break;
                case 'decimal':
                case 'float':
                case 'double':
                case 'numeric':
                    $rule = ["nullable", "numeric"];
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $rule = ["nullable", "date"];
                    break;
                case 'uuid':
                    $rule = ["nullable", "uuid"];
                    break;
                default:
                    // fallback to string
                    $rule = ["nullable", "string", 'max:255'];
                    break;
            }

            $rulesLines[] = "'{$name}' => [" . implode(', ', array_map(fn($r) => "'$r'", $rule)) . "],";
        }

        $rulesBlock = implode("\n", $rulesLines);

        $path = "{$this->basePath}/{$module}/Http/Requests/{$module}Request.php";

        $stub = <<<PHP
    <?php

    namespace App\Modules\\{$module}\Http\Requests;

    use App\Models\Status;
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

        public function prepareForValidation()
        {   
            if (\$this->has('status_id')) {
                \$this->merge([
                    'status_id' => Status::where('uuid', \$this->input('status_id'))->value('id')
                ]);
            }
        }
    }
    PHP;

        // ensure directory exists
        $dir = dirname(base_path($path));
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $this->files->put(base_path($path), $stub);
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
            // Add {$module}-specific methods here if needed
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

        // Save repository file
        File::put($path, $stub);

        // --- Bindings logic ---
        $bindingsFile = app_path('Modules/bindings.php');

        // Load existing bindings
        $bindings = file_exists($bindingsFile) ? require $bindingsFile : [];

        // Prepare fully-qualified class names
        $contract   = "App\\Modules\\{$module}\\Repositories\\Contracts\\{$module}Contract";
        $repository = "App\\Modules\\{$module}\\Repositories\\Eloquent\\{$module}Repository";

        // Add or update binding
        $bindings[$contract] = $repository;

        // Save back to bindings.php using ::class style formatting
        $content = "<?php\n\nreturn [\n";
        foreach ($bindings as $key => $value) {
            $content .= "    \\{$key}::class => \\$value::class,\n";
        }
        $content .= "];\n";

        file_put_contents($bindingsFile, $content);

        $this->info("📚 Repository created: {$path}");
        $this->info("🗂 Binding updated: {$contract} => {$repository}");
    }

    protected function createViews(string $module, array $fields = []): void
    {
        $pluralFolder = Str::kebab(Str::plural($module));
        $viewPath = resource_path("views/back-office/{$pluralFolder}");

        // Make directories
        if (!file_exists($viewPath)) mkdir($viewPath, 0755, true);

        // List of stubs
        $stubs = [
            'index.stub'          => "{$viewPath}/index.blade.php",
            'create_content.stub' => "{$viewPath}/create_content.blade.php",
            'edit_content.stub'   => "{$viewPath}/edit_content.blade.php",
            'show_content.stub'   => "{$viewPath}/show_content.blade.php",
        ];

        foreach ($stubs as $stub => $destination) {
            $content = file_get_contents(resource_path("views/stubs/{$stub}"));
            file_put_contents($destination, $content);
        }

        $this->info("Views created for module: {$module}");
    }

    protected function createRoute($module)
    {
        $singular = Str::lower($module);                 // e.g., country
        $plural   = Str::plural($singular); 
        $controller = "{$module}Controller";
        $routeFile = base_path("routes/back-office/{$singular}.php");

        $content = <<<PHP
        <?php

        use Illuminate\Support\Facades\Route;
        use App\Modules\\{$module}\Http\Controllers\\{$controller};

        // 🧩 {$module} Module Routes
        Route::group([
            'middleware' => ['web', 'auth']
        ], function () {
            Route::controller({$controller}::class)->group(function () {
                Route::post('bulk-delete', 'bulkDelete')->name('bulkDelete');
                Route::post('bulk-restore', 'bulkRestore')->name('bulkRestore');
                Route::post('{{$singular}}/restore', 'restore')->name('restore');
                Route::delete('{{$singular}}/force-delete', 'forceDelete')->name('forceDelete');
            });

            // 🧱 Resource CRUD
            Route::resource('/', {$controller}::class)
                    ->parameters(['' => '{$singular}']);
        });
        PHP;

        File::put($routeFile, $content);

        $this->info("✅ Route file created: routes/{$singular}.php");
    }
}
