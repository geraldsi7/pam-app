<?php

namespace App\Services\PaymentStrategies;

use App\Interface\PaymentInterface;
use App\Models\Payment;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class BankDepositPayment implements PaymentInterface
{
    private $registrationService;
    public function __construct(
    ) {
        $this->registrationService = new RegistrationService();
    }

    public function render(Registration $registration)
    {
        $pricing = $this->registrationService->calculateTotalAmount($registration);

        return Inertia::render('Payment/BankDeposit', [
            'registration' => $registration,
            'pricing' => $pricing,
            'bankDetails' => [
                'bank_name' => 'Example Bank',
                'account_name' => 'FDI Summit Organizers',
                'account_number' => '1234567890',
                'swift_code' => 'EXAMUS33',
            ],
        ]);
    }

    public function process(Registration $registration, $input): InertiaResponse
    {
        $validator = Validator::make($input, [
            'deposit_reference' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'deposit_date' => 'required|date',
            'bank_name' => 'required|string',
            'account_holder' => 'required|string',
        ]);

        if ($validator->fails()) {
            // If validation fails, redirect back to the payment page with errors
            return Inertia::render('Payment/BankDeposit', [
                'registration' => $registration,
                'pricing' => $this->registrationService->calculateTotalAmount($registration),
                'bankDetails' => [
                    'bank_name' => 'Example Bank',
                    'account_name' => 'FDI Summit Organizers',
                    'account_number' => '1234567890',
                    'swift_code' => 'EXAMUS33',
                ],
                'errors' => $validator->errors()->toArray(),
                'old' => $input,
            ])->with('error', 'Please correct the errors below.');
        }

        $validated = $validator->validated();

        $pricing = $this->registrationService->calculateTotalAmount($registration);

        // Create payment record with pending status (requires manual verification)
        $payment = Payment::create([
            'id' => Str::uuid(),
            'registration_id' => $registration->id,
            'method' => 'bank_deposit',
            'status' => 'pending',
            'amount' => $pricing['total'],
            'transaction_reference' => $validated['deposit_reference'],
            'payment_details' => [
                'deposit_amount' => $validated['amount'] * 100, // Convert to cents
                'deposit_date' => $validated['deposit_date'],
                'bank_name' => $validated['bank_name'],
                'account_holder' => $validated['account_holder'],
                'submitted_at' => now(),
            ],
        ]);

        // Update registration status to pending verification
        $registration->update(['status' => 'payment_verification_pending']);

        // Redirect to pending verification page
        return Inertia::render('Payment/Pending', [
            'registration' => $registration->load(['business.attendees']),
        ])->with('success', 'Bank deposit details submitted. We will verify your payment within 24 hours.');
    }
}
