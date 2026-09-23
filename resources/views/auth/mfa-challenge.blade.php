@extends('layouts.fullscreen-layout')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl">
        <div class="bg-[#00549c] px-8 py-6 text-white">
            <h1 class="text-xl font-semibold">Verifikasi Dua Langkah</h1>
            <p class="mt-1 text-sm text-white/80">Masukkan kode dari Microsoft Authenticator.</p>
        </div>

        <div class="space-y-5 px-8 py-7">
            <p class="text-sm text-gray-600">Login sebagai <strong>{{ $user->name }}</strong></p>

            @if ($errors->any())
                <div class="rounded-lg bg-red-50 p-3 text-sm text-red-600">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('mfa.challenge.verify') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="code" class="mb-2 block text-sm font-medium text-gray-700">Kode Authenticator</label>
                    <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" placeholder="000000" autofocus required
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-center text-xl tracking-[0.5em] focus:border-[#00549c] focus:outline-none focus:ring-2 focus:ring-[#00549c]/20">
                </div>
                <button type="submit" class="w-full rounded-xl bg-[#00549c] py-3 text-sm font-semibold text-white transition hover:bg-[#004b8c]">
                    Verifikasi dan Masuk
                </button>
            </form>

            <p class="text-center text-xs text-gray-500">Kehilangan akses? Hubungi Super Admin untuk unlink MFA.</p>

            <form action="{{ route('mfa.cancel') }}" method="POST" class="text-center">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Kembali ke login</button>
            </form>
        </div>
    </div>
</div>
@endsection
