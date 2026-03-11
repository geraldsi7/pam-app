<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Registration;
use App\Services\Pricing\PricingCalculator;
use App\Services\Pricing\TicketPricingStrategy;
use App\Services\Pricing\OutsideChinaThreeStarStrategy;
use App\Services\Pricing\OutsideChinaOneStarStrategy;
use App\Services\Pricing\ChinaThreeStarStrategy;
use App\Services\Pricing\OnlineStrategy;
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
            'reference_code' => $this->generateReferenceCode(),
            'email' => $personalInfo['email'],
            'personal_info' => $personalInfo,
            'current_step' => 1,
        ]);

        return $registration;
    }

    public function savePersonalInfo(array $personalInfo, ?Registration $registration = null): Registration
    {
        if ($registration) {
            $registration->update([
                'email' => $personalInfo['email'],
                'personal_info' => $personalInfo,
                // We keep the current_step if it's already higher, or set it to 1
                'current_step' => max($registration->current_step, 1),
            ]);
            return $registration;
        }

        return Registration::create([
            'reference_code' => $this->generateReferenceCode(),
            'email' => $personalInfo['email'],
            'personal_info' => $personalInfo,
            'current_step' => 1,
        ]);
    }

    public function updateStep(Registration $registration, int $step, array $data = []): void
    {
        $this->validateStepTransition($registration, $step);

        $updateData = ['current_step' => $step];

        switch ($step) {
            case 2:
                $this->handleBusinessInfo($registration, $data);
                break;
            case 3:
                $this->handleAttendees($registration, $data);
                break;
            case 4:
                $this->handleAddons($registration, $data);
                break;
            case 5:
                $this->handleCheckout($registration, $data);
                break;
        }

        $registration->update($updateData);

        // Send reference code email after step 1
        if ($step === 1) {
            app(PostPaymentService::class)->sendReferenceCodeEmail($registration);
        }
    }

    protected function validateStepTransition(Registration $registration, int $step): void
    {
        if ($step > 1 && $registration->current_step < ($step - 1)) {
            throw new \InvalidArgumentException("Cannot advance to step {$step} from current step {$registration->current_step}");
        }
    }

    protected function handleBusinessInfo(Registration $registration, array $data): void
    {
        // Derive origin from country
        $origin = $this->deriveOriginFromCountry($data['country_id']);

        $business = $registration->business()->updateOrCreate(
            ['registration_id' => $registration->id],
            [
                'company_name' => $data['company_name'],
                'country_id' => $data['country_id'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'attendance_mode' => $data['attendance_mode'],
                'origin' => $origin,
                'business_details' => [
                    'company_size' => $data['company_size'] ?? null,
                    'website' => $data['website'] ?? null,
                    'street_address' => $data['street_address'] ?? null,
                ],
            ]
        );

        if (isset($data['industries']) && is_array($data['industries'])) {
            $business->industries()->sync($data['industries']);
        }
    }

    protected function handleAttendees(Registration $registration, array $data): void
    {
        $business = $registration->business;
        if (!$business) {
            throw new \InvalidArgumentException('Business information must be completed before adding attendees');
        }

        $business->update(['ticket_type' => $data['ticket_type']]);

        // Remove existing attendees to handle updates
        $business->attendees()->delete();

        foreach ($data['attendees'] as $attendeeData) {
            $business->attendees()->create([
                'first_name' => $attendeeData['first_name'],
                'middle_name' => $attendeeData['middle_name'] ?? null,
                'last_name' => $attendeeData['last_name'],
                'id_number' => $attendeeData['id_number'],
                'nationality' => $attendeeData['nationality'],
                'email' => $attendeeData['email'],
                'phone' => $attendeeData['phone'] ?? null,
                'additional_details' => $attendeeData['additional_details'] ?? [],
            ]);
        }
    }

    protected function handleAddons(Registration $registration, array $data): void
    {
        $registration->update(['addon_expo' => $data['addon_expo'] ?? false]);
    }

    protected function handleCheckout(Registration $registration, array $data): void
    {
        $updateData = [];

        if (isset($data['referral_code'])) {
            $updateData['referral_code'] = $data['referral_code'];
            $agent = Agent::where('referral_code', $data['referral_code'])->first();
            if ($agent) {
                $updateData['agent_id'] = $agent->id;
            }
        }

        if (isset($data['payment_method'])) {
            $updateData['payment_method'] = $data['payment_method'];
        }

        if (!empty($updateData)) {
            $registration->update($updateData);
        }
    }

    public function getTicketTypesByOriginAndMode(string $origin, string $attendanceMode): array
    {
        // For online attendance, only 1* is available regardless of origin
        if ($attendanceMode === 'online') {
            return ['1*'];
        }

        // For in-person attendance, availability depends on origin
        return match ($origin) {
            'China' => ['3*'],
            'Outside' => ['1*', '3*'],
            default => [],
        };
    }

    public function getTicketTypesByOrigin(string $origin): array
    {
        // Keep backward compatibility - assume in_person for origin-based calls
        return $this->getTicketTypesByOriginAndMode($origin, 'in_person');
    }

    protected function getPricingStrategy(string $origin, string $attendanceMode, string $ticketType): TicketPricingStrategy
    {
        // Handle online attendance mode first
        if ($attendanceMode === 'online') {
            return new OnlineStrategy();
        }

        // Handle in-person attendance based on origin and ticket type
        return match ([$origin, $ticketType]) {
            ['Outside', '3*'] => new OutsideChinaThreeStarStrategy(),
            ['Outside', '1*'] => new OutsideChinaOneStarStrategy(),
            ['China', '3*'] => new ChinaThreeStarStrategy(),
            default => throw new \InvalidArgumentException("Invalid combination: origin={$origin}, attendance_mode={$attendanceMode}, ticket_type={$ticketType}"),
        };
    }

    protected function deriveOriginFromCountry($countryId): string
    {
        $country = \App\Models\Country::find($countryId);
        return $country && $country->name === 'China' ? 'China' : 'Outside';
    }

    public function getTicketTypesByAttendanceMode(string $attendanceMode): array
    {
        return match ($attendanceMode) {
            'in_person' => ['1*', '3*'],
            'online' => ['1*'],
            default => [],
        };
    }

    public function validateReferralCode(string $referralCode): ?Agent
    {
        return Agent::where('referral_code', $referralCode)->first();
    }

    public function calculateTotalAmount(Registration $registration, ?Agent $agent = null): array
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

        $strategy = $this->getPricingStrategy($business->origin, $business->attendance_mode, $business->ticket_type);

        $calculator = new PricingCalculator($strategy);
        $hasReferral = $agent !== null || $registration->agent_id !== null;
        $calculator->setReferral($hasReferral);

        $ticketPrice = $calculator->getTicketPrice();
        $addonPrice = $registration->addon_expo ? $calculator->addExpoAddon() : 0;
        $agentCommission = $calculator->getAgentCommission();
        $companyNet = $calculator->getCompanyNet() + $addonPrice;
        $discount = $calculator->getDiscount();

        return [
            'ticket_price' => $ticketPrice,
            'addon_price' => $addonPrice,
            'agent_commission' => $agentCommission,
            'discount' => $discount,
            'company_net' => $companyNet,
            'total' => $ticketPrice + $addonPrice,
        ];
    }

    public function getRegistrationByReference(string $referenceCode): ?Registration
    {
        return Registration::where('reference_code', $referenceCode)->first();
    }

    public function canAccessStep(Registration $registration, int $step): bool
    {
        // Allow access to step 1 for new registrations
        if ($step === 1) {
            return true;
        }

        // For steps 2-5, user must have completed previous steps
        return $registration->current_step >= ($step - 1);
    }

    public function getValidRegistrationFromSession(): ?Registration
    {
        $referenceCode = session('registration_ref');
        if (!$referenceCode) {
            return null;
        }

        $registration = $this->getRegistrationByReference($referenceCode);
        if (!$registration || $registration->status === 'completed') {
            return null;
        }

        return $registration;
    }

    public function clearRegistrationSession(): void
    {
        session()->forget('registration_ref');
    }

    public function completeRegistration(Registration $registration): void
    {
        $registration->update(['status' => 'completed']);
    }
}
