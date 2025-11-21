<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Lead\Models\Lead;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        Lead::factory()->count(5)->create();
    }
}