<?php

namespace App\Services;

use App\Models\SystemSetting;
use Carbon\Carbon;

class LicenseService
{
    public static function settings()
    {
        return SystemSetting::first();
    }

    public static function isActivated(): bool
    {
        $s = self::settings();
        return $s && $s->license_active;
    }

    public static function isTrialActive(): bool
    {
        $s = self::settings();
        if (!$s) return false;
        return !$s->license_active && now()->lte($s->trial_expires_at);
    }

    public static function canWrite(): bool
    {
        return self::isActivated() || self::isTrialActive();
    }

    public static function trialDaysLeft(): int
    {
        $s = self::settings();
        if (!$s) return 0;
        $diff = now()->diffInDays($s->trial_expires_at, false);
        return max($diff, 0);
    }

    public static function trialLeadLimit(): int
    {
        $s = self::settings();
        return $s ? $s->trial_lead_limit : 0;
    }
}