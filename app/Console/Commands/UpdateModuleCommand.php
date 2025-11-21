<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UpdateModuleCommand extends Command
{
    protected $signature = 'module:update 
        {name : Module name} 
        {--fields= : Add or modify fields (comma separated)} 
        {--remove= : Fields to remove (comma separated)}';

    protected $description = 'Update an existing module with new or modified fields';

    protected string $basePath = 'app/Modules';

    public function handle(): void
    {
        $module = Str::studly($this->argument('name'));
        $fields = $this->parseFields($this->option('fields'));
        $remove = $this->parseRemove($this->option('remove'));

        $this->info("🔄 Updating module: {$module}");

        if (!File::exists(base_path("{$this->basePath}/{$module}"))) {
            $this->error("❌ Module '{$module}' does not exist.");
            return;
        }

        $this->updateModel($module, $fields, $remove);
        $this->updateRequest($module, $fields, $remove);
        $this->updateConfig($module, $fields, $remove);
        $this->createMigration($module, $fields, $remove);

        $this->info("✅ {$module} module updated successfully!");
    }

    /* -----------------------------------------------------------------
     |  Field Parsing Helpers
     | -----------------------------------------------------------------
     */

    protected function parseFields(?string $option): array
    {
        if (!$option) return [];

        return collect(explode(',', $option))
            ->map(function ($field) {
                [$name, $type] = array_pad(explode(':', $field), 2, 'string');
                return ['name' => trim($name), 'type' => trim($type)];
            })
            ->filter(fn($f) => $f['name'])
            ->values()
            ->toArray();
    }

    protected function parseRemove(?string $option): array
    {
        if (!$option) return [];
        return collect(explode(',', $option))
            ->map(fn($f) => trim($f))
            ->filter()
            ->toArray();
    }

    /* -----------------------------------------------------------------
     |  Update Model
     | -----------------------------------------------------------------
     */

    protected function updateModel(string $module, array $fields, array $remove): void
    {
        $modelPath = base_path("{$this->basePath}/{$module}/Models/{$module}.php");
        if (!File::exists($modelPath)) return;

        $content = File::get($modelPath);

        // extract fillable array
        preg_match('/protected \$fillable\s*=\s*\[([^\]]*)\];/s', $content, $matches);
        $fillable = isset($matches[1]) ? explode(',', $matches[1]) : [];

        $fillable = collect($fillable)
            ->map(fn($f) => trim(str_replace(['"', "'"], '', $f)))
            ->filter()
            ->merge(collect($fields)->pluck('name'))
            ->reject(fn($name) => in_array($name, $remove))
            ->unique()
            ->values()
            ->map(fn($f) => "'{$f}'")
            ->implode(', ');

        $content = preg_replace(
            '/protected \$fillable\s*=\s*\[[^\]]*\];/s',
            "protected \$fillable = [{$fillable}];",
            $content
        );

        File::put($modelPath, $content);
        $this->info('→ Updated model fillable fields');
    }

    /* -----------------------------------------------------------------
     |  Update Request
     | -----------------------------------------------------------------
     */

    protected function updateRequest(string $module, array $fields, array $remove): void
    {
        $path = base_path("{$this->basePath}/{$module}/Http/Requests/{$module}Request.php");
        if (!File::exists($path)) return;

        $rules = collect($fields)->map(function ($field) {
            return "            '{$field['name']}' => 'required',";
        })->implode("\n");

        $content = <<<PHP
<?php

namespace App\Modules\\{$module}\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class {$module}Request extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
{$rules}
        ];
    }
}
PHP;

        File::put($path, $content);
        $this->info('→ Updated form request rules');
    }

    /* -----------------------------------------------------------------
     |  Update Config
     | -----------------------------------------------------------------
     */

    protected function updateConfig(string $module, array $fields, array $remove): void
    {
        $configPath = config_path("modules/{$module}.php");
        if (!File::exists($configPath)) return;

        $existing = include $configPath;
        $newFields = collect($fields)->mapWithKeys(fn($f) => [$f['name'] => $f['type']]);
        $updated = collect($existing['fields'] ?? [])
            ->reject(fn($_, $key) => in_array($key, $remove))
            ->merge($newFields)
            ->toArray();

        $stub = <<<PHP
<?php

return [
    'name' => '{$module}',
    'fields' => [
PHP;

        foreach ($updated as $name => $type) {
            $stub .= "\n        '{$name}' => '{$type}',";
        }

        $stub .= "\n    ],\n];";

        File::put($configPath, $stub);
        $this->info('→ Updated module config file');
    }

    /* -----------------------------------------------------------------
     |  Migration
     | -----------------------------------------------------------------
     */

    protected function createMigration(string $module, array $fields, array $remove): void
    {
        if (empty($fields) && empty($remove)) return;

        $table = Str::snake(Str::pluralStudly($module));
        $timestamp = now()->format('Y_m_d_His');
        $migration = database_path("migrations/{$timestamp}_update_{$table}_table.php");

        $up = '';
        foreach ($fields as $field) {
            $type = $field['type'];
            $name = $field['name'];
            $up .= "            \$table->{$type}('{$name}')->nullable();\n";
        }

        foreach ($remove as $field) {
            $up .= "            \$table->dropColumn('{$field}');\n";
        }

        $content = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('{$table}', function (Blueprint \$table) {
{$up}        });
    }

    public function down(): void
    {
        Schema::table('{$table}', function (Blueprint \$table) {
            // rollback logic if needed
        });
    }
};
PHP;

        File::put($migration, $content);
        $this->info('→ Created update migration');
    }
}
