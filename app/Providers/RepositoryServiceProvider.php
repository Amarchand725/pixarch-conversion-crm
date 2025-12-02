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
        // Individual repository bindings generated automatically
        // $this->app->bind(\App\Repositories\Contracts\AttachmentRepositoryContract::class, \App\Repositories\Eloquents\AttachmentRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}