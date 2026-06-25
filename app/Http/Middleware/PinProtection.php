<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PinProtection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to the lock screen and its Livewire component
        if ($request->is('locked') || $request->is('livewire/*')) {
            return $next($request);
        }

        // Check if the pwa_unlocked cookie exists
        if (!$request->hasCookie('pwa_unlocked')) {
            return redirect()->route('locked');
        }

        return $next($request);
    }
}
