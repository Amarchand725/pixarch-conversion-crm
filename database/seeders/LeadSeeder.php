<?php

namespace Database\Seeders;

use App\Models\LogEntityStatus;
use App\Models\Status;
use Illuminate\Database\Seeder;
use App\Modules\Lead\Models\Lead;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        // Predefined Lead Statuses
        $statusNames = [
            'created',
            'assigned',
            'no contacted',
            'contact established',
            'junk',
            'potential',
            'follow up',
            'hot client',
            'sales closed',
            'pool',
        ];

        // Fetch or create these statuses in DB
        $statuses = collect();
        foreach ($statusNames as $name) {
            $statuses->push(
                Status::firstOrCreate([
                    'model' => 'Lead',
                    'name'  => $name
                ])
            );
        }

        // Create 50 leads
        $leads = Lead::factory()->count(50)->create();

        foreach ($leads as $lead) {
            // Pick a random status for this lead
            $status = $statuses->random();

            $lead->assignees()->sync([rand(3, 10)]);

            LogEntityStatus::create([
                'author_id'   => 2, // admin
                'model_id'    => $lead->id,
                'model_type'  => $lead->getMorphClass(),
                'status_id'   => $status->id,
                'assignee_id' => $lead->currentAssignee?->user_id,
                'meeting_id'  => null,
                'description' => 'Auto generated status log',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}