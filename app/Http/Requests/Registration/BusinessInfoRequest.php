<?php

namespace App\Http\Requests\Registration;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessInfoRequest extends FormRequest
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
            'company_name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'email' => 'required|email|max:255',
            'phone' => 'required|string',
            'attendance_mode' => ['required', Rule::in(['in_person', 'online'])],
            'industries' => 'required|array',
            'industries.*' => 'exists:industries,id',
            'company_size' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'street_address' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'attendance_mode.in' => 'Please select a valid attendance mode.',
            'country_id.exists' => 'Please select a valid country.',
            'website.url' => 'Please provide a valid website URL.',
        ];
    }
}