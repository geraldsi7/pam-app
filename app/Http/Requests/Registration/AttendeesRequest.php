<?php

namespace App\Http\Requests\Registration;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendeesRequest extends FormRequest
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
            'ticket_type' => ['required', Rule::in(['1*', '3*'])],
            'attendees' => 'required|array|min:1',
            'attendees.*.first_name' => 'required|string|max:255',
            'attendees.*.middle_name' => 'nullable|string|max:255',
            'attendees.*.last_name' => 'required|string|max:255',
            'attendees.*.id_number' => 'required|string|max:255',
            'attendees.*.nationality' => 'required|string|max:255',
            'attendees.*.email' => 'required|email',
            'attendees.*.phone' => 'required|string',
            'attendees.*.designation' => 'nullable|string|max:255',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $ticketType = $this->input('ticket_type');
            $attendees = $this->input('attendees', []);

            // Validate ticket type availability based on business origin
            $referenceCode = session('registration_ref');
            if ($referenceCode) {
                $registration = app(\App\Services\RegistrationService::class)->getRegistrationByReference($referenceCode);
                if ($registration && $registration->business) {
                    $business = $registration->business;
                    $availableTicketTypes = app(\App\Services\RegistrationService::class)->getTicketTypesByOrigin($business->origin);

                    if (!in_array($ticketType, $availableTicketTypes)) {
                        $validator->errors()->add('ticket_type', 'Invalid ticket type for your origin');
                    }
                }
            }

            // Validate attendee count based on ticket type
            if ($ticketType) {
                $maxAttendees = $ticketType === '1*' ? 1 : 3;
                if (count($attendees) > $maxAttendees) {
                    $validator->errors()->add('attendees', "Maximum {$maxAttendees} attendees allowed for this ticket type");
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'attendees.*.first_name' => 'first name',
            'attendees.*.middle_name' => 'middle name',
            'attendees.*.last_name' => 'last name',
            'attendees.*.id_number' => 'ID/Passport number',
            'attendees.*.nationality' => 'nationality',
            'attendees.*.email' => 'email',
            'attendees.*.phone' => 'phone',
            'attendees.*.designation' => 'designation',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ticket_type.in' => 'Please select a valid ticket type.',
            'attendees.required' => 'At least one attendee is required.',
            'attendees.*.email.email' => 'Please provide a valid email address.',
        ];
    }
}