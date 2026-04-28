<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Auth::loginUsingId(1);

        $this->call([
            SourceSeeder::class,
            CurrencySeeder::class,
            CountrySeeder::class,
            RolePermissionSeeder::class,
            StatusSeeder::class,
            UserSeeder::class,
            // CampaignSeeder::class,
            // LeadCaptureSeeder::class,
            // LeadSeeder::class,
            // FaqSeeder::class,
        ]);
    }
}
