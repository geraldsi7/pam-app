<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AgentUpdateRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $agentId = $this->route('agent')?->id ?? $this->route('agent');

        return [
            'name' => ['required', 'string', 'max:255'],
            'referral_code' => ['required', 'string', 'max:50', Rule::unique('agents', 'referral_code')->ignore($agentId)],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
