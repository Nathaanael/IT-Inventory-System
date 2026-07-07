<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFirstLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->password === null) {
            // Jika rute saat ini BUKAN change_password, redirect ke change_password
            if (!$request->routeIs('change_password') && !$request->routeIs('auth.change-password.update')) {
                return redirect()->route('change_password')->with('warning', 'Anda harus mengganti password Anda terlebih dahulu.');
            }
        }

        // Jika password sudah di-set (tidak null), jangan biarkan user mengakses halaman change_password
        if (auth()->check() && auth()->user()->password !== null) {
            if ($request->routeIs('change_password') || $request->routeIs('auth.change-password.update')) {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
