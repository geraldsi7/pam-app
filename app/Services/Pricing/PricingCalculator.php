<?php

namespace App\Services\Pricing;

class PricingCalculator
{
    private TicketPricingStrategy $strategy;
    private bool $hasReferral = false;

    public function __construct(TicketPricingStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function setReferral(bool $hasReferral): self
    {
        $this->hasReferral = $hasReferral;
        return $this;
    }

    public function getTicketPrice(): int
    {
        return $this->hasReferral ? $this->strategy->getReferralPrice() : $this->strategy->getBasePrice();
    }

    public function getAgentCommission(): int
    {
        return $this->hasReferral ? $this->strategy->getAgentCommission() : 0;
    }

    public function getCompanyNet(): int
    {
        return $this->hasReferral ? $this->strategy->getCompanyNet() : $this->strategy->getBasePrice();
    }

    public function getTicketType(): string
    {
        return $this->strategy->getTicketType();
    }

    public function addExpoAddon(): int
    {
        return 50000; // $500 in cents
    }
}
