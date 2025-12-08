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
        $this->app->bind(\App\Modules\Lead\Repositories\Contracts\LeadContract::class, \App\Modules\Lead\Repositories\Eloquent\LeadRepository::class);
        $this->app->bind(\App\Modules\Team\Repositories\Contracts\TeamContract::class, \App\Modules\Team\Repositories\Eloquent\TeamRepository::class);
        $this->app->bind(\App\Modules\LeadCapture\Repositories\Contracts\LeadCaptureContract::class, \App\Modules\LeadCapture\Repositories\Eloquent\LeadCaptureRepository::class);
        $this->app->bind(\App\Modules\Campaign\Repositories\Contracts\CampaignContract::class, \App\Modules\Campaign\Repositories\Eloquent\CampaignRepository::class);
        $this->app->bind(\App\Modules\BusinessSetting\Repositories\Contracts\BusinessSettingContract::class, \App\Modules\BusinessSetting\Repositories\Eloquent\BusinessSettingRepository::class);
        $this->app->bind(\App\Modules\Role\Repositories\Contracts\RoleContract::class, \App\Modules\Role\Repositories\Eloquent\RoleRepository::class);
        $this->app->bind(\App\Modules\User\Repositories\Contracts\UserContract::class, \App\Modules\User\Repositories\Eloquent\UserRepository::class);
        $this->app->bind(\App\Modules\Faq\Repositories\Contracts\FaqContract::class, \App\Modules\Faq\Repositories\Eloquent\FaqRepository::class);
        $this->app->bind(\App\Modules\Meeting\Repositories\Contracts\MeetingContract::class, \App\Modules\Meeting\Repositories\Eloquent\MeetingRepository::class);
        $this->app->bind(\App\Modules\ActivityLog\Repositories\Contracts\ActivityLogContract::class, \App\Modules\ActivityLog\Repositories\Eloquent\ActivityLogRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}