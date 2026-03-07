<?php

namespace App\Interface;

use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Inertia\Response as InertiaResponse;

interface PaymentInterface
{
    /**
     * Render the payment page for this strategy
     */
    public function render(Registration $registration);

    /**
     * Process the payment for this strategy
     */
    public function process(Registration $registration, $input): InertiaResponse;
}