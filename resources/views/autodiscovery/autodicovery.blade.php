@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="autoDiscovery()">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Auto-Discovery</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Pindai jaringan secara otomatis untuk mendeteksi perangkat baru dan Mac Address.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="startScan()" 
                    :disabled="isScanning"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-indigo-400 dark:disabled:bg-indigo-800 disabled:cursor-not-allowed rounded-xl transition-all shadow-sm hover:shadow-md">
                
                {{-- Radar Icon (Spinning when scanning) --}}
                <svg :class="isScanning ? 'animate-spin' : ''" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                
                <span x-text="isScanning ? 'Memindai Jaringan...' : 'Mulai Scan Jaringan'"></span>
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Subnet</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white font-mono mt-0.5">192.168.1.0/24</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Perangkat Tak Dikenal</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5" x-text="devices.filter(d => d.status === 'unknown').length"></p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cocok di Database</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white mt-0.5" x-text="devices.filter(d => d.status === 'known').length"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Scanning Progress (Visible only when scanning) --}}
    <div x-show="isScanning" x-transition.opacity class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 p-5 rounded-2xl flex flex-col gap-3" style="display: none;">
        <div class="flex justify-between items-center text-sm">
            <span class="font-medium text-indigo-800 dark:text-indigo-300">Memindai 254 IP Address (ARP Sweep)...</span>
            <span class="font-bold text-indigo-700 dark:text-indigo-400" x-text="scanProgress + '%'"></span>
        </div>
        <div class="w-full bg-indigo-200/50 dark:bg-indigo-900/50 rounded-full h-2.5 overflow-hidden">
            <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300 ease-out" :style="`width: ${scanProgress}%`"></div>
        </div>
        <p class="text-xs text-indigo-600/70 dark:text-indigo-400/70 font-mono" x-text="'Mengecek IP: 192.168.1.' + Math.floor((scanProgress / 100) * 254)"></p>
    </div>

    {{-- Results Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative">
        
        {{-- Loader overlay for table --}}
        <div x-show="isScanning" class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 backdrop-blur-[2px] z-10 flex items-center justify-center" style="display: none;"></div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 dark:text-gray-400 uppercase bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">IP Address</th>
                        <th class="px-6 py-4 font-semibold">Mac Address</th>
                        <th class="px-6 py-4 font-semibold">Vendor / Hostname</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    
                    {{-- Empty State --}}
                    <template x-if="devices.length === 0 && !isScanning">
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Belum Ada Data</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Klik tombol 'Mulai Scan Jaringan' untuk mendeteksi perangkat.</p>
                            </td>
                        </tr>
                    </template>

                    {{-- Loop through mocked devices --}}
                    <template x-for="device in devices" :key="device.mac">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <template x-if="device.status === 'known'">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Dikenali
                                    </span>
                                </template>
                                <template x-if="device.status === 'unknown'">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Rogue / Baru
                                    </span>
                                </template>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono font-medium text-gray-900 dark:text-gray-200" x-text="device.ip"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-gray-500 dark:text-gray-400 uppercase text-xs tracking-wider" x-text="device.mac"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-gray-200" x-text="device.vendor"></div>
                                <div class="text-xs text-gray-500" x-text="device.hostname"></div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <template x-if="device.status === 'unknown'">
                                    <button class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-400 dark:hover:bg-indigo-900/50 rounded-lg transition-colors border border-indigo-200 dark:border-indigo-800">
                                        Tambahkan ke Inventory
                                    </button>
                                </template>
                                <template x-if="device.status === 'known'">
                                    <span class="text-xs text-gray-400 flex items-center justify-end gap-1">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        Sudah Terdaftar
                                    </span>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('autoDiscovery', () => ({
        isScanning: false,
        scanProgress: 0,
        devices: [],
        
        // Mock data for UI demonstration
        mockData: [
            { ip: '192.168.1.10', mac: '00:1A:2B:3C:4D:5E', vendor: 'Dell Inc.', hostname: 'PC-FINANCE-01', status: 'known' },
            { ip: '192.168.1.15', mac: 'F8:B1:56:AB:CD:EF', vendor: 'HP Inc.', hostname: 'LAPTOP-HR-02', status: 'known' },
            { ip: '192.168.1.99', mac: 'B8:27:EB:11:22:33', vendor: 'Raspberry Pi', hostname: 'Unknown-Device', status: 'unknown' },
            { ip: '192.168.1.102', mac: 'D4:81:D7:AA:BB:CC', vendor: 'MikroTik', hostname: 'ROUTER-GUEST', status: 'known' },
            { ip: '192.168.1.200', mac: '00:0C:29:44:55:66', vendor: 'VMware, Inc.', hostname: 'SERVER-APP-TEST', status: 'unknown' },
        ],

        startScan() {
            if (this.isScanning) return;
            
            this.isScanning = true;
            this.scanProgress = 0;
            this.devices = []; // Clear previous results
            
            // Simulate scanning progress
            const interval = setInterval(() => {
                this.scanProgress += Math.floor(Math.random() * 15) + 5;
                if (this.scanProgress >= 100) {
                    this.scanProgress = 100;
                    clearInterval(interval);
                    
                    // Show results after fake scan finishes
                    setTimeout(() => {
                        this.isScanning = false;
                        this.devices = this.mockData;
                    }, 500);
                }
            }, 300);
        }
    }));
});
</script>
@endpush
@endsection
