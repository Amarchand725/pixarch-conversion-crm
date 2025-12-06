<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ExportContact;
use Carbon\Carbon;

class ImportContacts extends Command
{
    protected $signature = 'import:contacts {file : Path to the CSV file}';
    protected $description = 'Import contacts from a CSV file into the export_contacts table';

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
            
            ExportContact::create([
                'first_name' => $row['first name'] ?? null,
                'last_name' => $row['last name'] ?? null,
                'full_name' => $row['name'] ?? null,
                'phone' => $row['phone'] ?? null,
                'email' => $row['email'] ?? null,
                'created' => isset($row['created']) ? Carbon::parse($row['created']) : null,
                'last_activity_at' => isset($row['last activity']) ? Carbon::parse($row['last activity']) : null,
                'tags' => $row['tags'] ?? null,
            ]);
        }

        fclose($handle);

        $this->info('Import completed!');
        return 0;
    }
}