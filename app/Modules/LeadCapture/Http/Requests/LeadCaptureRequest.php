<?php

namespace App\Modules\LeadCapture\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadCaptureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ];
    }
}