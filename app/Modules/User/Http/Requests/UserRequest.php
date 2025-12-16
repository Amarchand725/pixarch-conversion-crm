<?php

namespace App\Modules\User\Http\Requests;

use App\Enum\AgentTypeEnum;
use App\Enum\GenderEnum;
use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'status_id' => ['nullable', 'exists:statuses,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'nullable',
                'intl_phone', // validates full international number
                Rule::unique('users', 'phone')->ignore($user),
            ],
            'gender' => ['required', Rule::in(GenderEnum::cases())],
            'type' => ['required', Rule::in(AgentTypeEnum::cases())],
            'doj' => [ 'nullable'],
            'email'    => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'password' => [
                Rule::requiredIf(is_null($user)),
                'string',
                'min:6'
            ],
            'avatar' => [ 'nullable'],
            'daily_capacity' => ['required', 'numeric', 'min:1'],
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