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
            ['name' => 'tiktok'],
            ['name' => 'facebook ads'],
            ['name' => 'twitter'],
            ['name' => 'linkedin ads'],
            ['name' => 'unknown'],
            ['name' => 'seo'],
            ['name' => 'bark'],
            ['name' => 'google ads'],
            ['name' => 'Thumbtack'],
            ['name' => 'Email Marketing'],
            ['name' => 'Facebook'],
            ['name' => 'bing'],
            ['name' => 'OTHER'],
            ['name' => 'PPC'],
            ['name' => 'direct'],
            ['name' => 'linkedin'],
            ['name' => 'website'],
        ];

        // Insert the sources into the database
        foreach ($sources as $source) {
            $model = Source::firstOrNew($source);
            $model->toFill($source);
            $model->save();
        }
    }
}