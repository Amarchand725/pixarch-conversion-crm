<?php

namespace App\Modules\Meeting\Http\Requests;

use App\Models\Status;
use App\Models\User;
use App\Modules\Lead\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;

class MeetingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $action = $this->input('action');

        // Always required
        $rules = [
            'lead_id' => ['required', 'exists:leads,id'],
            'action'  => ['required'],
        ];

        if ($action === 'status') {
            $rules = array_merge($rules, [
                'status_id'   => ['required', 'exists:statuses,id'],
                'description' => ['required', 'string'],
            ]);

        } elseif ($action === 'reschedule') {

            $rules = array_merge($rules, [
                'start_date_time' => ['required', 'date'],
                'end_date_time'   => ['required', 'date', 'after_or_equal:start_date_time'],
                // 'attendee_id'     => ['required', 'exists:users,id'],
            ]);
        }

        return $rules;
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