<?php

namespace App\Traits;

use App\Services\LicenseService;

trait TrialProtection
{
    public function checkTrialLimit(int $currentCount, int $limit)
    {
        if (!LicenseService::isActivated() && $currentCount >= $limit) {
            abort(403, 'Trial limit reached. Please activate CRM.');
        }
    }

    public function checkTrialExpired()
    {
        if (!LicenseService::canWrite()) {
            abort(403, 'Trial period expired. Please contact vendor for activation.');
        }
    }
}