<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function __construct(
        private RegistrationService $registrationService
    ) {}

    /**
     * Paystack Payment Page
     */
    public function paystack(Request $request)
    {
        $referenceCode = $request->query('ref');
        $registration = $this->registrationService->getRegistrationByReference($referenceCode);

        if (!$registration || $registration->status !== 'payment_pending') {
            return redirect()->route('registration.index');
        }

        $pricing = $this->registrationService->calculateTotalAmount($registration);

        return Inertia::render('Payment/Paystack', [
            'registration' => $registration->load(['business.attendees']),
            'pricing' => $pricing,
        ]);
    }

    /**
     * Process Paystack Payment
     */
    public function processPaystack(Request $request)
    {
        $validated = $request->validate([
            'ref' => 'required|string|exists:registrations,reference_code',
            'paystack_reference' => 'required|string',
        ]);

        $registration = $this->registrationService->getRegistrationByReference($validated['ref']);

        if (!$registration) {
            return response()->json(['error' => 'Invalid registration'], 400);
        }

        // Here you would verify the Paystack payment
        // For now, we'll assume it's successful
        $pricing = $this->registrationService->calculateTotalAmount($registration);

        // Create payment record
        $payment = Payment::create([
            'id' => Str::uuid(),
            'registration_id' => $registration->id,
            'method' => 'paystack',
            'status' => 'success',
            'amount' => $pricing['total'],
            'transaction_reference' => $validated['paystack_reference'],
            'payment_details' => [
                'verified_at' => now(),
            ],
        ]);

        // Handle post-payment logic
        app(\App\Services\PostPaymentService::class)->handleSuccessfulPayment($registration, $payment);

        return response()->json([
            'success' => true,
            'message' => 'Payment successful',
            'redirect' => route('registration.success', ['ref' => $registration->reference_code]),
        ]);
    }

    /**
     * Bank Deposit Page
     */
    public function bankDeposit(Request $request)
    {
        $referenceCode = $request->query('ref');
        $registration = $this->registrationService->getRegistrationByReference($referenceCode);

        if (!$registration || $registration->status !== 'payment_pending') {
            return redirect()->route('registration.index');
        }

        $pricing = $this->registrationService->calculateTotalAmount($registration);

        return Inertia::render('Payment/BankDeposit', [
            'registration' => $registration->load(['business.attendees']),
            'pricing' => $pricing,
            'bankDetails' => [
                'bank_name' => 'Example Bank',
                'account_name' => 'FDI Summit Organizers',
                'account_number' => '1234567890',
                'swift_code' => 'EXAMUS33',
            ],
        ]);
    }

    /**
     * Process Bank Deposit
     */
    public function processBankDeposit(Request $request)
    {
        $validated = $request->validate([
            'ref' => 'required|string|exists:registrations,reference_code',
            'deposit_reference' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'deposit_date' => 'required|date',
            'bank_name' => 'required|string',
            'account_holder' => 'required|string',
        ]);

        $registration = $this->registrationService->getRegistrationByReference($validated['ref']);

        if (!$registration) {
            return response()->json(['error' => 'Invalid registration'], 400);
        }

        $pricing = $this->registrationService->calculateTotalAmount($registration);

        // For bank deposits, we set status to 'pending' until manually verified
        $payment = Payment::create([
            'id' => Str::uuid(),
            'registration_id' => $registration->id,
            'method' => 'bank_deposit',
            'status' => 'pending', // Requires manual verification
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

        // Send confirmation email
        app(\App\Services\PostPaymentService::class)->sendReferenceCodeEmail($registration);

        return response()->json([
            'success' => true,
            'message' => 'Bank deposit details submitted. We will verify your payment within 24 hours.',
            'redirect' => route('registration.pending', ['ref' => $registration->reference_code]),
        ]);
    }

    /**
     * Payment Success Page
     */
    public function success(Request $request)
    {
        $referenceCode = $request->query('ref');
        $registration = $this->registrationService->getRegistrationByReference($referenceCode);

        if (!$registration || $registration->status !== 'completed') {
            return redirect()->route('registration.index');
        }

        return Inertia::render('Payment/Success', [
            'registration' => $registration->load(['business.attendees']),
        ]);
    }

    /**
     * Payment Pending Verification Page
     */
    public function pending(Request $request)
    {
        $referenceCode = $request->query('ref');
        $registration = $this->registrationService->getRegistrationByReference($referenceCode);

        if (!$registration || $registration->status !== 'payment_verification_pending') {
            return redirect()->route('registration.index');
        }

        return Inertia::render('Payment/Pending', [
            'registration' => $registration->load(['business.attendees']),
        ]);
    }
}
