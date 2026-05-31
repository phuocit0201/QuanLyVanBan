<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Form Request for user registration validation.
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('messages.validation.name_required'),
            'name.max' => trans('messages.validation.name_max'),
            'email.required' => trans('messages.validation.email_required'),
            'email.email' => trans('messages.validation.email_invalid'),
            'email.unique' => trans('messages.validation.email_unique'),
            'password.required' => trans('messages.validation.password_required'),
            'password.min' => trans('messages.validation.password_min'),
            'password.confirmed' => trans('messages.validation.password_confirmed'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => trans('messages.validation.name_required'),
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
