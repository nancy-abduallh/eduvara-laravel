<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OnboardingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->isStudent() && auth()->user()->needsOnboarding()) {
            if (!$request->routeIs('onboarding.*')) {
                return redirect()->route('onboarding.vark');
            }
        }
        return $next($request);
    }
}
