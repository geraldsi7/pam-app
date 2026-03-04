<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agents = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@pamagency.com',
                'password' => Hash::make('password123'),
                'referral_code' => 'JSMITH2024',
                'commission_rate' => 15.50,
                'is_active' => true,
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@pamagency.com',
                'password' => Hash::make('password123'),
                'referral_code' => 'SJOHNSON2024',
                'commission_rate' => 12.75,
                'is_active' => true,
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@pamagency.com',
                'password' => Hash::make('password123'),
                'referral_code' => 'MBROWN2024',
                'commission_rate' => 18.25,
                'is_active' => true,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@pamagency.com',
                'password' => Hash::make('password123'),
                'referral_code' => 'EDAVIS2024',
                'commission_rate' => 10.00,
                'is_active' => false,
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@pamagency.com',
                'password' => Hash::make('password123'),
                'referral_code' => 'DWILSON2024',
                'commission_rate' => 14.30,
                'is_active' => true,
            ],
        ];

        foreach ($agents as $agentData) {
            Agent::create($agentData);
        }
    }
}