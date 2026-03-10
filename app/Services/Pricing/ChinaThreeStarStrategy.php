<?php

namespace App\Services\Pricing;

class ChinaThreeStarStrategy implements TicketPricingStrategy
{
    public function getBasePrice(): int
    {
        return 30000; // $300 in cents
    }

    public function getReferralPrice(): int
    {
        return 26000; // $260 in cents (display - discount)
    }

    public function getAgentCommission(): int
    {
        return 4000; // $40 in cents
    }

    public function getCompanyNet(): int
    {
        return 22000; // $220 in cents
    }

    public function getTicketType(): string
    {
        return '3*';
    }
}