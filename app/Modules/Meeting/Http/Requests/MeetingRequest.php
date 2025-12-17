<?php

namespace App\Modules\Meeting\Http\Requests;

use App\Models\Status;
use App\Models\User;
use App\Modules\Lead\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;

class MeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_id' => ['required', 'exists:leads,id'],
            'status_id' => ['nullable', 'exists:statuses,id'],

            // Only validate if the field is present
            'description' => ['nullable', 'string'],

            // Meeting fields: validate only if start or end time is present
            'start_date_time' => [
                'required',
                'date',
                'after_or_equal:now',
            ],
            'end_date_time' => [
                'required',
                'date',
                'after_or_equal:now',
                'after_or_equal:start_date_time',
            ],
            'attendee_id' => ['nullable', 'exists:users,id', 'required_with:start_date_time,end_date_time'],
        ];
    }

    public function prepareForValidation()
    {   
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('uuid', $this->input('status_id'))->value('id')
            ]);
        }

        // Convert attendee UUID to ID
        if ($this->has('attendee_id')) {
            $this->merge([
                'attendee_id' => User::where('uuid', $this->input('attendee_id'))->value('id')
            ]);
        }

        if ($this->has('lead_id')) {
            $this->merge([
                'lead_id' => Lead::where('uuid', $this->input('lead_id'))->value('id')
            ]);
        }
    }
}