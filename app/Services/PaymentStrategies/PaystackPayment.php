<?php

namespace App\Services\PaymentStrategies;

use App\Interface\PaymentInterface;
use App\Models\Registration;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use App\Services\RegistrationService;

class PaystackPayment implements PaymentInterface
{
    private $registrationService;

    public function __construct(
    ) {
        $this->registrationService = new RegistrationService();
    }
    public function render(Registration $registration)
    {
        $pricing = $this->registrationService->calculateTotalAmount($registration);

        return Inertia::render('Payment/Paystack', [
            'registration' => $registration,
            'pricing' => $pricing,
        ]);
    }

    public function process(Registration $registration, $input): InertiaResponse
    {
        // For now, redirect to success page
        // In a real implementation, this would process the Paystack payment
        return Inertia::render('Payment/Success', [
            'registration' => $registration->load(['business.attendees']),
        ])->with('success', 'Payment processed successfully');
    }
}
