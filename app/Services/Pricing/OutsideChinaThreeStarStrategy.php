<?php

namespace App\Services\Pricing;

class OutsideChinaThreeStarStrategy implements TicketPricingStrategy
{
    public function getBasePrice(): int
    {
        return 850000; // $8,500 in cents
    }

    public function getReferralPrice(): int
    {
        return 820000; // $8,200 in cents (display - discount)
    }

    public function getAgentCommission(): int
    {
        return 70000; // $700 in cents
    }

    public function getCompanyNet(): int
    {
        return 750000; // $7,500 in cents
    }

    public function getTicketType(): string
    {
        return '3*';
    }
}