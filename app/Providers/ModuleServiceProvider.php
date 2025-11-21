<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ModuleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $routePath = base_path('routes');
        if (File::exists($routePath)) {
            foreach (File::files($routePath) as $file) {
                // load all *.php files inside routes/
                if ($file->getExtension() === 'php') {
                    $this->loadRoutesFrom($file->getPathname());
                }
            }
        }
    }
}
