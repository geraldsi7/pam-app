<?php

namespace App\Services;

use App\Mail\RegistrationCompleteMail;
use App\Mail\RegistrationReferenceMail;
use App\Models\Agent;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PostPaymentService
{
    /**
     * Handle successful payment completion
     */
    public function handleSuccessfulPayment(Registration $registration, Payment $payment): void
    {
        // Create user account (or get existing if already created)
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
     * Handle bank deposit submission - create account and send credentials immediately
     */
    public function handleBankDepositSubmission(Registration $registration, Payment $payment): void
    {
        // Create user account
        $user = $this->createUserAccount($registration);

        // Send completion email with login credentials (with pending verification note)
        $this->sendBankDepositCompletionEmail($registration, $user);

        // Process agent commission if applicable (for bank deposits, commission might be paid after verification)
        if ($registration->agent_id) {
            $this->processPendingAgentCommission($registration, $payment);
        }

        // Registration status remains payment_pending until verification.
    }

    /**
     * Create user account for the registrant
     */
    private function createUserAccount(Registration $registration): User
    {
        $existingUser = User::where('email', $registration->email)->first();

        if ($existingUser) {
            if (!$existingUser->registration_id) {
                $existingUser->update(['registration_id' => $registration->id]);
            }
            $existingUser->temporary_password = null;

            return $existingUser;
        }

        $password = Str::random(12);

        $user = User::create([
            'name' => $registration->personal_info['first_name'] . ' ' . $registration->personal_info['last_name'],
            'email' => $registration->email,
            'password' => Hash::make($password),
            'registration_id' => $registration->id,
            'user_type' => 'member',
            'is_active' => true,
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
        Mail::to($registration->email)->queue(new RegistrationCompleteMail($registration, $user));
    }

    /**
     * Send completion email for bank deposits (pending verification)
     */
    private function sendBankDepositCompletionEmail(Registration $registration, User $user): void
    {
        Mail::to($registration->email)->queue(new RegistrationCompleteMail($registration, $user));
    }

    /**
     * Send reference code email after step 1 completion
     */
    public function sendReferenceCodeEmail(Registration $registration): void
    {
        Mail::to($registration->email)->queue(new RegistrationReferenceMail($registration));
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
        Log::info('Agent commission processed', [
            'agent_id' => $agent->id,
            'registration_id' => $registration->id,
            'commission_amount' => $commissionAmount,
            'ticket_type' => $business->ticket_type,
        ]);

        // You could also send notification to agent
        // Mail::to($agent->email)->send(new AgentCommissionNotification($agent, $commissionAmount));
    }

    /**
     * Process agent commission for pending bank deposits (deferred until verification)
     */
    private function processPendingAgentCommission(Registration $registration, Payment $payment): void
    {
        $agent = $registration->agent;

        if (!$agent) {
            return;
        }

        // For bank deposits, we log the pending commission but don't process it yet
        // It will be processed when the payment is verified
        Log::info('Agent commission pending - bank deposit verification required', [
            'agent_id' => $agent->id,
            'registration_id' => $registration->id,
            'payment_id' => $payment->id,
            'ticket_type' => $registration->business->ticket_type,
            'status' => 'pending_verification',
        ]);
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
        $registration->update(['status' => 'cancelled']);
    }
}
