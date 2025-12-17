<?php

namespace App\Modules\Lead\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Source;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $lead = $this->route('lead');

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'intl_phone', // validates full international number
                Rule::unique('leads', 'phone')->ignore($lead),
            ],
            'email'    => [
                'nullable',
                'email',
                Rule::unique('leads', 'email')->ignore($lead),
            ],
            'budget'    => [
                'nullable', 'numeric',
            ],
            'source_id' => ['nullable', 'exists:sources,id'],
            'status_id' => ['nullable', 'exists:statuses,id'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'status' => ['nullable', Rule::in(statuses())],
            'pipeline' => ['nullable', Rule::in(pipelines())],

            'fields' => ['nullable', 'array'],

            // Dynamic Field Validations
            'fields.*' => ['nullable'],

            // captcha only if required
            'g-recaptcha-response' => $this->captchaRequired()
                ? 'required'
                : 'nullable',
        ];
    }
    public function prepareForValidation()
    {   
        if ($this->has('status_id')) {
            $this->merge([
                'status_id' => Status::where('uuid', $this->input('status_id'))->value('id')
            ]);
        }

        if ($this->has('source_id')) {
            $this->merge([
                'source_id' => Source::where('uuid', $this->input('source_id'))->value('id')
            ]);
        }

        if ($this->has('assignee_id')) {
            $this->merge([
                'assignee_id' => User::where('uuid', $this->input('assignee_id'))->value('id')
            ]);
        }
    }

    public function withValidator($validator)
    {
        if (! $this->captchaRequired()) {
            return;
        }

        $validator->after(function ($validator) {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => config('recaptcha.secret_key'),
                    'response' => $this->input('g-recaptcha-response'),
                    'remoteip' => $this->ip(),
                ]);

            if (! data_get($response->json(), 'success')) {
                $validator->errors()->add(
                    'g-recaptcha-response',
                    'Captcha verification failed'
                );
            }
        });
    }

    protected function captchaRequired(): bool
    {
        return $this->boolean('captcha_required');
    }
}