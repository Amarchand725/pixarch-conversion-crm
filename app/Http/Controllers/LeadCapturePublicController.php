<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Models\Source;
use App\Models\Status;
use App\Models\User;
use App\Modules\Faq\Models\Faq;
use App\Modules\LeadCapture\Models\LeadCapture;
use App\Modules\Lead\Http\Requests\LeadRequest;
use App\Modules\Lead\Repositories\Contracts\LeadContract;
use App\Services\LeadAssigner;
use App\Services\PhoneNumberService;
use Exception;
use Illuminate\Support\Facades\DB;

class LeadCapturePublicController extends BaseModuleController
{
    protected $status;
    protected $source;
    protected $lead_capture;
    protected $user;

    public function __construct(
        protected LeadContract $leadRepo
    ){
        $this->status = new Status();
        $this->source = new Source();
        $this->lead_capture = new LeadCapture();
        $this->user = new User();

        // Initialize common module variables automatically
        $this->autoInit();
    }

    public function show($uuid)
    {
        $faqs = [];
        $status_id = $this->status->where('model', 'Faq')->where('name', 'active')->value('id');
        $model = $this->lead_capture->where('uuid', $uuid)->firstOrFail();
        if($model->faq_status){
            $faqs = Faq::where('status_id', $status_id)->get();
        }
        $title = $model->name.' - Form';
        return view('frontend.landing-form', get_defined_vars());
    }

    public function store(LeadRequest $request, $lead_capture_uuid, PhoneNumberService $phoneService){
        $payload = $request->validated();

        $parsed = $phoneService->parse($request->phone);
        $payload['numeric_code'] = $parsed['numeric_code'];
        $payload['iso_code'] = $parsed['iso_code'];

        $leadCapture = $this->lead_capture->where('uuid', $lead_capture_uuid)->first();
        $lead_capture_id = $leadCapture->id;
        $status_id = $this->status->where('model', 'Lead')->where('name', 'created')->value('id');
        
        $campaignAgents = optional($leadCapture->campaign)->agents ?? collect();
        
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $lead_capture_id, $status_id, $campaignAgents) {
                $payload['status_id'] = $status_id; //default
                $payload['assignee_id'] = LeadAssigner::getNextAgent($campaignAgents, $payload['iso_code']); //rol-robbin agent id
                $payload['author'] = null; //default
                $payload['source_id'] = $this->source->where('name', 'website')->value('id');
                $payload['lead_capture_id'] = $lead_capture_id;
                $payload['status'] = 'open'; //default
                $payload['pipeline'] = 'paid social - leads'; //default
                $payload['fields'] = json_encode($payload['fields']);
                
                $this->leadRepo->storeModel($payload);
            });
            return redirect()->back()->with('message', 'Lead submitted successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
