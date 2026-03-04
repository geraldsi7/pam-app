<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RegistrationController extends Controller
{
    public function __construct(
        private RegistrationService $registrationService
    ) {}

    /**
     * Step 0: Entry - Fresh registration or resume
     */
    public function index(Request $request)
    {
        $referenceCode = $request->query('ref');

        if ($referenceCode) {
            $registration = $this->registrationService->getRegistrationByReference($referenceCode);

            if ($registration) {
                return Inertia::render('Registration/Resume', [
                    'registration' => $registration->load(['business.attendees']),
                ]);
            }

            return Inertia::render('Registration/Entry', [
                'error' => 'Invalid reference code. Please start a new registration.',
            ]);
        }

        return Inertia::render('Registration/Entry');
    }

    /**
     * Step 1: Personal Info
     */
    public function create()
    {
        return Inertia::render('Registration/Step1');
    }

    public function storePersonalInfo(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:registrations,email',
            'phone' => 'nullable|string|max:20',
            'designation' => 'nullable|string|max:255',
        ]);

        $registration = $this->registrationService->createRegistration($validated);

        return redirect()->route('registration.step2', ['ref' => $registration->reference_code]);
    }

    /**
     * Step 2: Business Info
     */
    public function step2(Request $request)
    {
        $referenceCode = $request->query('ref');
        $registration = $this->registrationService->getRegistrationByReference($referenceCode);

        if (!$registration || $registration->current_step < 1) {
            return redirect()->route('registration.index');
        }

        return Inertia::render('Registration/Step2', [
            'registration' => $registration,
        ]);
    }

    public function storeBusinessInfo(Request $request)
    {
        $validated = $request->validate([
            'ref' => 'required|string|exists:registrations,reference_code',
            'company_name' => 'required|string|max:255',
            'origin' => ['required', Rule::in(['China', 'Outside', 'Online'])],
            'industry' => 'nullable|string|max:255',
            'company_size' => 'nullable|string|max:255',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
        ]);

        $registration = $this->registrationService->getRegistrationByReference($validated['ref']);

        if (!$registration) {
            return back()->withErrors(['ref' => 'Invalid reference code']);
        }

        $this->registrationService->updateStep($registration, 2, [
            'company_name' => $validated['company_name'],
            'origin' => $validated['origin'],
            'business_details' => array_filter([
                'industry' => $validated['industry'] ?? null,
                'company_size' => $validated['company_size'] ?? null,
                'website' => $validated['website'] ?? null,
                'address' => $validated['address'] ?? null,
            ]),
        ]);

        return redirect()->route('registration.step3', ['ref' => $registration->reference_code]);
    }

    /**
     * Step 3: Attendees
     */
    public function step3(Request $request)
    {
        $referenceCode = $request->query('ref');
        $registration = $this->registrationService->getRegistrationByReference($referenceCode);

        if (!$registration || $registration->current_step < 2) {
            return redirect()->route('registration.index');
        }

        $business = $registration->business;
        $availableTicketTypes = $this->registrationService->getTicketTypesByOrigin($business->origin);

        return Inertia::render('Registration/Step3', [
            'registration' => $registration->load('business'),
            'availableTicketTypes' => $availableTicketTypes,
        ]);
    }

    public function storeAttendees(Request $request)
    {
        $validated = $request->validate([
            'ref' => 'required|string|exists:registrations,reference_code',
            'ticket_type' => ['required', Rule::in(['1*', '3*'])],
            'attendees' => 'required|array|min:1',
            'attendees.*.first_name' => 'required|string|max:255',
            'attendees.*.last_name' => 'required|string|max:255',
            'attendees.*.email' => 'required|email',
            'attendees.*.phone' => 'nullable|string|max:20',
            'attendees.*.designation' => 'nullable|string|max:255',
        ]);

        $registration = $this->registrationService->getRegistrationByReference($validated['ref']);

        if (!$registration) {
            return back()->withErrors(['ref' => 'Invalid reference code']);
        }

        $business = $registration->business;
        $availableTicketTypes = $this->registrationService->getTicketTypesByOrigin($business->origin);

        if (!in_array($validated['ticket_type'], $availableTicketTypes)) {
            return back()->withErrors(['ticket_type' => 'Invalid ticket type for your origin']);
        }

        $maxAttendees = $validated['ticket_type'] === '1*' ? 1 : 3;
        if (count($validated['attendees']) > $maxAttendees) {
            return back()->withErrors(['attendees' => "Maximum {$maxAttendees} attendees allowed for this ticket type"]);
        }

        $this->registrationService->updateStep($registration, 3, [
            'ticket_type' => $validated['ticket_type'],
            'attendees' => array_map(function ($attendee) {
                return array_filter([
                    'first_name' => $attendee['first_name'],
                    'last_name' => $attendee['last_name'],
                    'email' => $attendee['email'],
                    'phone' => $attendee['phone'] ?? null,
                    'additional_details' => array_filter([
                        'designation' => $attendee['designation'] ?? null,
                    ]),
                ]);
            }, $validated['attendees']),
        ]);

        return redirect()->route('registration.step4', ['ref' => $registration->reference_code]);
    }

    /**
     * Step 4: Addons
     */
    public function step4(Request $request)
    {
        $referenceCode = $request->query('ref');
        $registration = $this->registrationService->getRegistrationByReference($referenceCode);

        if (!$registration || $registration->current_step < 3) {
            return redirect()->route('registration.index');
        }

        return Inertia::render('Registration/Step4', [
            'registration' => $registration->load(['business.attendees']),
        ]);
    }

    public function storeAddons(Request $request)
    {
        $validated = $request->validate([
            'ref' => 'required|string|exists:registrations,reference_code',
            'addon_expo' => 'boolean',
        ]);

        $registration = $this->registrationService->getRegistrationByReference($validated['ref']);

        if (!$registration) {
            return back()->withErrors(['ref' => 'Invalid reference code']);
        }

        $this->registrationService->updateStep($registration, 4, [
            'addon_expo' => $validated['addon_expo'] ?? false,
        ]);

        return redirect()->route('registration.step5', ['ref' => $registration->reference_code]);
    }

    /**
     * Step 5: Review & Checkout
     */
    public function step5(Request $request)
    {
        $referenceCode = $request->query('ref');
        $registration = $this->registrationService->getRegistrationByReference($referenceCode);

        if (!$registration || $registration->current_step < 4) {
            return redirect()->route('registration.index');
        }

        $pricing = $this->registrationService->calculateTotalAmount($registration);

        return Inertia::render('Registration/Step5', [
            'registration' => $registration->load(['business.attendees']),
            'pricing' => $pricing,
        ]);
    }

    public function storeCheckout(Request $request)
    {
        $validated = $request->validate([
            'ref' => 'required|string|exists:registrations,reference_code',
            'referral_code' => 'nullable|string|exists:agents,referral_code',
            'payment_method' => ['required', Rule::in(['paystack', 'bank_deposit'])],
        ]);

        $registration = $this->registrationService->getRegistrationByReference($validated['ref']);

        if (!$registration) {
            return back()->withErrors(['ref' => 'Invalid reference code']);
        }

        if ($validated['referral_code']) {
            $this->registrationService->updateStep($registration, 5, [
                'referral_code' => $validated['referral_code'],
            ]);
        }

        // Update registration status and redirect to payment
        $registration->update(['status' => 'payment_pending']);

        if ($validated['payment_method'] === 'paystack') {
            return redirect()->route('payment.paystack', ['ref' => $registration->reference_code]);
        } else {
            return redirect()->route('payment.bank', ['ref' => $registration->reference_code]);
        }
    }
}
