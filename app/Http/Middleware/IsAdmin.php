<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user login dan rolenya admin (case-insensitive)
        if (auth()->check() && strtolower((string) auth()->user()->role) === 'admin') {
            return $next($request);
        }

        return redirect('/')->with('error', 'Akses terbatas. Halaman ini khusus admin.');
    }
}
