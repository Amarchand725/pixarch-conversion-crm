<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [    
            ['name' => 'Manual'],
            ['name' => 'Landing Page'],
            ['name' => 'Facebook'],
        ];

        // Insert the sources into the database
        foreach ($sources as $source) {
            $model = Source::firstOrNew($source);
            $model->toFill($source);
            $model->save();
        }
    }
}