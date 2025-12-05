<?php

namespace App\Modules\LeadCapture\Http\Requests;

use App\Models\Status;
use App\Modules\Campaign\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;

class LeadCaptureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lead = $this->route('lead');

        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            
            'fields' => ['required', 'array'],

            // Dynamic Field Validations
            'fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.type' => ['required', 'string', 'in:text,email,number,textarea,select,file'],
            'fields.*.placeholder' => ['nullable', 'string', 'max:255'],
            'fields.*.required' => ['required', 'in:0,1'],
            'fields.*.options' => ['nullable', 'string'], // only needed when type=select
        ];
    }

    public function attributes(): array
    {
        return [
            'fields.*.label' => 'Label',
            'fields.*.type' => 'Type',
            'fields.*.required' => 'Required field',
            'fields.*.options' => 'Select options',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('uuid', $this->input('status_id'))->value('id')
            ]);
        }

        if ($this->has('campaign_id')) {
            $this->merge([
                'campaign_id' => Campaign::where('uuid', $this->input('campaign_id'))->value('id')
            ]);
        }
    }
}