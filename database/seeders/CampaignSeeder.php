<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Campaign\Models\Campaign;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        Campaign::factory()->count(5)->create();
    }
}