<?php

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

    protected string $leadgenId;
    protected string $formId;
    protected string $pageId;
    protected ?array $prefetchedData;

    public function __construct(
        protected LeadContract $leadRepo,
        string $leadgenId,
        string $formId,
        string $pageId,
        ?array $prefetchedData = null // optional pre-fetched data
    ) {
        $this->leadgenId = $leadgenId;
        $this->formId = $formId;
        $this->pageId = $pageId;
        $this->prefetchedData = $prefetchedData;
    }

    public function handle(): void
    {
        // 1️⃣ Check if lead already exists
        $existing = FacebookLeadMeta::where('leadgen_id', $this->leadgenId)->first();
        if ($existing) return;

        // 2️⃣ Use prefetched data if available, else fetch from FB API
        $data = $this->prefetchedData;
        if (!$data) {
            $pageAccessToken = config('services.facebook.page_token');
            $response = Http::get("https://graph.facebook.com/v19.0/{$this->leadgenId}", [
                'fields' => 'created_time,field_data',
                'access_token' => $pageAccessToken,
            ]);

            if (!$response->successful()) {
                logger()->error("Facebook Lead API failed", [
                    'leadgen_id' => $this->leadgenId,
                    'response' => $response->body(),
                ]);
                return;
            }

            $data = $response->json();
        }

        $fieldData = $data['field_data'] ?? [];

        // Map fields
        $mapped = [];
        foreach ($fieldData as $field) {
            $mapped[$field['name']] = $field['values'][0] ?? null;
        }

        // Rest of your job remains the same...
        $name  = $mapped['full_name'] ?? $mapped['name'] ?? 'Facebook Lead';
        $email = $mapped['email'] ?? null;
        $phone = $mapped['phone_number'] ?? null;

        $numericCode = null;
        $isoCode = null;
        // if ($phone) {
        //     $numericCode = preg_replace('/\D/', '', $phone);
        //     $isoCode = substr($numericCode, 0, 2);
        // }

        if (!empty($phone)) {
            // Remove all non-digit characters
            $digits = preg_replace('/\D/', '', $phone);

            if (!empty($digits)) {
                $numericCode = $digits;

                // Only assign ISO code if we have at least 2 digits
                $isoCode = strlen($digits) >= 2 ? substr($digits, 0, 2) : null;
            }
        }

        if ($phone && empty($digits)) {
            logger()->warning("Facebook lead phone could not be parsed", [
                'leadgen_id' => $this->leadgenId,
                'raw_phone' => $phone,
            ]);
        }

        $status_id = Status::where('model', 'Lead')->where('name', 'created')->value('id');

        $leadCapture = LeadCapture::firstOrCreate(
            ['platform' => 'facebook', 'external_id' => $this->formId],
            [
                'status_id' => $status_id,
                'shareable_link' => 'https://facebook.com/leadgen/' . $this->formId,
                'faq_status' => 0,
                'name' => 'Facebook Form - ' . $this->formId,
                'description' => 'Auto-created from Facebook webhook',
                'meta' => json_encode(['page_id' => $this->pageId]),
            ]
        );

        $source_id = Source::where('name', 'Facebook Ads')->value('id');

        $payload = [
            'author_id' => null,
            'status_id' => $status_id,
            'assignee_id' => LeadAssigner::getNextAgent($isoCode),
            'source_id' => $source_id,
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

        $lead = $this->leadRepo->storeModel($payload);

        if ($lead) {
            FacebookLeadMeta::create([
                'lead_id' => $lead->id,
                'leadgen_id' => $this->leadgenId,
                'form_id' => $this->formId,
                'page_id' => $this->pageId,
                'raw_payload' => json_encode($data),
                'received_at' => $data['created_time'] ?? now(),
            ]);
        }
    }
}


// class ProcessFacebookLead implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     protected string $leadgenId;
//     protected string $formId;
//     protected string $pageId;

