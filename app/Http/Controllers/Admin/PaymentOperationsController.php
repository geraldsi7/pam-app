<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentDecisionRequest;
use App\Models\Payment;
use App\Services\AdminActivityLogger;
use App\Services\PostPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentOperationsController extends Controller
{
    public function __construct(
        private PostPaymentService $postPaymentService,
        private AdminActivityLogger $activityLogger,
    ) {}

    public function index(Request $request): Response
    {
        $query = Payment::query()
            ->with([
                'registration:id,reference_code,email,status,payment_method,admin_review_status',
                'registration.business:id,registration_id,company_name,ticket_type',
            ])
            ->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($method = $request->string('method')->toString()) {
            $query->where('method', $method);
        }

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('transaction_reference', 'like', "%{$search}%")
                    ->orWhereHas('registration', fn ($registration) => $registration
                        ->where('reference_code', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $query->paginate(12)->withQueryString(),
            'filters' => [
                'status' => $request->input('status'),
                'method' => $request->input('method'),
                'search' => $request->input('search'),
            ],
        ]);
    }

    public function verify(PaymentDecisionRequest $request, Payment $payment): RedirectResponse
    {
        $payment->load('registration');
        $registration = $payment->registration;

        if ($payment->status !== 'success') {
            if ($payment->method === 'bank_deposit') {
                $this->postPaymentService->verifyBankDeposit($registration, $payment);
            } else {
                $payment->update(['status' => 'success']);
                $this->postPaymentService->handleSuccessfulPayment($registration, $payment);
            }
        }

        $registration->update([
            'admin_review_status' => 'approved',
            'admin_review_notes' => $request->validated('notes'),
            'admin_reviewed_at' => now(),
            'admin_reviewed_by' => $request->user()?->id,
        ]);

        $this->activityLogger->log(
            action: 'payment.verified',
            subject: $payment,
            actorId: $request->user()?->id,
            description: 'Payment verified from admin portal.',
            metadata: ['notes' => $request->validated('notes')]
        );

        return back()->with('success', 'Payment verified and registration completed.');
    }

    public function reject(PaymentDecisionRequest $request, Payment $payment): RedirectResponse
    {
        $payment->load('registration');
        $registration = $payment->registration;

        $this->postPaymentService->handleFailedPayment($registration, $payment);

        $registration->update([
            'admin_review_status' => 'rejected',
            'admin_review_notes' => $request->validated('notes'),
            'admin_reviewed_at' => now(),
            'admin_reviewed_by' => $request->user()?->id,
        ]);

        $this->activityLogger->log(
            action: 'payment.rejected',
            subject: $payment,
            actorId: $request->user()?->id,
            description: 'Payment rejected from admin portal.',
            metadata: ['notes' => $request->validated('notes')]
        );

        return back()->with('success', 'Payment rejected.');
    }
}
