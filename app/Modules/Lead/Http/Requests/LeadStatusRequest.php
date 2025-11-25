<?php

namespace App\Modules\Lead\Http\Requests;

use App\Models\Status;
use App\Modules\Lead\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;

class LeadStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_id' => ['required', 'exists:leads,id'],
            'status_id' => ['required', 'exists:statuses,id'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('lead_id')) {
            $this->merge([
                'lead_id' => Lead::where('uuid', $this->input('lead_id'))->value('id')
            ]);
        }

        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('uuid', $this->input('status_id'))->value('id')
            ]);
        }
    }
}