//     /**
//      * Create a new job instance.
//      */
//     public function __construct(
//         protected LeadContract $leadRepo, string $leadgenId, string $formId, string $pageId)
//     {
//         $this->leadgenId = $leadgenId;
//         $this->formId = $formId;
//         $this->pageId = $pageId;
//     }

//     /**
//      * Execute the job.
//      */
//     public function handle(): void
//     {
//         // 1️⃣ Check if lead already exists
//         $existing = FacebookLeadMeta::where('leadgen_id', $this->leadgenId)->first();
//         if ($existing) {
//             // Already processed
//             return;
//         }

//         // 2️⃣ Fetch lead data from Facebook Graph API
//         $pageAccessToken = config('services.facebook.page_token');

//         $response = Http::get("https://graph.facebook.com/v19.0/{$this->leadgenId}", [
//             'fields' => 'created_time,field_data',
//             'access_token' => $pageAccessToken,
//         ]);

//         if (!$response->successful()) {
//             // Optional: throw or log for retry
//             logger()->error("Facebook Lead API failed", [
//                 'leadgen_id' => $this->leadgenId,
//                 'response' => $response->body(),
//             ]);
//             return;
//         }

//         $data = $response->json();
//         $fieldData = $data['field_data'] ?? [];

//         // Map fields
//         $mapped = [];
//         foreach ($fieldData as $field) {
//             $mapped[$field['name']] = $field['values'][0] ?? null;
//         }

//         $name  = $mapped['full_name'] ?? $mapped['name'] ?? 'Facebook Lead';
//         $email = $mapped['email'] ?? null;
//         $phone = $mapped['phone_number'] ?? null;

//         // Parse numeric_code / iso_code if you want
//         $numericCode = null;
//         $isoCode = null;
//         if ($phone) {
//             // Simple example, you can use libphonenumber
//             $numericCode = preg_replace('/\D/', '', $phone);
//             $isoCode = substr($numericCode, 0, 2);
//         }

//         $status_id = Status::where('model', 'Lead')->where('name', 'created')->value('id');

//         // 3️⃣ Find or create lead_capture for this form
//         $leadCapture = LeadCapture::firstOrCreate(
//             [
//                 'platform' => 'facebook',
//                 'external_id' => $this->formId,
//             ],
//             [
//                 'status_id' => $status_id,
//                 'shareable_link' => 'https://facebook.com/leadgen/' . $this->formId,
//                 'faq_status' => 0,
//                 'name' => 'Facebook Form - ' . $this->formId,
//                 'description' => 'Auto-created from Facebook webhook',
//                 'meta' => json_encode(['page_id' => $this->pageId]),
//             ]
//         );

//         $source_id = Source::where('name', 'Facebook Ads')->value('id');

//         $payload['author_id'] = null; //default
//         $payload['status_id'] = $status_id; //default
//         $payload['assignee_id'] = LeadAssigner::getNextAgent( $payload['iso_code']); //rol-robbin agent id
//         $payload['source_id'] = $source_id;
//         $payload['lead_capture_id'] = $leadCapture->id;
//         $payload['budget'] = 0;
//         $payload['name'] = $name;
//         $payload['email'] = $email;
//         $payload['phone'] = $phone;
//         $payload['numeric_code'] = $numericCode;
//         $payload['iso_code'] = $isoCode;
//         $payload['status'] = 'open'; //default
//         $payload['pipeline'] = 'paid social - leads'; //default
//         $payload['fields'] = json_encode($mapped);
        
//         $lead = $this->leadRepo->storeModel($payload);

//         // 5️⃣ Store Facebook meta info
//         if($lead){
//             FacebookLeadMeta::create([
//                 'lead_id' => $lead->id,
//                 'leadgen_id' => $this->leadgenId,
//                 'form_id' => $this->formId,
//                 'page_id' => $this->pageId,
//                 'raw_payload' => json_encode($data),
//                 'received_at' => Carbon::now(),
//             ]);
//         }
//     }
// }