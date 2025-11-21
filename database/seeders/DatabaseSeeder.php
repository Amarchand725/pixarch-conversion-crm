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
            UserSeeder::class,
            StatusSeeder::class,
            LeadCaptureSeeder::class,
            CampaignSeeder::class,
        ]);
    }
}
