<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username_ad' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username_ad', $request->username_ad)->first();

        if ($user) {
            // Jika password masih null (first time login)
            if ($user->password === null) {
                // Cek apakah password yang dimasukkan sama dengan username_ad
                if ($request->password === $user->username_ad) {
                    Auth::login($user);
                    return redirect()->route('change_password')->with('warning', 'Silakan ganti password Anda untuk pertama kalinya.');
                }
            } else {
                // Jika password sudah di set, gunakan mekanisme standar
                if (Auth::attempt(['username_ad' => $request->username_ad, 'password' => $request->password])) {
                    $request->session()->regenerate();
                    
                    if (Auth::user()->role === 'IT SAS Supervisor') {
                        return redirect()->route('inventory.index');
                    }
                    
                    return redirect()->route('dashboard');
                }
            }
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
