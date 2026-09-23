@extends('layouts.fullscreen-layout')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-10">
    <div class="w-full max-w-lg overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-xl">
        <div class="bg-[#00549c] px-8 py-6 text-white">
            <h1 class="text-xl font-semibold">Hubungkan Microsoft Authenticator</h1>
            <p class="mt-1 text-sm text-white/80">Langkah keamanan ini wajib sebelum mengakses sistem.</p>
        </div>

        <div class="space-y-6 px-8 py-7">
            @if (session('success'))
                <div class="rounded-lg bg-green-50 p-3 text-sm text-green-700">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg bg-red-50 p-3 text-sm text-red-600">{{ $errors->first() }}</div>
            @endif

            <ol class="list-decimal space-y-2 pl-5 text-sm text-gray-600">
                <li>Buka Microsoft Authenticator di ponsel.</li>
                <li>Pilih <strong>Tambah akun</strong> lalu <strong>Akun lainnya</strong>.</li>
                <li>Pindai QR Code berikut dan masukkan kode 6 digit yang muncul.</li>
            </ol>

            <div class="flex justify-center">
                <img src="{{ $qrCode }}" alt="QR Code Microsoft Authenticator" class="h-60 w-60 rounded-xl border border-gray-200 bg-white p-2">
            </div>

            <div class="rounded-lg bg-gray-50 p-4 text-center">
                <p class="mb-2 text-xs text-gray-500">Jika QR tidak dapat dipindai, masukkan setup key ini:</p>
                <code class="break-all text-sm font-semibold tracking-wider text-gray-800">{{ $secret }}</code>
            </div>

            <form action="{{ route('mfa.setup.verify') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="code" class="mb-2 block text-sm font-medium text-gray-700">Kode verifikasi</label>
                    <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" placeholder="000000" autofocus required
                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-center text-xl tracking-[0.5em] focus:border-[#00549c] focus:outline-none focus:ring-2 focus:ring-[#00549c]/20">
                </div>
                <button type="submit" class="w-full rounded-xl bg-[#00549c] py-3 text-sm font-semibold text-white transition hover:bg-[#004b8c]">
                    Aktifkan MFA dan Masuk
                </button>
            </form>

            <form action="{{ route('mfa.cancel') }}" method="POST" class="text-center">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Batalkan dan kembali ke login</button>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const codeInput = document.getElementById('code');
        const form = codeInput?.closest('form');
        const submitButton = form?.querySelector('button[type=submit]');

        form?.addEventListener('submit', () => {
            if (!submitButton) return;

            submitButton.disabled = true;
            submitButton.classList.add('cursor-not-allowed', 'opacity-70');
            submitButton.innerHTML = `
                <span class='flex items-center justify-center gap-2'>
                    <svg class='h-5 w-5 animate-spin' viewBox='0 0 24 24' fill='none' aria-hidden='true'>
                        <circle class='opacity-25' cx='12' cy='12' r='10' stroke='currentColor' stroke-width='4'></circle>
                        <path class='opacity-75' fill='currentColor' d='M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z'></path>
                    </svg>
                    Mengaktifkan MFA...
                </span>`;
        });
    });
</script>
@endpush
@endsection
