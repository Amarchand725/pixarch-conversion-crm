<?php

//chatgpt shared chat code
// https://chatgpt.com/share/694eb8a3-9664-8009-b49d-347dad2916e5   

namespace App\Jobs;

use App\Models\FacebookLeadMeta;
use App\Models\Source;
use App\Models\Status;
use App\Modules\Lead\Repositories\Contracts\LeadContract;
use App\Modules\LeadCapture\Models\LeadCapture;
use App\Services\LeadAssigner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ProcessFacebookLead implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum retry attempts
     */
    public $tries = 10;

    /**
     * Retry delay in seconds (5 minutes)
     */
    public $backoff = 300;

    protected string $leadgenId;
    protected string $formId;
    protected string $pageId;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected LeadContract $leadRepo,
        string $leadgenId,
        string $formId,
        string $pageId
    ) {
        $this->leadgenId = $leadgenId;
        $this->formId   = $formId;
        $this->pageId   = $pageId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /**
         * 1️⃣ Prevent duplicate processing
         */
        if (FacebookLeadMeta::where('leadgen_id', $this->leadgenId)->exists()) {
            return;
        }

        /**
         * 2️⃣ Fetch lead details from Facebook Graph API
         */
        $pageAccessToken = config('services.facebook.page_token');

        $response = Http::get(
            "https://graph.facebook.com/v19.0/{$this->leadgenId}",
            [
                'fields' => 'created_time,field_data',
                'access_token' => $pageAccessToken,
            ]
        );

        /**
         * 3️⃣ Handle API failure (token expired / network issue)
         */
        if (!$response->successful()) {

            $errorCode = $response->json('error.code');

            // Token expired or invalid
            if ($errorCode == 190) {
                logger()->critical('Facebook Page token expired or invalid', [
                    'leadgen_id' => $this->leadgenId,
                ]);
                // 👉 Notify admin here if needed
            } else {
                logger()->warning('Facebook lead fetch failed, retrying', [
                    'leadgen_id' => $this->leadgenId,
                    'response' => $response->body(),
                ]);
            }

            // Retry job later
            $this->release($this->backoff);
            return;
        }

        /**
         * 4️⃣ Parse Facebook response
         */
        $data = $response->json();
        $fieldData = $data['field_data'] ?? [];

        /**
         * Convert Facebook field_data to key-value array
         */
        $mapped = [];
        foreach ($fieldData as $field) {
            $mapped[$field['name']] = $field['values'][0] ?? null;
        }

        /**
         * Extract common fields
         */
        $name  = $mapped['full_name'] ?? $mapped['name'] ?? 'Facebook Lead';
        $email = $mapped['email'] ?? null;
        $phone = $mapped['phone_number'] ?? null;

        /**
         * Parse phone codes (basic example)
         */
        $numericCode = null;
        $isoCode = null;

        if ($phone) {
            $numericCode = preg_replace('/\D/', '', $phone);
            $isoCode = substr($numericCode, 0, 2);
        }

        /**
         * Get default Lead status
         */
        $status_id = Status::where('model', 'Lead')
            ->where('name', 'created')
            ->value('id');

        /**
         * 5️⃣ Create or fetch Lead Capture (Facebook Form)
         */
        $leadCapture = LeadCapture::firstOrCreate(
            [
                'platform' => 'facebook',
                'external_id' => $this->formId,
            ],
            [
                'status_id' => $status_id,
                'shareable_link' => 'https://facebook.com/leadgen/' . $this->formId,
                'faq_status' => 0,
                'name' => 'Facebook Form - ' . $this->formId,
                'description' => 'Auto-created from Facebook webhook',
                'meta' => json_encode(['page_id' => $this->pageId]),
            ]
        );

        /**
         * 6️⃣ Prepare Lead payload
         */
        $payload = [
            'author_id' => null,
            'status_id' => $status_id,
            'source_id' => Source::where('name', 'Facebook Ads')->value('id'),
            'lead_capture_id' => $leadCapture->id,
            'budget' => 0,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'numeric_code' => $numericCode,
            'iso_code' => $isoCode,
            'status' => 'open',
            'pipeline' => 'paid social - leads',
            'fields' => json_encode($mapped),
        ];

        /**
         * Assign agent AFTER iso_code is set
         */
        $payload['assignee_id'] = LeadAssigner::getNextAgent($isoCode);

        /**
         * 7️⃣ Store Lead
         */
        $lead = $this->leadRepo->storeModel($payload);

        /**
         * 8️⃣ Save Facebook meta
         */
        if ($lead) {
            FacebookLeadMeta::create([
                'lead_id' => $lead->id,
                'leadgen_id' => $this->leadgenId,
                'form_id' => $this->formId,
                'page_id' => $this->pageId,
                'raw_payload' => json_encode($data),
                'received_at' => Carbon::now(),
            ]);
        }
    }
}
