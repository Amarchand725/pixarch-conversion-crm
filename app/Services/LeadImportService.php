<?php

namespace App\Services;

use App\Models\Source;
use App\Modules\Lead\Models\Lead;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

class LeadImportService
{
    protected int $chunkSize = 200;

    public function importFromCsv(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }

        set_time_limit(0);

        $handle = fopen($filePath, 'r');

        $headers = fgetcsv($handle);
        $headers = array_map(function ($header) {
            return str_replace(' ', '_', strtolower(trim($header)));
        }, $headers);

        $totalInserted = 0;
        $duplicates = 0;

        // Email hash map (O(1) lookup)
        $existingEmails = Lead::pluck('email')
            ->mapWithKeys(fn ($e) => [strtolower($e) => true])
            ->toArray();
    
        // Cache lookups
        $sources = Source::pluck('id', 'name')->toArray();
        $statuses = Status::where('model', 'Lead')
            ->pluck('id', 'name')
            ->toArray();

        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            $rows[] = array_map('trim', array_combine($headers, $data));

            if (count($rows) >= $this->chunkSize) {
                $this->processChunk(
                    $rows,
                    $sources,
                    $statuses,
                    $existingEmails,
                    $totalInserted,
                    $duplicates
                );
                $rows = [];
            }
        }
        
        // Process remaining rows
        if (!empty($rows)) {
            $this->processChunk(
                $rows,
                $sources,
                $statuses,
                $existingEmails,
                $totalInserted,
                $duplicates
            );
        }

        fclose($handle);

        return [
            'inserted' => $totalInserted,
            'duplicates' => $duplicates,
        ];
    }

    protected function processChunk(
        array $rows,
        array $sources,
        array $statuses,
        array &$existingEmails,
        int &$totalInserted,
        int &$duplicates
    ): void {
        DB::transaction(function () use (
            $rows,
            $sources,
            $statuses,
            &$existingEmails,
            &$totalInserted,
            &$duplicates
        ) {
            foreach ($rows as $row) {
                $email = strtolower($row['email'] ?? '');

                if (!$email || isset($existingEmails[$email])) {
                    $duplicates++;
                    continue;
                }

                if(empty($sources[$row['source']])) {
                    $row['source'] = Source::firstOrCreate(['name' => $row['source'] ?? 'Unknown'])->id;
                }else{
                    $row['source'] = $sources[$row['source']] ?? null;
                }

                $lead = Lead::create([
                    'source_id' => $row['source'],
                    'name' => $row['name']
                        ?? $row['opportunity_name']
                        ?? 'N/A',
                    'phone' => $row['phone'] ?? null,
                    'email' => $email,
                    'budget' => $row['lead_value'] ?? null,
                    'pipeline' => $row['pipeline'] ?? null,
                    'status' => $row['status'] ?? null,
                    'created_at' => $row['created_on'] ?? now(),
                    'updated_at' => $row['updated_on'] ?? now(),
                ]);

                if (!empty($row['stage'])) {
                    $status_id = Status::where('model', 'Lead')->where('name', $row['stage'])->value('id'); 
                    $lead->statusLogs()->create([
                        'assignee_id' => auth()->id(),
                        'status_id' => $status_id ?? null,
                        'amount' => $row['lead_value'] ?? 0,
                        'description' => $row['notes'] ?? null,
                        'model_id' => $lead->id,
                        'model_type' => $lead->getMorphClass(),
                    ]);
                }

                $lead->assignees()->sync([auth()->id()]);

                $existingEmails[$email] = true;
                $totalInserted++;
            }
        });
    }
}
