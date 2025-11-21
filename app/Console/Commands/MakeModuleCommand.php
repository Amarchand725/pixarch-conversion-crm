<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
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
        $this->createConfig($module, $fields);
        $this->createSeeder($module);
        $this->registerPermissions($module);

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
            "{$this->basePath}/{$module}/Http/Controllers",
            "{$this->basePath}/{$module}/Http/Requests",
            "{$this->basePath}/{$module}/Models",
            "{$this->basePath}/{$module}/Repositories/Contracts",
            "{$this->basePath}/{$module}/Repositories/Eloquent",
            "{$this->basePath}/{$module}/resources/views",
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

        use App\Http\Controllers\Controller;
        use App\Modules\\{$module}\Repositories\Eloquent\\{$module}Repository;
        use App\Modules\\{$module}\Http\Requests\\{$module}Request;
        use App\Modules\\{$module}\Models\\{$module};
        use Exception;

        class {$module}Controller extends Controller
        {
            protected \${$variable}Repo;

            public function __construct({$module}Repository \${$variable}Repo)
            {
                \$this->{$variable}Repo = \${$variable}Repo;
            }

            public function index()
            {
                \${$pluralVariable} = \$this->{$variable}Repo->getAll();
                return view(strtolower('{$pluralRoute}.index'), compact('{$pluralVariable}'));
            }

            public function create()
            {
                return view('{$pluralRoute}.create');
            }

            public function store({$module}Request \$request)
            {
                \$payload = \$request->validated();
                try {
                    \$this->{$variable}Repo->storeModel(\$payload);
                    return redirect()->route(strtolower('{$module}.index'))->with('success', '{$module} created successfully.');
                } catch (Exception \$e) {
                    return back()->withErrors(['error' => \$e->getMessage()]);
                }
            }

            public function edit(\$id)
            {
                \${$variable} = \$this->{$variable}Repo->showModel(\$id);
                return view('{$pluralRoute}.edit', compact('{$variable}'));
            }

            public function update({$module}Request \$request, {$module} \${$variable})
            {
                \$payload = \$request->validated();
                try {
                    \$this->{$variable}Repo->updateModel(\${$variable}, \$payload);
                    return redirect()->route(strtolower('{$module}.index'))->with('success', '{$module} updated successfully.');
                } catch (Exception \$e) {
                    return back()->withErrors(['error' => \$e->getMessage()]);
                }
            }

            public function show(\$id)
            {
                \${$variable} = \$this->{$variable}Repo->showModel(\$id);
                return view('{$pluralRoute}.show', compact('{$variable}'));
            }

            public function destroy(\$id)
            {
                try {
                    \$this->{$variable}Repo->softDeleteModel(\$id);
                    return redirect()->route(strtolower('{$pluralRoute}.index'))->with('success', '{$module} deleted successfully.');
                } catch (Exception \$e) {
                    return back()->withErrors(['error' => \$e->getMessage()]);
                }
            }

            public function restore(\$id)
            {
                try {
                    \$this->{$variable}Repo->restoreModel(\$id);
                    return redirect()->route(strtolower('{$pluralRoute}.index'))->with('success', '{$module} restored successfully.');
                } catch (Exception \$e) {
                    return back()->withErrors(['error' => \$e->getMessage()]);
                }
            }

            public function forceDelete(\$id)
            {
                try {
                    \$this->{$variable}Repo->permanentlyDeleteModel(\$id);
                    return redirect()->route(strtolower('{$pluralRoute}.index'))->with('success', '{$module} permanently deleted.');
                } catch (Exception \$e) {
                    return back()->withErrors(['error' => \$e->getMessage()]);
                }
            }

            public function bulkDelete()
            {
                try {
                    \$this->{$variable}Repo->bulkDelete();
                    return redirect()->route(strtolower('{$pluralRoute}.index'))->with('success', 'Bulk delete successful.');
                } catch (Exception \$e) {
                    return back()->withErrors(['error' => \$e->getMessage()]);
                }
            }

            public function bulkRestore()
            {
                try {
                    \$this->{$variable}Repo->bulkRestore();
                    return redirect()->route(strtolower('{$pluralRoute}.index'))->with('success', 'Bulk restore successful.');
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

        File::put($path, $stub);
        $this->info("📚 Repository created: {$path}");
    }

    protected function createViews(string $module, array $fields = [])
    {
        // folder name: lowercase module (Country -> country)
        $pluralFolder = Str::plural(strtolower($module));
        $viewPath = resource_path("views/{$pluralFolder}");

        if (!file_exists($viewPath)) {
            mkdir($viewPath, 0755, true);
        }

        // variable names
        $singular = lcfirst($module);              // Country -> country
        $plural = Str::plural($singular);          // country -> countries

        // common header/footer using x-app-layout
        $header = "<x-app-layout>\n    <x-slot name=\"header\">\n        <h2 class=\"font-semibold text-xl text-gray-800 leading-tight\">{{ __('" . ucfirst($module) . "') }}</h2>\n    </x-slot>\n\n    <div class=\"py-12\">\n        <div class=\"max-w-7xl mx-auto sm:px-6 lg:px-8\">\n            <div class=\"bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900\">\n";
        $footer = "            </div>\n        </div>\n    </div>\n</x-app-layout>";

        // INDEX
        $index = <<<BLADE
    {$header}
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-bold">All {$module}</h1>
                        <a href="{{ route('{$plural}.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add New</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">#</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Name</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach(\${$plural} ?? [] as \${$singular})
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ \${$singular}->id }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ \${$singular}->name ?? '-' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ \${$singular}->status ?? '-' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            <a href="{{ route('{$plural}.edit', \${$singular}->id) }}" class="text-blue-600">Edit</a>
                                            <a href="{{ route('{$plural}.show', \${$singular}->id) }}" class="ml-2 text-green-600">View</a>
                                            <form action="{{ route('{$plural}.destroy', \${$singular}->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="ml-2 text-red-600">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
    {$footer}
    BLADE;

        // CREATE
        // (You can expand fields dynamically — here we show a simple name + status)
        $create = <<<BLADE
    {$header}
                    <h1 class="text-2xl font-bold mb-4">Create {$module}</h1>

                    <form action="{{ route('{$plural}.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-gray-700">Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full">
                            @error('name') <span class="text-red-600 text-sm">{{ \$message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700">Status</label>
                            <select name="status" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('status') <span class="text-red-600 text-sm">{{ \$message }}</span> @enderror
                        </div>

                        <button class="bg-green-600 text-white px-4 py-2 rounded">Save</button>
                    </form>
    {$footer}
    BLADE;

        // EDIT
        $edit = <<<BLADE
    {$header}
                    <h1 class="text-2xl font-bold mb-4">Edit {$module}</h1>

                    <form action="{{ route('{$plural}.update', \${$singular}->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-gray-700">Name</label>
                            <input type="text" name="name" value="{{ old('name', \${$singular}->name) }}" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full">
                            @error('name') <span class="text-red-600 text-sm">{{ \$message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700">Status</label>
                            <select name="status" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full">
                                <option value="active" {{ (old('status', \${$singular}->status) === 'active') ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ (old('status', \${$singular}->status) === 'inactive') ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <span class="text-red-600 text-sm">{{ \$message }}</span> @enderror
                        </div>

                        <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    </form>
    {$footer}
    BLADE;

        // SHOW
        $show = <<<BLADE
    {$header}
                    <h1 class="text-2xl font-bold mb-4">View {$module}</h1>

                    <div class="mb-4">
                        <strong>ID:</strong> {{ \${$singular}->id }}
                    </div>

                    <div class="mb-4">
                        <strong>Name:</strong> {{ \${$singular}->name ?? '-' }}
                    </div>

                    <div class="mb-4">
                        <strong>Status:</strong> {{ \${$singular}->status ?? '-' }}
                    </div>

                    <a href="{{ route('{$plural}.edit', \${$singular}->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded">Edit</a>
    {$footer}
    BLADE;

        // write files
        file_put_contents("{$viewPath}/index.blade.php", $index);
        file_put_contents("{$viewPath}/create.blade.php", $create);
        file_put_contents("{$viewPath}/edit.blade.php", $edit);
        file_put_contents("{$viewPath}/show.blade.php", $show);
    }

    protected function createRoute($module)
    {
        $singular = Str::lower($module);                 // e.g., country
        $plural   = Str::plural($singular); 
        $controller = "{$module}Controller";
        $routeFile = base_path("routes/{$singular}.php");

        $content = <<<PHP
        <?php

        use Illuminate\Support\Facades\Route;
        use App\Modules\\{$module}\Http\Controllers\\{$controller};

        // 🧩 {$module} Module Routes
        Route::middleware(['web', 'auth'])
            ->prefix('{$plural}')
            ->name('{$plural}.')
            ->group(function () {

                // 🧱 Resource CRUD
                Route::resource('/', {$controller}::class)
                    ->parameters(['' => '{$singular}']);

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

        $this->info("✅ Route file created: routes/{$singular}.php");
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
