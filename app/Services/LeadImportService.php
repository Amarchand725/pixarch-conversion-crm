<?php

namespace App\Services;

use App\Models\Source;
use App\Modules\Lead\Models\Lead;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

class LeadImportService
{
    protected int $chunkSize = 500;

    /**
     * Import leads from CSV file
     *
     * @param string $filePath
     * @return array
     */
    public function importFromCsv(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: $filePath");
        }

        $handle = fopen($filePath, 'r');

        // Read headers
        $headers = fgetcsv($handle);
        $headers = array_map('strtolower', $headers);

        $totalInserted = 0;
        $duplicates = 0;

        // Preload existing emails to avoid duplicates
        $existingEmails = DB::table('leads')->pluck('email')->map(fn($e) => strtolower($e))->toArray();

        $rows = [];
        while (($data = fgetcsv($handle)) !== false) {
            $row = array_combine($headers, $data);
            $rows[] = array_map('trim', $row);
        }
        fclose($handle);

        // Process in chunks
        $chunks = array_chunk($rows, $this->chunkSize);

        foreach ($chunks as $chunk) {
            DB::transaction(function () use ($chunk, &$existingEmails, &$totalInserted, &$duplicates) {
                foreach ($chunk as $row) {
                    $email = strtolower($row['email'] ?? '');

                    // Skip duplicates
                    if (empty($email) || in_array($email, $existingEmails)) {
                        $duplicates++;
                        continue;
                    }

                    // Source mapping
                    $source = isset($row['source']) ? Source::where('name', $row['source'])->first() : null;

                    // Create Lead
                    $lead = Lead::create([
                        'source_id' => $source->id ?? null,
                        'name' => $row['name'] ?? $row['opportunity_name'] ?? null,
                        'phone' => $row['phone'] ?? null,
                        'email' => $email,
                        'value' => $row['lead_value'] ?? null,
                        'pipeline' => $row['pipeline'] ?? null,
                        'status' => $row['status'] ?? null,
                        'created_at' => $row['created_on'] ?? now(),
                        'updated_at' => $row['updated_on'] ?? now(),
                    ]);

                    // Status log
                    if (isset($row['stage'])) {
                        $status = Status::where('model', 'Lead')
                            ->where('name', $row['stage'])
                            ->first();

                        $logData = [
                            'status_id' => $status->id ?? null,
                            'notes' => $row['notes'] ?? null,
                            'model_id' => $lead->id,
                            'model_type' => $lead->getMorphClass(),
                        ];

                        $log = $lead->statusLogs()->firstOrNew();
                        $log->toFill($logData);
                        $log->save();
                    }

                    // Add email to existing to avoid duplicates in this run
                    $existingEmails[] = $email;
                    $totalInserted++;
                }
            });
        }

        return [
            'inserted' => $totalInserted,
            'duplicates' => $duplicates,
        ];
    }
}
