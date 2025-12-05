<?php

namespace App\Modules\Campaign\Http\Requests;

use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', Rule::in(campaignTypes())],
            'budget'    => [ 'nullable'],
            'start_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'end_date'   => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('uuid', $this->input('status_id'))->value('id')
            ]);
        }
    }
}