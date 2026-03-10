<?php

namespace App\Services\Pricing;

class OutsideChinaOneStarStrategy implements TicketPricingStrategy
{
    public function getBasePrice(): int
    {
        return 320000; // $3,200 in cents
    }

    public function getReferralPrice(): int
    {
        return 290000; // $2,900 in cents (display - discount)
    }

    public function getAgentCommission(): int
    {
        return 40000; // $400 in cents
    }

    public function getCompanyNet(): int
    {
        return 250000; // $2,500 in cents
    }

    public function getTicketType(): string
    {
        return '1*';
    }
}