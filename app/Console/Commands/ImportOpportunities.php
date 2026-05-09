<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Opportunity;

class ImportOpportunities extends Command
{
    // php artisan import:opportunities storage/app/100_KEYS_final_fixed.csv
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

            $phone = $row['phone'] ?? '';
            if (is_numeric($phone) && str_contains((string)$phone, 'E')) {
                $phone = (string) sprintf('%.0f', $phone);
            }
            
            Opportunity::create([
                'opportunity_name' => $row['opportunity name'],
                'contact_name' => $row['contact name'],
                'phone' => $phone,
                'email' => $row['email'],
                // 'pipeline' => $row['pipeline'] ?? 'Paid Social', // default to 'Paid Social' if pipeline is missing
                'pipeline' => 'Paid Social', // default to 'Paid Social' if pipeline is missing
                'stage' => $row['stage'],
                'lead_value' => $row['lead value'],
                // 'source' => $row['source'] ?? null,
                'source' => 'Facebook',
                'assigned' => $row['assigned'],
                'created_on' => $row['created on'],
                'updated_on' => $row['updated on'],
                // 'lost_reason_id' => $row['lost reason id'] ?? null,
                'lost_reason_id' => null,
                // 'lost_reason_name' => $row['lost reason name'] ?? null,
                'lost_reason_name' => null,
                // 'followers' => $row['followers'] ?? '',
                'followers' => null,
                'notes' => $row['notes'],
                'tags' => $row['tags'],
                // 'engagement_score' => $row['engagement score'] ?? null,
                'engagement_score' => null,
                // 'status' => $row['status'] ?? 'open',
                'status' => 'open',
                // 'last_updates_on' => $row['last updates on'],
                'last_updates_on' => null,
                // 'opportunity_id' => $row['opportunity id'] ?? null,
                'opportunity_id' => null,
                // 'contact_id' => $row['contact id'] ?? null,
                'contact_id' => null,
                // 'pipeline_stage_id' => $row['pipeline stage id'] ?? null,
                'pipeline_stage_id' => null,
                // 'pipeline_id' => $row['pipeline id'] ?? null,
                'pipeline_id' => null,
                'days_since_last_stage_change' => $row['days since last stage change date '] ?? null,
                'days_since_last_status_change' => $row['days since last status change date '] ?? null,
                'days_since_last_updated' => $row['days since last updated '] ?? null,
            ]);
        }

        fclose($handle);

        $this->info('Import completed!');
        return 0;
    }
}
