<?php

namespace App\Console\Commands;

use App\Modules\Lead\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Services\WhatsAppService;

class FetchFacebookLeads extends Command
{
    protected $signature = 'facebook:fetch-leads';
    protected $description = 'Fetch new leads from Facebook and send WhatsApp messages';

    // protected $whatsapp;

    public function __construct()
    {
        parent::__construct();
        // $this->whatsapp = new WhatsAppService();
    }

    public function handle()
    {
        $accessToken = env('FB_PAGE_ACCESS_TOKEN');
        $formId = env('FB_LEAD_FORM_ID');

        $this->info("Fetching leads for form ID: {$formId}");

        $url = "https://graph.facebook.com/v17.0/{$formId}/leads?access_token={$accessToken}";
        $response = Http::get($url);
        // dd($response->json());
        if (!$response->successful()) {
            $this->error('Failed to fetch leads from Facebook');
            return 1;
        }

        $leads = $response->json()['data'] ?? [];

        if (empty($leads)) {
            $this->info('No new leads found.');
            return 0;
        }
        
        foreach ($leads as $leadData) {
            $savedLead = Lead::updateOrCreate(
                ['lead_id' => $leadData['id']],
                [
                    'form_id' => $leadData['form_id'],
                    'field_data' => $leadData['field_data'],
                    'created_time' => $leadData['created_time']
                ]
            );

            // Send WhatsApp if phone exists
            // $this->sendWhatsappToLead($savedLead);
        }

        $this->info(count($leads) . " lead(s) processed.");
        return 0;
    }

    protected function sendWhatsappToLead(Lead $lead)
    {
        $phone = null;
        $name = null;

        foreach ($lead->field_data as $field) {
            if ($field['name'] === 'phone_number') $phone = $field['values'][0] ?? null;
            if ($field['name'] === 'full_name') $name = $field['values'][0] ?? null;
        }

        if (!$phone) return;

        $message = "Hello " . ($name ?? "") . ", thank you for submitting your request. Our team will contact you shortly!";
        $result = $this->whatsapp->sendMessage($phone, $message);

        $lead->update([
            'whatsapp_status' => $result['status'],
            'whatsapp_error' => $result['error']
        ]);

        $this->info("WhatsApp message to {$phone}: " . $result['status']);
    }
}