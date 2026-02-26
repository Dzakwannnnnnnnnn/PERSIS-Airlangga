<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsGuru
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && strtolower((string) auth()->user()->role) === 'guru') {
            return $next($request);
        }

        return redirect('/')->with('error', 'Akses terbatas. Halaman ini khusus guru.');
    }
}

