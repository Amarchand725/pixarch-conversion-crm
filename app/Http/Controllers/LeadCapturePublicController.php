<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BackOffice\BaseModuleController;
use App\Models\Source;
use App\Models\Status;
use App\Modules\Faq\Models\Faq;
use App\Modules\LeadCapture\Models\LeadCapture as ModelsLeadCapture;
use App\Modules\Lead\Http\Requests\LeadRequest;
use App\Modules\Lead\Repositories\Contracts\LeadContract;
use Exception;
use Illuminate\Support\Facades\DB;

class LeadCapturePublicController extends BaseModuleController
{
    protected $status;
    protected $source;

    public function __construct(
        protected LeadContract $leadRepo
    ){
        $this->status = new Status();
        $this->source = new Source();
    }

    public function show($uuid)
    {
        $status_id = $this->status->where('model', 'Faq')->where('name', 'active')->value('id');
        $model = ModelsLeadCapture::where('uuid', $uuid)->firstOrFail();
        $faqs = Faq::where('status_id', $status_id)->get();
        $title = $model->name.' - Form';
        return view('frontend.landing-form', get_defined_vars());
    }

    public function store(LeadRequest $request, ModelsLeadCapture $leadCapture){
        $payload = $request->validated();
        
        try {
            $response = null;
            DB::transaction(function () use (&$response, $payload, $leadCapture) {
                $payload['author'] = null;
                $payload['source_id'] = $this->source->where('name', 'website')->value('id');
                $payload['lead_capture_id'] = $leadCapture->id;
                $payload['status'] = 'open'; //default
                $payload['fields'] = json_encode($payload['fields']);

                dd($payload);
                $this->leadRepo->storeModel($payload);
            });
            return successResponse($response, $this->singularLabel. ' registered successfully.');
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
