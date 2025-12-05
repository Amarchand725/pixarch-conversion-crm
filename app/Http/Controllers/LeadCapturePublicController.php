<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Modules\Faq\Models\Faq;
use App\Modules\Lead\Models\Lead;
use App\Modules\LeadCapture\Models\LeadCapture as ModelsLeadCapture;
use Illuminate\Http\Request;

class LeadCapturePublicController extends Controller
{
    protected $status;

    public function __construct(){
        $this->status = new Status();
    }

    public function show($uuid)
    {
        $status_id = $this->status->where('model', 'Faq')->where('name', 'active')->value('id');
        $model = ModelsLeadCapture::where('uuid', $uuid)->firstOrFail();
        $faqs = Faq::where('status_id', $status_id)->get();
        $title = $model->name.' - Form';
        return view('frontend.landing-form', get_defined_vars());
    }

    public function store(Request $request, Lead $lead){
        dd($request->all());
    }
}
