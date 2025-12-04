<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

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
                Route::as('users.')->prefix('users')->group(base_path('routes/back-office/user.php'));
                Route::as('campaigns.')->prefix('campaigns')->group(base_path('routes/back-office/campaign.php'));
                Route::as('lead-captures.')->prefix('lead-captures')->group(base_path('routes/back-office/lead-capture.php'));
                Route::as('leads.')->prefix('leads')->group(base_path('routes/back-office/lead.php'));
                Route::as('roles.')->prefix('roles')->group(base_path('routes/back-office/role.php'));
                Route::as('auth.')->prefix('auth')->group(base_path('routes/back-office/auth.php'));
            });
    }
}