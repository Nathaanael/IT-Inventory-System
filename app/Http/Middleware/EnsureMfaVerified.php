<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMfaVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ((int) $request->session()->get('mfa.verified_user_id') !== (int) $user->id) {
            $request->session()->put('mfa.pending_user_id', $user->id);

            return redirect()->route('mfa.setup');
        }

        return $next($request);
    }
}
