<?php

namespace Tests\Feature\Admin;

use App\Models\Business;
use App\Models\Country;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentVerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_verify_pending_bank_deposit_payment(): void
    {
        $admin = User::factory()->create();
        [$registration, $payment] = $this->createPendingBankPayment();

        $response = $this->actingAs($admin)->post(route('admin.payments.verify', $payment), [
            'notes' => 'Bank statement reconciled',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'status' => 'completed',
            'admin_review_status' => 'approved',
        ]);

        $this->assertDatabaseHas('admin_activity_logs', [
            'action' => 'payment.verified',
        ]);
    }

    /**
     * @return array{Registration, Payment}
     */
    private function createPendingBankPayment(): array
    {
        $country = Country::create([
            'name' => 'Ghana',
            'code' => 'GH',
        ]);

        $registration = Registration::create([
            'reference_code' => 'REG-TEST-0001',
            'email' => 'registrant@example.com',
            'personal_info' => [
                'first_name' => 'Test',
                'last_name' => 'Registrant',
            ],
            'current_step' => 5,
            'status' => 'payment_pending',
            'payment_method' => 'bank_deposit',
            'admin_review_status' => 'pending',
        ]);

        Business::create([
            'registration_id' => $registration->id,
            'company_name' => 'Acme Minerals',
            'origin' => 'Outside',
            'ticket_type' => '1*',
            'country_id' => $country->id,
            'email' => 'ops@acme.example',
            'attendance_mode' => 'in_person',
        ]);

        $payment = Payment::create([
            'registration_id' => $registration->id,
            'method' => 'bank_deposit',
            'status' => 'pending',
            'amount' => 1200.00,
            'transaction_reference' => 'BANK-REF-001',
            'payment_details' => ['deposit_date' => now()->toDateString()],
        ]);

        return [$registration, $payment];
    }
}
