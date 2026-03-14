<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegistrationDecisionRequest;
use App\Models\Payment;
use App\Models\Registration;
use App\Services\AdminActivityLogger;
use App\Services\PostPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationManagementController extends Controller
{
    public function __construct(
        private PostPaymentService $postPaymentService,
        private AdminActivityLogger $activityLogger,
    ) {}

    public function index(Request $request): Response
    {
        $query = Registration::query()
            ->with([
                'business:id,registration_id,company_name,attendance_mode,ticket_type,origin',
                'agent:id,name,referral_code',
                'payments:id,registration_id,method,status,amount,transaction_reference,created_at',
            ])
            ->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($reviewStatus = $request->string('review_status')->toString()) {
            $query->where('admin_review_status', $reviewStatus);
        }

        if ($mode = $request->string('mode')->toString()) {
            $query->whereHas('business', fn ($builder) => $builder->where('attendance_mode', $mode));
        }

        if ($paymentMethod = $request->string('payment_method')->toString()) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('reference_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('business', fn ($business) => $business->where('company_name', 'like', "%{$search}%"));
            });
        }

        if ($from = $request->date('from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->date('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $registrations = $query->paginate(12)->withQueryString();

        return Inertia::render('Admin/Registrations/Index', [
            'registrations' => $registrations,
            'filters' => [
                'status' => $request->input('status'),
                'review_status' => $request->input('review_status'),
                'mode' => $request->input('mode'),
                'payment_method' => $request->input('payment_method'),
                'search' => $request->input('search'),
                'from' => $request->input('from'),
                'to' => $request->input('to'),
            ],
        ]);
    }

    public function show(Registration $registration): Response
    {
        $registration->load([
            'business.country:id,name',
            'business.industries:id,name',
            'business.attendees',
            'agent',
            'payments' => fn ($query) => $query->latest(),
            'adminReviewer:id,name,email',
        ]);

        return Inertia::render('Admin/Registrations/Show', [
            'registration' => $registration,
        ]);
    }

    public function approve(RegistrationDecisionRequest $request, Registration $registration): RedirectResponse
    {
        $notes = $request->validated('notes');
        $actor = $request->user();
        $payment = $registration->payments()->latest()->first();

        if (
            $payment &&
            $payment->method === 'bank_deposit' &&
            $payment->status === 'pending' &&
            $registration->status !== Registration::STATUS_COMPLETED
        ) {
            $this->postPaymentService->verifyBankDeposit($registration, $payment);
        } elseif ($registration->status !== Registration::STATUS_COMPLETED) {
            $registration->update(['status' => Registration::STATUS_COMPLETED]);
        }

        $registration->update([
            'admin_review_status' => 'approved',
            'admin_review_notes' => $notes,
            'admin_reviewed_at' => now(),
            'admin_reviewed_by' => $actor?->id,
        ]);

        $this->activityLogger->log(
            action: 'registration.approved',
            subject: $registration,
            actorId: $actor?->id,
            description: 'Registration approved from admin portal.',
            metadata: ['notes' => $notes]
        );

        return back()->with('success', 'Registration approved successfully.');
    }

    public function reject(RegistrationDecisionRequest $request, Registration $registration): RedirectResponse
    {
        $notes = $request->validated('notes');
        $actor = $request->user();

        /** @var Payment|null $payment */
        $payment = $registration->payments()->latest()->first();
        if ($payment && $payment->status === 'pending') {
            $this->postPaymentService->handleFailedPayment($registration, $payment);
        } else {
            $registration->update(['status' => Registration::STATUS_CANCELLED]);
        }

        $registration->update([
            'admin_review_status' => 'rejected',
            'admin_review_notes' => $notes,
            'admin_reviewed_at' => now(),
            'admin_reviewed_by' => $actor?->id,
        ]);

        $this->activityLogger->log(
            action: 'registration.rejected',
            subject: $registration,
            actorId: $actor?->id,
            description: 'Registration rejected from admin portal.',
            metadata: ['notes' => $notes]
        );

        return back()->with('success', 'Registration rejected.');
    }
}
