<?php

namespace App\Http\Middleware;

use App\Services\RegistrationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRegistrationSession
{
    public function __construct(
        private RegistrationService $registrationService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currentRoute = $request->route()?->getName();

        // Only apply registration session logic to registration routes
        if (!$this->isRegistrationRoute($currentRoute)) {
            return $next($request);
        }

        $referenceCode = session('registration_ref');

        // Allow access to entry page and step 1 without session
        if ($this->isPublicRoute($request)) {
            return $next($request);
        }

        // Check if registration session exists and is valid
        if (!$referenceCode) {
            return redirect()->route('registration.index')->with('error', 'Please start your registration first.');
        }

        $registration = $this->registrationService->getRegistrationByReference($referenceCode);

        if (!$registration) {
            // Clear invalid session
            session()->forget('registration_ref');
            return redirect()->route('registration.index')->with('error', 'Invalid registration session. Please start again.');
        }

        if ($registration->status === 'completed') {
            return redirect()->route('registration.index')->with('error', 'This registration is already completed.');
        }

        // Check if user can access the requested step
        $step = $this->getStepFromRoute($request);
        if ($step && !$this->registrationService->canAccessStep($registration, $step)) {
            // Redirect to the appropriate step
            $currentStep = $registration->current_step;
            $route = $this->getRouteForStep($currentStep + 1);
            return redirect()->route($route);
        }

        return $next($request);
    }

    private function isRegistrationRoute(?string $routeName): bool
    {
        if (!$routeName) {
            return false;
        }

        return str_starts_with($routeName, 'registration.');
    }

    private function isPublicRoute(Request $request): bool
    {
        $currentRoute = $request->route()?->getName();

        return in_array($currentRoute, [
            'registration.index',
            'registration.step1',
            'registration.storePersonalInfo',
        ]);
    }

    private function getStepFromRoute(Request $request): ?int
    {
        $routeName = $request->route()?->getName();

        return match ($routeName) {
            'registration.step2' => 2,
            'registration.step3' => 3,
            'registration.step4' => 4,
            'registration.step5' => 5,
            'registration.storeBusinessInfo' => 2,
            'registration.storeAttendees' => 3,
            'registration.storeAddons' => 4,
            'registration.storeCheckout' => 5,
            default => null,
        };
    }

    private function getRouteForStep(int $step): string
    {
        return match ($step) {
            1 => 'registration.step1',
            2 => 'registration.step2',
            3 => 'registration.step3',
            4 => 'registration.step4',
            5 => 'registration.step5',
            default => 'registration.index',
        };
    }
}