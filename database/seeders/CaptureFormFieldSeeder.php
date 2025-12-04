<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\LeadCapture\Models\CaptureFormField;

class CaptureFormFieldSeeder extends Seeder
{
    public function run(): void
    {
        CaptureFormField::factory()->count(5)->create();
    }
}