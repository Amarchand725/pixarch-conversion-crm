<?php

namespace App\Providers;

use App\Models\{
    Attachment, Country, EntityRelationship, LogEntityStatus,
    OtpToken, Permission, Role, State, Status, User,
};
use App\Modules\Campaign\Models\Campaign;
use App\Modules\Faq\Models\Faq;
use App\Modules\Lead\Models\Lead;
use App\Modules\LeadCapture\Models\LeadCapture;
use App\Modules\Meeting\Models\Meeting;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MorphMapServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register individual model mappers for optimal performance
        Relation::morphMap([
            'Attachment' => Attachment::class,
            'Country' => Country::class,
            'OtpToken' => OtpToken::class,
            'Permission' => Permission::class,
            'Role' => Role::class,
            'State' => State::class,
            'Status' => Status::class,
            'User' => User::class,
            "Lead"  => Lead::class,
            "Meeting"  => Meeting::class,
            "Faq"  => Faq::class,
            "LeadCapture"  => LeadCapture::class,
            "Campaign"  => Campaign::class,
            "LogEntityStatus"  => LogEntityStatus::class,
            "EntityRelationship"  => EntityRelationship::class,
        ]);
    }
}