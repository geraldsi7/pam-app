<?php

namespace App\Services\Pricing;

class GroupTicketStrategy implements TicketPricingStrategy
{
    public function getBasePrice(): int
    {
        return 750000; // $7,500 in cents
    }

    public function getReferralPrice(): int
    {
        return 690000; // $6,900 in cents
    }

    public function getAgentCommission(): int
    {
        return 90000; // $900 in cents
    }

    public function getCompanyNet(): int
    {
        return 600000; // $6,000 in cents
    }

    public function getTicketType(): string
    {
        return '3*';
    }
}
