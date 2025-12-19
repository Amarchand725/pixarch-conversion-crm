<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $bindingsFile = app_path('Modules/bindings.php');
        
        if (file_exists($bindingsFile)) {
            $bindings = require $bindingsFile;
            
            foreach ($bindings as $contract => $repository) {
                $this->app->bind($contract, $repository);
            }
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}