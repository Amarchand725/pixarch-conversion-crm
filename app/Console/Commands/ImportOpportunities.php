<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Opportunity;

class ImportOpportunities extends Command
{
    protected $signature = 'import:opportunities {file}';
    protected $description = 'Import opportunities from a CSV file';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $handle = fopen($file, 'r');

        // Read header row
        $headers = fgetcsv($handle);

        while (($data = fgetcsv($handle)) !== false) {
            $row = array_combine($headers, $data);
            $row = array_change_key_case($row, CASE_LOWER); // make all keys lowercase
            $row = array_map('trim', $row); // trim spaces from keys
            
            Opportunity::create([
                'opportunity_name' => $row['opportunity name'],
                'contact_name' => $row['contact name'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'pipeline' => $row['pipeline'],
                'stage' => $row['stage'],
                'lead_value' => $row['lead value'],
                'source' => $row['source'],
                'assigned' => $row['assigned'],
                'created_on' => $row['created on'],
                'updated_on' => $row['updated on'],
                'lost_reason_id' => $row['lost reason id'],
                'lost_reason_name' => $row['lost reason name'],
                'followers' => $row['followers'],
                'notes' => $row['notes'],
                'tags' => $row['tags'],
                'engagement_score' => $row['engagement score'],
                'status' => $row['status'],
                'last_updates_on' => $row['last updates on'],
                'opportunity_id' => $row['opportunity id'],
                'contact_id' => $row['contact id'],
                'pipeline_stage_id' => $row['pipeline stage id'],
                'pipeline_id' => $row['pipeline id'],
                'days_since_last_stage_change' => $row['days since last stage change date '],
                'days_since_last_status_change' => $row['days since last status change date '],
                'days_since_last_updated' => $row['days since last updated '],
            ]);
        }

        fclose($handle);

        $this->info('Import completed!');
        return 0;
    }
}
