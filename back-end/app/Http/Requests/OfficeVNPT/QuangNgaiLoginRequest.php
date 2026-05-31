<?php

declare(strict_types=1);

namespace App\Http\Requests\OfficeVNPT;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

/**
 * Form Request for Quang Ngai Office login validation.
 */
class QuangNgaiLoginRequest extends FormRequest
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
            'tokenFireBase' => ['nullable', 'string'],
            'language' => ['nullable', 'string', Rule::in(['VI', 'EN'])],
            'type' => ['nullable', 'string', Rule::in(['IOS', 'ANDROID'])],
            'device' => ['nullable', 'string'],
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
