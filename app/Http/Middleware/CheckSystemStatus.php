<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSystemStatus
{
    /**
     * Routes to exclude from the middleware.
     *
     * @var array
     */
    protected $except = [
        '+!c',
        '800w',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the route is in the exceptions list
        foreach ($this->except as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        // Check the system's active status from the config
        if (!config('system_status.is_active')) {
            abort(403, 'Service down. Please contact support.');
        }

        return $next($request);
    }
}
