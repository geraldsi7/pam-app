<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Registration;
use App\Services\Pricing\PricingCalculator;
use App\Services\Pricing\SingleTicketStrategy;
use App\Services\Pricing\GroupTicketStrategy;
use Illuminate\Support\Str;

class RegistrationService
{
    public function generateReferenceCode(): string
    {
        do {
            $code = 'REG-' . strtoupper(Str::random(8));
        } while (Registration::where('reference_code', $code)->exists());

        return $code;
    }

    public function createRegistration(array $personalInfo): Registration
    {
        $registration = Registration::create([
            'id' => Str::uuid(),
            'reference_code' => $this->generateReferenceCode(),
            'email' => $personalInfo['email'],
            'personal_info' => $personalInfo,
            'current_step' => 1,
        ]);

        return $registration;
    }

    public function updateStep(Registration $registration, int $step, array $data = []): void
    {
        $updateData = ['current_step' => $step];

        if ($step === 2) {
            // Business info step
            $registration->business()->create([
                'id' => Str::uuid(),
                'company_name' => $data['company_name'],
                'origin' => $data['origin'],
                'business_details' => $data['business_details'] ?? [],
            ]);
        } elseif ($step === 3) {
            // Attendees step
            $business = $registration->business;
            $business->update(['ticket_type' => $data['ticket_type']]);

            foreach ($data['attendees'] as $attendeeData) {
                $business->attendees()->create([
                    'id' => Str::uuid(),
                    'first_name' => $attendeeData['first_name'],
                    'last_name' => $attendeeData['last_name'],
                    'email' => $attendeeData['email'],
                    'phone' => $attendeeData['phone'] ?? null,
                    'additional_details' => $attendeeData['additional_details'] ?? [],
                ]);
            }
        } elseif ($step === 4) {
            // Addons step
            $registration->update(['addon_expo' => $data['addon_expo'] ?? false]);
        } elseif ($step === 5) {
            // Checkout step
            if (isset($data['referral_code'])) {
                $registration->update(['referral_code' => $data['referral_code']]);
                $agent = Agent::where('referral_code', $data['referral_code'])->first();
                if ($agent) {
                    $registration->update(['agent_id' => $agent->id]);
                }
            }
        }

        $registration->update($updateData);

        // Send reference code email after step 1
        if ($step === 1) {
            app(PostPaymentService::class)->sendReferenceCodeEmail($registration);
        }
    }

    public function getTicketTypesByOrigin(string $origin): array
    {
        return match ($origin) {
            'China' => ['3*'],
            'Outside' => ['1*', '3*'],
            'Online' => ['1*'],
            default => [],
        };
    }

    public function calculateTotalAmount(Registration $registration): array
    {
        $business = $registration->business;
        if (!$business || !$business->ticket_type) {
            return [
                'ticket_price' => 0,
                'addon_price' => 0,
                'agent_commission' => 0,
                'company_net' => 0,
                'total' => 0,
            ];
        }

        $strategy = match ($business->ticket_type) {
            '1*' => new SingleTicketStrategy(),
            '3*' => new GroupTicketStrategy(),
            default => throw new \InvalidArgumentException('Invalid ticket type'),
        };

        $calculator = new PricingCalculator($strategy);
        $hasReferral = $registration->agent_id !== null;
        $calculator->setReferral($hasReferral);

        $ticketPrice = $calculator->getTicketPrice();
        $addonPrice = $registration->addon_expo ? $calculator->addExpoAddon() : 0;
        $agentCommission = $calculator->getAgentCommission();
        $companyNet = $calculator->getCompanyNet() + $addonPrice;

        return [
            'ticket_price' => $ticketPrice,
            'addon_price' => $addonPrice,
            'agent_commission' => $agentCommission,
            'company_net' => $companyNet,
            'total' => $ticketPrice + $addonPrice,
        ];
    }

    public function getRegistrationByReference(string $referenceCode): ?Registration
    {
        return Registration::where('reference_code', $referenceCode)->first();
    }

    public function completeRegistration(Registration $registration): void
    {
        $registration->update(['status' => 'completed']);
    }
}
