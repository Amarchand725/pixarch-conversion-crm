<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DeleteModuleCommand extends Command
{
    protected $signature = 'module:delete {name} {--force : Skip confirmation prompt} {--rollback : Rollback module migration before deleting}';
    protected $description = 'Delete an existing module and all related files (routes, migrations, config, seeders)';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $lower = Str::lower($name);
        $pluralFolder = Str::plural(strtolower($name));
        $modulePath     = base_path("app/Modules/{$name}");
        $routePath      = base_path("routes/{$lower}.php");
        $bladesPath = resource_path("views/{$pluralFolder}");
        $configPath     = base_path("config/{$lower}.php");
        $migrationGlob  = base_path("database/migrations/*_create_" . Str::snake(Str::pluralStudly($name)) . "_table.php");
        $seederPath     = base_path("database/seeders/{$name}Seeder.php");

        if (!$this->option('force')) {
            if (!$this->confirm("Are you sure you want to delete the '{$name}' module and all related files?")) {
                $this->warn('Operation cancelled.');
                return;
            }
        }

        // 🧩 1. Optionally rollback migration
        if ($this->option('rollback')) {
            $this->callSilent('migrate:rollback', [
                '--path' => 'database/migrations',
                '--step' => 1,
            ]);
            $this->info("🌀 Rolled back migration for module: {$name}");
        }

        // 🧩 2. Delete Module Folder
        if (File::exists($modulePath)) {
            File::deleteDirectory($modulePath);
            $this->info("✅ Module folder deleted: {$modulePath}");
        }

        // 🧩 3. Delete Route File
        if (File::exists($routePath)) {
            File::delete($routePath);
            $this->info("✅ Route file deleted: {$routePath}");
        }

        // 🧩 3. Delete blades
        if (File::exists($bladesPath)) {
            File::deleteDirectory($bladesPath);
            $this->info("✅ Blade folder deleted: {$bladesPath}");
        } else {
            $this->warn("⚠️ Blade folder not found: {$bladesPath}");
        }

        // 🧩 4. Delete Config File
        if (File::exists($configPath)) {
            File::delete($configPath);
            $this->info("✅ Config file deleted: {$configPath}");
        }

        // 🧩 5. Delete Migration Files
        $migrations = glob($migrationGlob);
        if (!empty($migrations)) {
            foreach ($migrations as $migration) {
                File::delete($migration);
                $this->info("✅ Migration deleted: {$migration}");
            }
        } else {
            $this->warn("⚠️ No migrations found for {$name}");
        }

        // 🧩 6. Delete Seeder File
        if (File::exists($seederPath)) {
            File::delete($seederPath);
            $this->info("✅ Seeder deleted: {$seederPath}");
        }

        // ✅ 5️⃣ Optional refresh (full refresh)
        if ($this->confirm('Do you want to refresh your database after deletion?', false)) {
            $this->info('Refreshing database...');
            Artisan::call('migrate:refresh');
            $this->info(Artisan::output());
        }

        $this->line("\n🎯 Module '{$name}' completely deleted.");
    }
}
