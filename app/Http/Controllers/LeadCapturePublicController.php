<?php

namespace App\Http\Controllers;

use App\Modules\Lead\Models\Lead;
use App\Modules\LeadCapture\Models\LeadCapture as ModelsLeadCapture;
use Illuminate\Http\Request;

class LeadCapturePublicController extends Controller
{
    public function show($uuid)
    {
        $model = ModelsLeadCapture::where('uuid', $uuid)->firstOrFail();
        $title = $model->name.' - Form';
        return view('frontend.landing-form', get_defined_vars());
    }

    public function store(Request $request, Lead $lead){
        dd($request->all());
    }
}
