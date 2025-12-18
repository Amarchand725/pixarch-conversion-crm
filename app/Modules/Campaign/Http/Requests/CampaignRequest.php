<?php

namespace App\Modules\Campaign\Http\Requests;

use App\Models\Status;
use App\Models\User;
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
            'start_date' => [
                'nullable',
                'date',
                'after_or_equal:now',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:now',
                'after_or_equal:start_date',
            ],
            'description' => ['nullable', 'string'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function prepareForValidation()
    {
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('uuid', $this->input('status_id'))->value('id')
            ]);
        }

        if ($this->has('user_ids')) {
            $userIds = collect($this->input('user_ids'))
                ->map(fn($uuid) => User::where('uuid', $uuid)->value('id'))
                ->filter() // remove nulls if uuid not found
                ->toArray();

            $this->merge([
                'user_ids' => $userIds
            ]);
        }
    }
}