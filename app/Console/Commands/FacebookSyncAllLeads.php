<?php

namespace App\Console\Commands;

use App\Jobs\ProcessFacebookLead;
use App\Models\FacebookLeadMeta;
use App\Modules\Lead\Repositories\Contracts\LeadContract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookSyncAllLeads extends Command
{
    protected $signature = 'facebook:sync-all-leads';
    protected $description = 'Sync all Facebook leads from all forms';

    public function handle()
    {
        $token  = config('services.facebook.page_token');
        $pageId = config('services.facebook.page_id');

        // 1. Get all forms
        $formsResponse = Http::get(
            "https://graph.facebook.com/v17.0/{$pageId}/leadgen_forms",
            ['access_token' => $token]
        );
        
        $forms = $formsResponse->json('data') ?? [];
        
        foreach ($forms as $form) {
            $this->info("Syncing Form: {$form['name']}");

            $url = "https://graph.facebook.com/v17.0/{$form['id']}/leads";
            
            do {
                $response = Http::get($url, [
                    'access_token' => $token,
                    'limit' => 100,
                ]);

                $data = $response->json();
                
                $counter = 1;
                foreach ($data['data'] ?? [] as $lead) {
                    Log::info("Lead Data: ".$counter++);  
                    
                    if (!FacebookLeadMeta::where('leadgen_id', $lead['id'])->exists()) {
                        Log::info("Condition true");
                        
                        ProcessFacebookLead::dispatch(
                            app(LeadContract::class),
                            $lead['id'],
                            $form['id'],
                            $pageId
                        );
                    }
                }

                $url = $data['paging']['next'] ?? null;

            } while ($url);
        }

        $this->info('All leads synced successfully.');
    }
}