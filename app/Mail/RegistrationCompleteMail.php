<?php

namespace App\Mail;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationCompleteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Registration $registration,
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'FDI Summit Registration Complete - Login Credentials',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-complete',
            with: [
                'registration' => $this->registration,
                'user' => $this->user,
            ],
        );
    }
}