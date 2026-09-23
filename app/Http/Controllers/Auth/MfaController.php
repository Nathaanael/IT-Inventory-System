<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MfaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MfaController extends Controller
{
    public function __construct(private readonly MfaService $mfa)
    {
    }

    public function show(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);

        if (!$user) {
            return redirect()->route('login')->withErrors([
                'username_ad' => 'Sesi autentikasi telah berakhir. Silakan login kembali.',
            ]);
        }

        if ($user->password === null) {
            return redirect()->route('change_password');
        }

        if ($user->mfa_secret) {
            return view('auth.mfa-challenge', [
                'title' => 'Verifikasi Authenticator',
                'user' => $user,
            ]);
        }

        $secret = $request->session()->get('mfa.setup_secret');

        if (!$secret) {
            $secret = $this->mfa->generateSecret();
            $request->session()->put('mfa.setup_secret', $secret);
        }

        return view('auth.mfa-setup', [
            'title' => 'Hubungkan Microsoft Authenticator',
            'user' => $user,
            'secret' => $secret,
            'qrCode' => $this->mfa->qrCodeDataUri($user->username_ad, $secret),
        ]);
    }

    public function verifySetup(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'regex:/^\d{6}$/']]);

        $user = $this->pendingUser($request);
        $secret = $request->session()->get('mfa.setup_secret');

        if (!$user || !$secret || $user->mfa_secret) {
            return redirect()->route('login')->withErrors([
                'username_ad' => 'Sesi pemasangan MFA tidak valid. Silakan login kembali.',
            ]);
        }

        $usedAt = $this->mfa->verify($secret, $request->string('code')->toString());

        if ($usedAt === false) {
            return back()->withErrors(['code' => 'Kode tidak valid atau sudah kedaluwarsa.'])->withInput();
        }

        $user->forceFill([
            'mfa_secret' => $secret,
            'mfa_enabled_at' => now(),
            'mfa_last_used_at' => $usedAt,
        ])->save();

        return $this->completeLogin($request, $user);
    }

    public function verifyChallenge(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'regex:/^\d{6}$/']]);

        $user = $this->pendingUser($request);

        if (!$user || !$user->mfa_secret) {
            return redirect()->route('login')->withErrors([
                'username_ad' => 'Sesi verifikasi MFA tidak valid. Silakan login kembali.',
            ]);
        }

        $usedAt = $this->mfa->verify(
            $user->mfa_secret,
            $request->string('code')->toString(),
            $user->mfa_last_used_at,
        );

        if ($usedAt === false) {
            return back()->withErrors(['code' => 'Kode tidak valid, kedaluwarsa, atau sudah pernah digunakan.'])->withInput();
        }

        $user->forceFill(['mfa_last_used_at' => $usedAt])->save();

        return $this->completeLogin($request, $user);
    }

    public function cancel(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function pendingUser(Request $request): ?User
    {
        $id = $request->session()->get('mfa.pending_user_id');

        if (!$id) {
            return null;
        }

        $user = User::find($id);

        if (Auth::check() && (int) Auth::id() !== (int) $id) {
            return null;
        }

        return $user;
    }

    private function completeLogin(Request $request, User $user): RedirectResponse
    {
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget(['mfa.pending_user_id', 'mfa.setup_secret']);
        $request->session()->put('mfa.verified_user_id', $user->id);

        return redirect()->intended(
            $user->role === 'IT Support' ? route('inventory.index') : route('dashboard')
        )->with('success', 'Verifikasi MFA berhasil.');
    }
}
