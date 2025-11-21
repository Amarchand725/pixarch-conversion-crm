<?php

namespace App\Providers;

use App\Models\Attachment;
use App\Models\Country;
use App\Models\EntityRelationship;
use App\Models\LogEntityStatus;
use App\Models\Meeting;
use App\Models\OtpToken;
use App\Models\Permission;
use App\Models\Role;
use App\Models\State;
use App\Models\Status;
use App\Models\User;
use App\Modules\Lead\Models\Lead;
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
            "LogEntityStatus"  => LogEntityStatus::class,
            "EntityRelationship"  => EntityRelationship::class,
        ]);
    }
}