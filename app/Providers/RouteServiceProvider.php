<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->group(function () {
                $this->mapBackOfficeRoutes();
            });
    }

    /**
     * Map back office routes - Individual route registrations generated automatically
     */
    protected function mapBackOfficeRoutes(): void
    {
        Route::as('back-office.')
            ->prefix('back-office')
            ->group(function () {
                Route::as('auth.')->prefix('auth')->group(base_path('routes/back-office/auth.php'));
                $routeFiles = glob(base_path('routes/back-office/*.php'));

                foreach ($routeFiles as $file) {
                    $fileName = pathinfo($file, PATHINFO_FILENAME); // e.g., 'activity-log'
                    
                    // Convert to plural kebab-case for URL prefix
                    $prefix = Str::kebab(Str::plural($fileName)); // 'activity-logs'
                    
                    // Use plural prefix as route URL, but keep file path as is
                    Route::as($prefix . '.')
                        ->prefix($prefix)
                        ->group($file);
                }
            });
    }
}