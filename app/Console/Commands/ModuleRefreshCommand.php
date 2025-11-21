<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleRefreshCommand extends Command
{
    protected $signature = 'module:refresh {name}';
    protected $description = 'Rebuild a module from existing configuration';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $configPath = config_path("modules/{$name}.php");

        if (!File::exists($configPath)) {
            $this->error("❌ No config found for {$name} module.");
            return;
        }

        $config = include $configPath;
        $fields = $config['fields'] ?? [];

        $this->info("♻️ Rebuilding {$name} module...");

        $this->callSilent('make:module', [
            'name' => $name,
            '--fields' => implode(',', array_map(
                fn($f, $t) => "{$f}:{$t}",
                array_keys($fields),
                $fields
            )),
            '--force' => true,
        ]);

        $this->info("✅ {$name} module refreshed successfully.");
    }
}
