<?php

namespace App\Http\Controllers;

use App\Modules\LeadCapture\Models\LeadCapture as ModelsLeadCapture;

class LeadCapturePublicController extends Controller
{
    public function show($uuid)
    {
        $model = ModelsLeadCapture::where('uuid', $uuid)->firstOrFail();
        $title = $model->name.' - Form'; ;
        return view('frontend.lead-form', get_defined_vars());
    }
}
