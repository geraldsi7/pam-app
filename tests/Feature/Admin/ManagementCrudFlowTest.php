<?php

namespace Tests\Feature\Admin;

use App\Models\Agent;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ManagementCrudFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_toggle_agent(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post(route('admin.agents.store'), [
            'name' => 'Prime Agent',
            'referral_code' => 'PRIME-AGT',
            'commission_rate' => 12.5,
            'is_active' => true,
        ])->assertRedirect();

        $agent = Agent::first();

        $this->assertNotNull($agent);
        $this->assertDatabaseHas('agents', [
            'name' => 'Prime Agent',
            'referral_code' => 'PRIME-AGT',
            'is_active' => 1,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.agents.toggle', $agent))
            ->assertRedirect();

        $this->assertDatabaseHas('agents', [
            'id' => $agent->id,
            'is_active' => 0,
        ]);
    }

    public function test_admin_can_create_update_and_toggle_user(): void
    {
        $admin = User::factory()->create();
        $registration = Registration::create([
            'reference_code' => 'REG-'.Str::upper(Str::random(8)),
            'email' => 'linked@example.com',
            'personal_info' => ['first_name' => 'Linked', 'last_name' => 'User'],
            'current_step' => 1,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Ops User',
            'email' => 'ops@example.com',
            'password' => 'secretpass123',
            'user_type' => 'operations',
            'is_active' => true,
            'registration_id' => $registration->id,
        ])->assertRedirect();

        $user = User::where('email', 'ops@example.com')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Ops User Updated',
            'email' => 'ops@example.com',
            'password' => '',
            'user_type' => 'support',
            'is_active' => false,
            'registration_id' => $registration->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Ops User Updated',
            'user_type' => 'support',
            'is_active' => 0,
        ]);

        $this->actingAs($admin)->patch(route('admin.users.toggle', $user))->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => 1,
        ]);
    }
}
