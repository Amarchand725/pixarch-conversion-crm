<?php

namespace App\Modules\Lead\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Source;
use App\Models\Status;
use App\Models\User;
use App\Modules\LeadCapture\Models\LeadCapture;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lead = $this->route('lead');

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'nullable',
                'regex:/^\(\d{3}\)\s-\s\d{8}$/',
                Rule::unique('leads', 'phone')->ignore($lead),
            ],
            'email'    => [
                'nullable',
                'email',
                Rule::unique('leads', 'email')->ignore($lead),
            ],
            'value'    => [
                'nullable',
            ],
            'source_id' => ['nullable', 'exists:sources,id'],
            'status_id' => ['nullable', 'exists:statuses,id'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'status' => ['nullable', Rule::in(statuses())],
            'pipeline' => ['nullable', Rule::in(pipelines())],

            'fields' => ['nullable', 'array'],

            // Dynamic Field Validations
            'fields.*' => ['nullable'],
        ];
    }
    public function prepareForValidation()
    {   
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('uuid', $this->input('status_id'))->value('id')
            ]);
        }

        if ($this->has('source_id')) {
            $this->merge([
                'source_id' => Source::where('uuid', $this->input('source_id'))->value('id')
            ]);
        }

        if ($this->has('assignee_id')) {
            $this->merge([
                'assignee_id' => User::where('uuid', $this->input('assignee_id'))->value('id')
            ]);
        }
    }
}