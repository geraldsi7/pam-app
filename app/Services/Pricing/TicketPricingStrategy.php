<?php

namespace App\Services\Pricing;

interface TicketPricingStrategy
{
    /**
     * Calculate the base price for a ticket type
     * @return int Price in cents
     */
    public function getBasePrice(): int;

    /**
     * Calculate the discounted price when referral code is applied
     * @return int Price in cents
     */
    public function getReferralPrice(): int;

    /**
     * Get the agent commission for this ticket type
     * @return int Commission in cents
     */
    public function getAgentCommission(): int;

    /**
     * Get the company net profit after agent commission
     * @return int Net profit in cents
     */
    public function getCompanyNet(): int;

    /**
     * Get the ticket type identifier
     */
    public function getTicketType(): string;
}
