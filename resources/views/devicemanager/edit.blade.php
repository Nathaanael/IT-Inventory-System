@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 gap-6 relative">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6 shadow-sm">
        
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Edit Perangkat IoT</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Perbarui informasi dan konfigurasi batas sensor perangkat.</p>
            </div>
            <a href="{{ route('devicemanager.index') }}" class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-brand-500 dark:text-gray-400 dark:hover:text-brand-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>

        <!-- Form -->
        <form action="{{ route('devicemanager.update', $device->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section: Informasi Utama -->
            <div class="mb-2">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Informasi Dasar</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Data identitas perangkat dan lokasinya</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/50 dark:bg-gray-800/20 p-5 rounded-xl border border-gray-100 dark:border-gray-800 mb-8"> 
                <!-- Nama Perangkat -->
                <div>
                    <label for="nama_perangkat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Perangkat <span class="text-red-500">*</span></label>
                    <input type="text" id="nama_perangkat" name="nama_perangkat" value="{{ old('nama_perangkat', $device->nama_perangkat) }}" placeholder="Contoh: Sensor Gudang A" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 shadow-sm" required />
                </div>

                <!-- Lokasi -->
                <div>
                    <label for="lokasi" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lokasi Penempatan <span class="text-red-500">*</span></label>
                    <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi', $device->lokasi) }}" placeholder="Contoh: Gedung 1 Lt. 2" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 shadow-sm" required />
                </div>
            </div>

            <!-- Section: Konfigurasi Jaringan & Sensor -->
            <div class="mb-2 mt-8">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Konfigurasi Jaringan & Sensor</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pengaturan IP dan batas notifikasi alarm</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-brand-50/30 dark:bg-brand-500/5 p-5 rounded-xl border border-brand-100 dark:border-brand-500/20">


                <!-- MAC Address -->
                <div>
                    <label for="mac_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">MAC Address <span class="text-red-500">*</span></label>
                    <input type="text" id="mac_address" name="mac_address" value="{{ old('mac_address', $device->mac_address) }}" placeholder="Contoh: 24:0A:C4:00:01:10" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 shadow-sm uppercase font-mono" required />
                </div>
                
                <!-- Threshold Suhu -->
                <div>
                    <label for="threshold_suhu" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ambang Batas Suhu (°C) <span class="text-red-500">*</span></label>
                    <div class="relative shadow-sm rounded-lg">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.75 5.5C10.75 4.80964 11.3096 4.25 12 4.25C12.6904 4.25 13.25 4.80964 13.25 5.5V13.2549C13.9457 13.7367 14.4038 14.5407 14.4038 15.4519C14.4038 16.9107 13.2126 18.0904 11.7539 18.0904C10.2951 18.0904 9.10386 16.9107 9.10386 15.4519C9.10386 14.5407 9.5619 13.7367 10.2577 13.2549V5.5C10.2577 5.5 10.75 5.5 10.75 5.5ZM12 2.75C10.4812 2.75 9.25 3.98122 9.25 5.5V12.5271C8.30016 13.3311 7.60386 14.5203 7.60386 15.4519C7.60386 17.7391 9.46672 19.5904 11.7539 19.5904C14.0411 19.5904 15.9038 17.7391 15.9038 15.4519C15.9038 14.4108 15.4838 13.323 14.75 12.5271V5.5C14.75 3.98122 13.5188 2.75 12 2.75Z"></path></svg>
                        </div>
                        <input type="number" step="0.1" id="threshold_suhu" name="threshold_suhu" value="{{ old('threshold_suhu', $device->threshold_suhu) }}" placeholder="Contoh: 30.0" class="w-full rounded-lg border border-gray-300 bg-transparent pl-11 pr-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 font-mono" required />
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Buzzer peringatan akan menyala jika suhu di atas nilai ini.</p>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="mt-6">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keterangan / Catatan Tambahan <span class="text-gray-400 font-normal text-xs">(Opsional)</span></label>
                <textarea id="keterangan" name="keterangan" rows="3" placeholder="Tambahkan catatan khusus untuk perangkat ini..." class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-1 dark:border-gray-700 dark:text-white/90 shadow-sm">{{ old('keterangan', $device->keterangan) }}</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-end gap-4 border-t border-gray-100 dark:border-gray-800 pt-6">
                <button type="submit" class="rounded-lg bg-brand-500 px-8 py-3 text-sm font-medium text-white hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-all shadow-theme-md flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
