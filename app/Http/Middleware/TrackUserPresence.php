<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserPresence
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $expiresAt = now()->addMinutes(5); // Considera online por 5 min sem atividade
            \Illuminate\Support\Facades\Cache::put('user-is-online-' . auth()->id(), true, $expiresAt);
        }

        return $next($request);
    }
}
