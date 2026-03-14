<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Agent;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $kpis = [
            'registrations_total' => Registration::count(),
            'registrations_pending' => Registration::where('status', Registration::STATUS_PAYMENT_PENDING)->count(),
            'registrations_completed' => Registration::where('status', Registration::STATUS_COMPLETED)->count(),
            'payments_pending_verification' => Payment::where('method', 'bank_deposit')->where('status', 'pending')->count(),
            'agents_total' => Agent::count(),
            'users_total' => User::count(),
        ];

        $pendingRegistrations = Registration::query()
            ->with(['business:id,registration_id,company_name,attendance_mode,ticket_type', 'agent:id,name'])
            ->where('status', Registration::STATUS_PAYMENT_PENDING)
            ->latest()
            ->limit(8)
            ->get();

        $pendingPayments = Payment::query()
            ->with(['registration:id,reference_code,email,status,payment_method', 'registration.business:id,registration_id,company_name'])
            ->where('method', 'bank_deposit')
            ->where('status', 'pending')
            ->latest()
            ->limit(8)
            ->get();

        $recentActivity = AdminActivityLog::query()
            ->with('actor:id,name,email')
            ->latest()
            ->limit(10)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'kpis' => $kpis,
            'pendingRegistrations' => $pendingRegistrations,
            'pendingPayments' => $pendingPayments,
            'recentActivity' => $recentActivity,
        ]);
    }
}
