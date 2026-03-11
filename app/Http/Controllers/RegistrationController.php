<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use App\Models\Country;
use App\Models\Registration;
use App\Services\RegistrationService;
use App\Http\Requests\Registration\PersonalInfoRequest;
use App\Http\Requests\Registration\BusinessInfoRequest;
use App\Http\Requests\Registration\AttendeesRequest;
use App\Http\Requests\Registration\AddonsRequest;
use App\Http\Requests\Registration\CheckoutRequest;
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
    // public function index(Request $request)
    // {
    //     $referenceCode = $request->query('ref');

    //     if ($referenceCode) {
    //         $registration = $this->registrationService->getRegistrationByReference($referenceCode);

    //         if ($registration) {
    //             return Inertia::render('Registration/Resume', [
    //                 'registration' => $registration->load(['business.attendees']),
    //             ]);
    //         }

    //         return Inertia::render('Registration/Entry', [
    //             'error' => 'Invalid reference code. Please start a new registration.',
    //         ]);
    //     }

    //     return Inertia::render('Registration/Entry');
    // }

    public function index(Request $request)
    {
        if ($request->isMethod('get')) {
            // Clear session if explicitly starting fresh
            if ($request->has('new')) {
                $this->registrationService->clearRegistrationSession();
            }
            return Inertia::render('Registration/Entry');
        }

        $request->validate([
            'reference_code' => 'nullable|string|exists:registrations,reference_code',
        ], [
            'reference_code.exists' => 'The reference code is invalid.',
        ]);

        $referenceCode = $request->input('reference_code');
        $registration = $this->registrationService->getRegistrationByReference($referenceCode);

        if ($registration->status === 'completed') {
            return redirect()->route('registration.index')->with('error', 'This registration is already completed.');
        }

        session(['registration_ref' => $registration->reference_code]);
        return redirect()->route($this->getRegistrationStepRoute($registration));
    }

    /**
     * Step 1: Personal Info
     */
    public function create(Request $request)
    {
        // Clear session if user explicitly wants a fresh registration
        if ($request->has('new')) {
            $this->registrationService->clearRegistrationSession();
        }

        $registration = $this->getValidRegistration();

        return Inertia::render('Registration/Step1', compact('registration'));
    }

    public function storePersonalInfo(PersonalInfoRequest $request)
    {
        $validated = $request->validated();
        $existingRegistration = $this->getValidRegistration();

        $registration = $this->registrationService->savePersonalInfo($validated, $existingRegistration);

        session(['registration_ref' => $registration->reference_code]);

        return redirect()->route('registration.step2');
    }

    /**
     * Step 2: Business Info
     */
    public function step2()
    {
        $registration = $this->getValidRegistration();

        if (!$registration || !$this->registrationService->canAccessStep($registration, 2)) {
            return redirect()->route('registration.index');
        }

        return Inertia::render('Registration/Step2', [
            'registration' => $registration->load(['business.industries', 'business.country']),
            'industries' => Industry::orderBy('name')->get(['id', 'name']),
            'countries' => Country::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function storeBusinessInfo(BusinessInfoRequest $request)
    {
        $validated = $request->validated();
        $registration = $this->getValidRegistration();

        if (!$registration) {
            return redirect()->route('registration.index')->withErrors(['error' => 'Session expired. Please start again.']);
        }

        $this->registrationService->updateStep($registration, 2, $validated);

        return redirect()->route('registration.step3');
    }

    /**
     * Step 3: Attendees
     */
    public function step3(Request $request)
    {
        $registration = $this->getValidRegistration();

        if (!$registration || !$this->registrationService->canAccessStep($registration, 3)) {
            return redirect()->route('registration.index');
        }

        $business = $registration->business;
        $availableTicketTypes = $this->registrationService->getTicketTypesByOriginAndMode($business->origin, $business->attendance_mode);

        return Inertia::render('Registration/Step3', [
            'registration' => $registration->load('business.attendees'),
            'availableTicketTypes' => $availableTicketTypes,
            'countries' => Country::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function storeAttendees(AttendeesRequest $request)
    {
        $validated = $request->validated();
        $registration = $this->getValidRegistration();

        if (!$registration) {
            return redirect()->route('registration.index')->withErrors(['error' => 'Session expired. Please start again.']);
        }

        $this->registrationService->updateStep($registration, 3, [
            'ticket_type' => $validated['ticket_type'],
            'attendees' => array_map(function ($attendee) {
                return [
                    'first_name' => $attendee['first_name'],
                    'middle_name' => $attendee['middle_name'] ?? null,
                    'last_name' => $attendee['last_name'],
                    'email' => $attendee['email'],
                    'phone' => $attendee['phone'],
                    'id_number' => $attendee['id_number'],
                    'nationality' => $attendee['nationality'],
                    'additional_details' => [
                        'designation' => $attendee['designation'] ?? null,
                    ],
                ];
            }, $validated['attendees']),
        ]);

        return redirect()->route('registration.step4');
    }

    /**
     * Step 4: Addons
     */
    public function step4(Request $request)
    {
        $registration = $this->getValidRegistration();

        if (!$registration || !$this->registrationService->canAccessStep($registration, 4)) {
            return redirect()->route('registration.index');
        }

        return Inertia::render('Registration/Step4', [
            'registration' => $registration,
        ]);
    }

    public function storeAddons(AddonsRequest $request)
    {
        $validated = $request->validated();
        $registration = $this->getValidRegistration();

        if (!$registration) {
            return redirect()->route('registration.index')->withErrors(['error' => 'Session expired. Please start again.']);
        }

        $this->registrationService->updateStep($registration, 4, [
            'addon_expo' => $validated['addon_expo'] ?? false,
        ]);

        return redirect()->route('registration.step5');
    }

    /**
     * Step 5: Review & Checkout
     */
    public function step5()
    {
        $registration = $this->getValidRegistration();

        if (!$registration || !$this->registrationService->canAccessStep($registration, 5)) {
            return redirect()->route('registration.index');
        }

        $pricing = $this->registrationService->calculateTotalAmount($registration);

        return Inertia::render('Registration/Step5', [
            'registration' => $registration->load(['business.attendees']),
            'pricing' => $pricing,
        ]);
    }

    public function applyReferralCode(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string|exists:agents,referral_code',
        ]);

        $registration = $this->getValidRegistration();

        if (!$registration) {
            return response()->json(['error' => 'Session expired. Please start again.'], 400);
        }

        $agent = $this->registrationService->validateReferralCode($request->referral_code);

        if (!$agent) {
            return response()->json(['error' => 'Invalid referral code.'], 422);
        }

        // Calculate pricing with the referral agent
        $pricing = $this->registrationService->calculateTotalAmount($registration, $agent);

        // Save the referral code to the registration immediately
        $this->registrationService->updateStep($registration, 5, [
            'referral_code' => $request->referral_code,
        ]);

        return response()->json([
            'success' => true,
            'pricing' => $pricing,
            'agent' => [
                'id' => $agent->id,
                'name' => $agent->name,
            ],
        ]);
    }

    public function storeCheckout(CheckoutRequest $request)
    {
        $validated = $request->validated();
        $registration = $this->getValidRegistration();

        if (!$registration) {
            return redirect()->route('registration.index')->withErrors(['error' => 'Session expired. Please start again.']);
        }

        $checkoutData = [];

        if ($validated['referral_code']) {
            $checkoutData['referral_code'] = $validated['referral_code'];
        }

        if ($validated['payment_method']) {
            $checkoutData['payment_method'] = $validated['payment_method'];
        }

        if (!empty($checkoutData)) {
            $this->registrationService->updateStep($registration, 5, $checkoutData);
        }

        // Update registration status and redirect to payment
        $registration->update(['status' => 'payment_pending']);

        if ($validated['payment_method'] === 'paystack') {
            return redirect()->route('payment.paystack', ['ref' => $registration->reference_code]);
        } else {
            return redirect()->route('payment.bank');
        }
    }

    private function getValidRegistration(): ?Registration
    {
        return $this->registrationService->getValidRegistrationFromSession();
    }

    /**
     * Pending Payment Verification Page
     */
    public function pending(Request $request)
    {
        $referenceCode = $request->query('ref');
        $registration = $this->registrationService->getRegistrationByReference($referenceCode);

        if (!$registration ||
            (!in_array($registration->status, ['payment_pending', 'payment_verification_pending']))) {
            return redirect()->route('registration.index');
        }

        $pricing = $this->registrationService->calculateTotalAmount($registration);

        return Inertia::render('Registration/Pending', [
            'registration' => $registration->load(['business.attendees']),
            'pricing' => $pricing,
        ]);
    }

    private function getRegistrationStepRoute(Registration $registration)
    {
        $routes = [
            'registration.step1',
            'registration.step2',
            'registration.step3',
            'registration.step4',
            'registration.step5',
        ];

        return $routes[$registration->current_step - 1] ?? 'registration.index';
    }
}
