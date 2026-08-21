@extends('layouts.app')

@section('content')
    <!-- Toast Notification -->
    @if (session('success'))
    <div class="mb-6 rounded-xl border border-success-200 bg-success-50 p-4 text-success-700 text-theme-sm dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-400">
        {{ session('success') }}
    </div>
    @endif

    <div x-data="{ showWifiModal: false }" class="grid grid-cols-1 gap-6 relative">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            
            <!-- Header & Action -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Device Manager</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manajemen daftar perangkat IoT (ESP32) dan sensor di berbagai lokasi.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <form id="searchForm" action="#" method="GET" class="flex flex-wrap sm:flex-nowrap gap-3">
                        <div class="relative flex-1 sm:w-64">
                            <input type="text" name="search" placeholder="Cari perangkat..." class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500" />
                            <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-brand-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                    <a href="{{ route('devicemanager.create') }}" class="flex-shrink-0 flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Perangkat
                    </a>
                </div>
            </div>

            <!-- Table Data -->
            <div class="overflow-x-auto overflow-y-auto max-h-[65vh] rounded-lg border border-gray-200 dark:border-gray-800 relative shadow-inner">
                <table class="w-full table-auto">
                    <thead class="text-left">
                        <tr>
                            <th class="sticky top-0 z-20 bg-gray-50 px-5 py-3 text-sm font-semibold text-gray-800 dark:bg-gray-800 dark:text-white/90 shadow-[0_1px_0_0_rgba(229,231,235,1)] dark:shadow-[0_1px_0_0_rgba(31,41,55,1)]">No</th>
                            <th class="sticky top-0 z-20 bg-gray-50 px-5 py-3 text-sm font-semibold text-gray-800 dark:bg-gray-800 dark:text-white/90 shadow-[0_1px_0_0_rgba(229,231,235,1)] dark:shadow-[0_1px_0_0_rgba(31,41,55,1)]">Nama Perangkat</th>
                            <th class="sticky top-0 z-20 bg-gray-50 px-5 py-3 text-sm font-semibold text-gray-800 dark:bg-gray-800 dark:text-white/90 shadow-[0_1px_0_0_rgba(229,231,235,1)] dark:shadow-[0_1px_0_0_rgba(31,41,55,1)]">Lokasi</th>
                            <th class="sticky top-0 z-20 bg-gray-50 px-5 py-3 text-sm font-semibold text-gray-800 dark:bg-gray-800 dark:text-white/90 shadow-[0_1px_0_0_rgba(229,231,235,1)] dark:shadow-[0_1px_0_0_rgba(31,41,55,1)]">MAC Address</th>
                            <th class="sticky top-0 z-20 bg-gray-50 px-5 py-3 text-sm font-semibold text-gray-800 dark:bg-gray-800 dark:text-white/90 shadow-[0_1px_0_0_rgba(229,231,235,1)] dark:shadow-[0_1px_0_0_rgba(31,41,55,1)]">IP Address</th>
                            <th class="sticky top-0 z-20 bg-gray-50 px-5 py-3 text-sm font-semibold text-gray-800 dark:bg-gray-800 dark:text-white/90 shadow-[0_1px_0_0_rgba(229,231,235,1)] dark:shadow-[0_1px_0_0_rgba(31,41,55,1)]">Threshold Suhu</th>
                            <th class="sticky top-0 z-20 bg-gray-50 px-5 py-3 text-sm font-semibold text-gray-800 dark:bg-gray-800 dark:text-white/90 shadow-[0_1px_0_0_rgba(229,231,235,1)] dark:shadow-[0_1px_0_0_rgba(31,41,55,1)]">Last Seen</th>
                            <th class="sticky top-0 z-20 bg-gray-50 px-5 py-3 text-sm font-semibold text-gray-800 dark:bg-gray-800 dark:text-white/90 shadow-[0_1px_0_0_rgba(229,231,235,1)] dark:shadow-[0_1px_0_0_rgba(31,41,55,1)]">Status</th>
                            <th class="sticky top-0 z-20 bg-gray-50 px-5 py-3 text-center text-sm font-semibold text-gray-800 dark:bg-gray-800 dark:text-white/90 shadow-[0_1px_0_0_rgba(229,231,235,1)] dark:shadow-[0_1px_0_0_rgba(31,41,55,1)]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($devices as $index => $device)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20">
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $index + 1 }}</td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">{{ $device->nama_perangkat }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $device->lokasi }}</td>
                            <td class="px-5 py-4 text-sm font-mono text-gray-600 dark:text-gray-400">{{ $device->mac_address }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $device->ip_address ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-orange-500">{{ number_format($device->threshold_suhu, 1) }} °C</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $device->last_seen ? \Carbon\Carbon::parse($device->last_seen)->diffForHumans() : '-' }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                @if($device->status == 'Online')
                                <span class="inline-flex rounded-full bg-success-50 px-2 py-1 text-xs font-semibold text-success-700 ring-1 ring-inset ring-success-600/20 dark:bg-success-500/10 dark:text-success-400 dark:ring-success-500/20">
                                    Online
                                </span>
                                @else
                                <span class="inline-flex rounded-full bg-red-50 px-2 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20">
                                    Offline
                                </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('devicemanager.edit', $device->id) }}" class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <button @click="showWifiModal = true" class="text-purple-500 hover:text-purple-700 transition-colors" title="Konfigurasi WiFi">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                                    </button>
                                    <form action="{{ route('devicemanager.destroy', $device->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus perangkat ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Data">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada perangkat IoT yang terdaftar.
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            
            <!-- Pagination (Dummy) -->
            <div class="mt-5 text-sm text-gray-500 dark:text-gray-400 text-center sm:text-left">
                Menampilkan 2 dari 2 data perangkat.
            </div>

        </div>
        
        <!-- Modal Konfigurasi WiFi -->
        <template x-teleport="body">
            <div 
                x-show="showWifiModal" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm" 
                style="display: none;"
                @keydown.escape.window="showWifiModal = false"
            >
                <div 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    @click.away="showWifiModal = false"
                    class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800"
                >
                    <div class="mb-5 flex items-center gap-3 text-purple-500">
                        <div class="p-2 bg-purple-100 dark:bg-purple-500/20 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Konfigurasi WiFi</h3>
                    </div>
                    
                    <p class="mb-5 text-sm text-gray-600 dark:text-gray-400">
                        Masukkan kredensial WiFi baru untuk perangkat ini. Perangkat akan otomatis restart dan mencoba terhubung.
                    </p>
                    
                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">SSID WiFi <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                            </div>
                            <input type="text" class="w-full pl-10 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-purple-500 transition-colors" placeholder="Masukkan nama WiFi">
                        </div>
                    </div>

                    <div class="mb-6" x-data="{ showPass: false }">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input :type="showPass ? 'text' : 'password'" class="w-full pl-10 pr-10 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-purple-500 focus:ring-1 focus:ring-purple-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-purple-500 transition-colors" placeholder="Masukkan password WiFi">
                            <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg x-show="!showPass" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showPass" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button @click="showWifiModal = false" class="rounded-lg border border-gray-300 dark:border-gray-600 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            Batal
                        </button>
                        <button @click="showWifiModal = false" class="rounded-lg bg-purple-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-purple-700 transition-colors shadow-theme-md">
                            Kirim ke Perangkat
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection
