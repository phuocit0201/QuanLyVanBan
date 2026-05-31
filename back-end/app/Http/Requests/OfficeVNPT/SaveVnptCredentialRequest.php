<?php

declare(strict_types=1);

namespace App\Http\Requests\OfficeVNPT;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SaveVnptCredentialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
            'device_type' => ['nullable', 'string', Rule::in(['IOS', 'ANDROID'])],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => trans('messages.validation.username_required'),
            'password.required' => trans('messages.validation.password_required'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => trans('messages.validation.failed'),
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
