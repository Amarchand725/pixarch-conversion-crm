<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // php artisan db:seed --class=SystemSettingsSeeder

        SystemSetting::create([
            'installed_at' => now(),
            'trial_expires_at' => now()->addDays(20), // 7-day trial
            'license_active' => false,
            'trial_lead_limit' => 500, // optional  
        ]);
    }
}
