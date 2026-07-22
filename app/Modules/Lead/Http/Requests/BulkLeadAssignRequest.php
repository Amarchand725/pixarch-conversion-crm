<?php

namespace App\Modules\Lead\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class BulkLeadAssignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assignee_id' => 'required|exists:users,id',
            'status_id' => 'required|exists:statuses,id',
            'lead_ids' => 'required|array',
            'lead_ids.*' => 'exists:leads,id',
        ];
    }
    public function prepareForValidation()
    {   
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('uuid', $this->input('status_id'))->value('id')
            ]);
        }

        if ($this->has('assignee_id')) {
            $this->merge([
                'assignee_id' => User::where('uuid', $this->input('assignee_id'))->value('id')
            ]);
        }

        if ($this->filled('lead_ids')) {
            $this->merge([
                'lead_ids' => json_decode($this->input('lead_ids'), true),
            ]);
        }
    }
}