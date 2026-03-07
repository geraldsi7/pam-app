<?php

namespace App\Services;

use App\Interface\PaymentInterface;
use App\Models\Registration;

class PaymentContext
{
    private $paymentInterface;

    public function __construct(PaymentInterface $paymentInterface)
    {
        $this->paymentInterface = $paymentInterface;
    }

    public function render(Registration $registration)
    {
        return $this->paymentInterface->render($registration);
    }

    public function process(Registration $registration, $input)
    {
        return $this->paymentInterface->process($registration, $input);
    }
}
