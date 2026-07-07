@extends('layouts.app')

@section('content')
    <div class="grid grid-cols-1 gap-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <h2 class="mb-2 text-2xl font-bold text-gray-800 dark:text-white/90">
                Selamat Datang, {{ auth()->user()?->name ?? 'User' }}!
            </h2>
            <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                Anda saat ini masuk sebagai <strong class="text-gray-800 dark:text-white/90">{{ auth()->user()?->role ?? '-' }}</strong>. 
                Gunakan menu di sebelah kiri untuk mengelola sistem IT Inventory.
            </p>
        </div>
    </div>
@endsection
