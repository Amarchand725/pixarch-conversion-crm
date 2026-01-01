<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Jobs\ProcessFacebookLead;

class ImportFacebookLeads extends Command
{
    protected $signature = 'facebook:import-old-leads';
    protected $description = 'Import all old leads from all forms of a Facebook Page';

    public function handle()
    {
        $pageId = config('services.facebook.page_id');
        $pageAccessToken = config('services.facebook.page_token');

        // 1️⃣ Get all forms for the page
        $formsResponse = Http::get("https://graph.facebook.com/v19.0/{$pageId}/forms", [
            'access_token' => $pageAccessToken,
        ]);

        if (!$formsResponse->successful()) {
            $this->error("Failed to fetch forms: " . $formsResponse->body());
            return 1;
        }

        $forms = $formsResponse->json()['data'] ?? [];
        $this->info("Found " . count($forms) . " forms on page {$pageId}");

        foreach ($forms as $form) {
            $formId = $form['id'];
            $this->info("Fetching leads for form {$formId}...");

            // 2️⃣ Get leads for this form
            $leadsResponse = Http::get("https://graph.facebook.com/v19.0/{$formId}/leads", [
                'access_token' => $pageAccessToken,
                'fields' => 'field_data,created_time'
            ]);

            if (!$leadsResponse->successful()) {
                $this->warn("Failed to fetch leads for form {$formId}");
                continue;
            }

            $leads = $leadsResponse->json()['data'] ?? [];
            $this->info("Found " . count($leads) . " leads for form {$formId}");

            // 3️⃣ Dispatch job for each lead
            foreach ($leads as $lead) {
                ProcessFacebookLead::dispatch($lead['id'], $formId, $pageId);
            }
        }

        $this->info("All leads dispatched for processing!");
    }
}
