<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'installed_at',
        'trial_expires_at',
        'license_active',
        'trial_lead_limit',
    ];

    protected $casts = [
        'trial_expires_at' => 'datetime',
    ];

    protected $dates = ['installed_at', 'trial_expires_at'];
}
