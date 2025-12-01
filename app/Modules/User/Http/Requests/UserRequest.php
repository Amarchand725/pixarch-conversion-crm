<?php

namespace App\Modules\User\Http\Requests;

use App\Enum\GenderEnum;
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
            'name' => ['required', 'string', 'max:255'],
            'phone'    => [
                'nullable',
                'regex:/^\(\d{3}\)\s-\s\d{8}$/',
                Rule::unique('users', 'phone')->ignore($user),
            ],
            'gender' => ['required', Rule::in(GenderEnum::cases())],
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
        ];
    }
}