<?php

namespace App\Modules\Meeting\Http\Requests;

use App\Models\Status;
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
            'status_id' => ['nullable', 'exists:statuses,id'],
            'author_id' => ['nullable', 'integer'],
'status_id' => ['nullable', 'integer'],
'name' => ['required', 'string', 'max:255'],
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