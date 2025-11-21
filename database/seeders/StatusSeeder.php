<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            //User
            ['model' => 'User', 'name' => 'active'],
            ['model' => 'User', 'name' => 'de-active'],

            //Campaign
            ['model' => 'Campaign', 'name' => 'active'],
            ['model' => 'Campaign', 'name' => 'de-active'],

            //LeadCapture
            ['model' => 'LeadCapture', 'name' => 'active'],
            ['model' => 'LeadCapture', 'name' => 'de-active'],
        ];
        
        foreach ($data as $item) {
            $status = Status::firstOrNew($item);
            $status->toFill($item);
            $status->save();
        }
    }
}
