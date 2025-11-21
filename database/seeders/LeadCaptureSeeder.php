<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\LeadCapture\Models\LeadCapture;

class LeadCaptureSeeder extends Seeder
{
    public function run(): void
    {
        LeadCapture::factory()->count(5)->create();
    }
}