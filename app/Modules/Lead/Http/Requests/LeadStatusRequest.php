<?php

namespace App\Modules\Lead\Http\Requests;

use App\Models\Status;
use App\Models\User;
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
            // Always required
            'status_id' => ['required', 'exists:statuses,id'],

            // Only validate if the field is present
            'assignee_id' => ['nullable', 'exists:users,id'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],

            // Meeting fields: validate only if start or end time is present
            'start_date_time' => ['nullable', 'date', 'required_with:end_date_time'],
            'end_date_time' => ['nullable', 'date', 'after_or_equal:start_date_time', 'required_with:start_date_time'],
            'attendee_id' => ['nullable', 'exists:users,id', 'required_with:start_date_time,end_date_time'],
            'time_zone' => ['nullable', 'string'],
        ];
    }

    public function prepareForValidation()
    {
        // Convert status UUID to ID
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('uuid', $this->input('status_id'))->value('id')
            ]);
        }

        // Convert assignee UUID to ID
        if ($this->has('assignee_id')) {
            $this->merge([
                'assignee_id' => User::where('uuid', $this->input('assignee_id'))->value('id')
            ]);
        }

        // Convert attendee UUID to ID
        if ($this->has('attendee_id')) {
            $this->merge([
                'attendee_id' => User::where('uuid', $this->input('attendee_id'))->value('id')
            ]);
        }
    }
}