<?php

namespace App\Services\Pricing;

class OnlineStrategy implements TicketPricingStrategy
{
    public function getBasePrice(): int
    {
        return 9500; // $95 in cents
    }

    public function getReferralPrice(): int
    {
        return 8000; // $80 in cents (display - discount)
    }

    public function getAgentCommission(): int
    {
        return 1200; // $12 in cents
    }

    public function getCompanyNet(): int
    {
        return 6800; // $68 in cents
    }

    public function getTicketType(): string
    {
        return '1*';
    }
}