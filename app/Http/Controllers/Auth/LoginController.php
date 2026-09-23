<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username_ad' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username_ad', $request->username_ad)->first();

        if ($user && $user->password === null && hash_equals($user->username_ad, $request->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            $request->session()->put('mfa.pending_user_id', $user->id);

            return redirect()->route('change_password')
                ->with('warning', 'Silakan ganti password Anda untuk pertama kalinya.');
        }

        if ($user && $user->password !== null && Hash::check($request->password, $user->password)) {
            $request->session()->regenerate();
            $request->session()->put('mfa.pending_user_id', $user->id);

            return redirect()->route('mfa.setup');
        }

        return back()->withErrors([
            'username_ad' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ])->onlyInput('username_ad');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
