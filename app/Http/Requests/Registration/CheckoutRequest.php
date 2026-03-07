<?php

namespace App\Http\Requests\Registration;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'referral_code' => 'nullable|string|exists:agents,referral_code',
            'payment_method' => ['required', Rule::in(['paystack', 'bank_deposit'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'referral_code.exists' => 'The referral code is invalid.',
            'payment_method.in' => 'Please select a valid payment method.',
        ];
    }
}