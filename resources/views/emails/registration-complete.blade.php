Subject: FDI Summit Registration Complete - Your Login Credentials

Dear {{ $registration->personal_info['first_name'] }} {{ $registration->personal_info['last_name'] }},

Congratulations! Your registration for the FDI B2B Government Summit has been completed successfully.

Your login credentials:
- Email: {{ $user->email }}
- Password: {{ $user->temporary_password }}

Please log in to your dashboard at [Your Dashboard URL] and change your password immediately.

Registration Details:
- Reference Code: {{ $registration->reference_code }}
- Ticket Type: {{ $registration->business->ticket_type }}
- Attendees: {{ $registration->business->attendees->count() }}

We look forward to seeing you at the summit!

Best regards,
FDI Summit Organizers