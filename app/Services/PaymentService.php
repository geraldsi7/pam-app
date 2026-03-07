<?php

namespace App\Services;

use App\Services\PaymentStrategies\BankDepositPayment;
use App\Services\PaymentStrategies\PaystackPayment;
use App\Interface\PaymentInterface; 

class PaymentService
{
    private function strategyMap(): array
    {
        return [
            'bank_deposit'   => BankDepositPayment::class,
            'paystack'       => PaystackPayment::class,

        ];
    }

    public function getStrategy(string $method): PaymentInterface
    {
        $strategyClass = $this->strategyMap()[$method];

        return new $strategyClass();
    }

    private function getPaymentContext($method): PaymentContext
    {
        $strategy = $this->getStrategy($method);
        return new PaymentContext($strategy);
    }

    public function render($registration)
    {
        $context = $this->getPaymentContext($registration->payment_method);
        return $context->render($registration);
    }

    public function process($registration, $input)
    {
        $context = $this->getPaymentContext($registration->payment_method);
        return $context->process($registration, $input);
    }
}
