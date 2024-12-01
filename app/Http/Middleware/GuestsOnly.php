<?php

namespace App\Http\Middleware;

use App\Exceptions\GuestsOnlyException;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GuestsOnly
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     * @throws GuestsOnlyException
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            throw new GuestsOnlyException();
        }

        return $next($request);
    }
}
