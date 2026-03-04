<?php

namespace App\Services;

use App\Mail\RegistrationCompleteMail;
use App\Mail\RegistrationReferenceMail;
use App\Models\Agent;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PostPaymentService
{
    /**
     * Handle successful payment completion
     */
    public function handleSuccessfulPayment(Registration $registration, Payment $payment): void
    {
        // Create user account
        $user = $this->createUserAccount($registration);

        // Send completion email with login credentials
        $this->sendCompletionEmail($registration, $user);

        // Process agent commission if applicable
        if ($registration->agent_id) {
            $this->processAgentCommission($registration, $payment);
        }

        // Update registration status
        $registration->update(['status' => 'completed']);
    }

    /**
     * Create user account for the registrant
     */
    private function createUserAccount(Registration $registration): User
    {
        $password = Str::random(12);

        $user = User::create([
            'name' => $registration->personal_info['first_name'] . ' ' . $registration->personal_info['last_name'],
            'email' => $registration->email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        // Store temporary password for email
        $user->temporary_password = $password;

        return $user;
    }

    /**
     * Send completion email with login credentials
     */
    private function sendCompletionEmail(Registration $registration, User $user): void
    {
        Mail::to($registration->email)->send(new RegistrationCompleteMail($registration, $user));
    }

    /**
     * Send reference code email after step 1 completion
     */
    public function sendReferenceCodeEmail(Registration $registration): void
    {
        Mail::to($registration->email)->send(new RegistrationReferenceMail($registration));
    }

    /**
     * Process agent commission
     */
    private function processAgentCommission(Registration $registration, Payment $payment): void
    {
        $agent = $registration->agent;

        if (!$agent) {
            return;
        }

        // Calculate commission based on ticket type
        $business = $registration->business;
        $commissionAmount = 0;

        if ($business->ticket_type === '1*') {
            $commissionAmount = 40000; // $400 in cents
        } elseif ($business->ticket_type === '3*') {
            $commissionAmount = 90000; // $900 in cents
        }

        // Here you would typically create a commission record or update agent's balance
        // For now, we'll just log it
        \Log::info('Agent commission processed', [
            'agent_id' => $agent->id,
            'registration_id' => $registration->id,
            'commission_amount' => $commissionAmount,
            'ticket_type' => $business->ticket_type,
        ]);

        // You could also send notification to agent
        // Mail::to($agent->email)->send(new AgentCommissionNotification($agent, $commissionAmount));
    }

    /**
     * Verify bank deposit payment (admin function)
     */
    public function verifyBankDeposit(Registration $registration, Payment $payment): void
    {
        // Update payment status
        $payment->update(['status' => 'success']);

        // Complete the registration process
        $this->handleSuccessfulPayment($registration, $payment);
    }

    /**
     * Handle failed payment
     */
    public function handleFailedPayment(Registration $registration, Payment $payment): void
    {
        $payment->update(['status' => 'failed']);
        $registration->update(['status' => 'payment_failed']);
    }
}
