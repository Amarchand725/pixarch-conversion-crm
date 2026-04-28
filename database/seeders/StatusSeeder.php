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

            //Lead
            ['model' => 'Lead', 'name' => 'created'],
            ['model' => 'Lead', 'name' => 'assigned'],
            ['model' => 'Lead', 'name' => 'no contact'],
            ['model' => 'Lead', 'name' => 'contact established'],
            ['model' => 'Lead', 'name' => 'junk'],
            ['model' => 'Lead', 'name' => 'potential'],
            ['model' => 'Lead', 'name' => 'follow up'],
            ['model' => 'Lead', 'name' => 'hot client'],
            ['model' => 'Lead', 'name' => 'sale closed'],
            ['model' => 'Lead', 'name' => 'pool'],
            ['model' => 'Lead', 'name' => 'trashed'],

            //Meeting
            ['model' => 'Meeting', 'name' => 'Upcoming'],
            ['model' => 'Meeting', 'name' => 'Missed'],
            ['model' => 'Meeting', 'name' => 'Rescheduled'],
            ['model' => 'Meeting', 'name' => 'Completed'],

            //Faq
            ['model' => 'Faq', 'name' => 'active'],
            ['model' => 'Faq', 'name' => 'de-active'],
        ];
        
        foreach ($data as $item) {
            $status = Status::firstOrNew($item);
            $status->toFill($item);
            $status->save();
        }
    }
}
