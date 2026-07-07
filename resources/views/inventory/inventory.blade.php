@extends('layouts.app')

@section('content')
    <div x-data="inventoryManager()" class="grid grid-cols-1 gap-6 relative">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            
            <!-- Header & Action -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Data PC & Remote</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Daftar inventory IP Address dan kredensial Remote.</p>
                </div>
                
                <div class="flex gap-3">
                    <div class="relative">
                        <input type="text" placeholder="Cari data..." class="w-full sm:w-64 rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500" />
                        <svg class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Data
                    </button>
                </div>
            </div>

            <!-- Table Data -->
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50 text-left dark:bg-gray-800/50">
                        <tr>
                            <th class="px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90">No</th>
                            <th class="px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90">Nama User</th>
                            <th class="px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90">Departemen</th>
                            <th class="px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90">IP Address</th>
                            <th class="px-5 py-3 text-sm font-semibold text-gray-800 dark:text-white/90">Password Remote</th>
                            <th class="px-5 py-3 text-center text-sm font-semibold text-gray-800 dark:text-white/90">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        
                        <!-- Dummy Row 1 -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20" x-data="{ rowId: 1, showPassword: false }" @password-verified.window="if($event.detail.id === rowId) showPassword = true">
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">1</td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">Budi Santoso</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Finance</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">192.168.1.150</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono tracking-widest text-lg mt-1 leading-none" x-text="showPassword ? 'Pa$$w0rd' : '********'"></span>
                                    <button @click="if(showPassword) { showPassword = false } else { $dispatch('open-auth-modal', { id: rowId }) }" class="text-gray-400 hover:text-brand-500 focus:outline-none transition-colors" title="Lihat Password">
                                        <!-- Eye Icon -->
                                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <!-- Eye Off Icon -->
                                        <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <button class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Dummy Row 2 -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20" x-data="{ rowId: 2, showPassword: false }" @password-verified.window="if($event.detail.id === rowId) showPassword = true">
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">2</td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">Siti Aisyah</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-400">HRD</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">192.168.1.151</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono tracking-widest text-lg mt-1 leading-none" x-text="showPassword ? 'S3cr3t!' : '********'"></span>
                                    <button @click="if(showPassword) { showPassword = false } else { $dispatch('open-auth-modal', { id: rowId }) }" class="text-gray-400 hover:text-brand-500 focus:outline-none transition-colors" title="Lihat Password">
                                        <!-- Eye Icon -->
                                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <!-- Eye Off Icon -->
                                        <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <button class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
            
            <!-- Pagination Dummy -->
            <div class="flex items-center justify-between mt-5">
                <span class="text-sm text-gray-500 dark:text-gray-400">Menampilkan 1 hingga 2 dari 2 data</span>
                <div class="flex gap-1">
                    <button class="px-3 py-1 rounded border border-gray-200 text-gray-500 dark:border-gray-800 dark:text-gray-400 cursor-not-allowed" disabled>Prev</button>
                    <button class="px-3 py-1 rounded bg-brand-500 text-white font-medium">1</button>
                    <button class="px-3 py-1 rounded border border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">Next</button>
                </div>
            </div>

        </div>

        <!-- Modal Password Overlay -->
        <template x-teleport="body">
            <div 
                x-show="showModal" 
                @open-auth-modal.window="showModal = true; activeRowId = $event.detail.id; passwordInput = ''"
                class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm" 
                x-transition.opacity 
                style="display: none;"
            >
                <div 
                    class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800" 
                    @click.away="showModal = false" 
                    x-show="showModal"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                >
                    <div class="mb-5 flex items-center gap-3 text-red-500">
                        <div class="p-2 bg-red-100 dark:bg-red-500/20 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Otorisasi Keamanan</h3>
                    </div>
                    
                    <p class="mb-5 text-sm text-gray-600 dark:text-gray-400">
                        Untuk alasan keamanan dan *audit trail*, silakan masukkan password akun Anda (<strong>{{ auth()->user()?->username_ad ?? 'User' }}</strong>) untuk melihat password remote ini.
                    </p>
                    
                    <div class="mb-6 relative">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Password Anda</label>
                        <input type="password" x-model="passwordInput" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90" placeholder="Masukkan password..." @keyup.enter="verifyPassword()" x-ref="pwdInput" x-effect="if(showModal) setTimeout(() => $refs.pwdInput.focus(), 100)" />
                    </div>
                    
                    <div class="flex justify-end gap-3">
                        <button @click="showModal = false" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">Batal</button>
                        <button @click="verifyPassword()" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors shadow-theme-md">Konfirmasi</button>
                    </div>
                </div>
            </div>
        </template>

    </div>

    <!-- Alpine Logic -->
    <script>
        function inventoryManager() {
            return {
                showModal: false,
                activeRowId: null,
                passwordInput: '',
                
                verifyPassword() {
                    // MOCK UI: Anggap saja verifikasi berhasil jika input tidak kosong
                    if(this.passwordInput.trim() !== '') {
                        // Memancarkan event bahwa password telah terverifikasi untuk baris ini
                        this.$dispatch('password-verified', { id: this.activeRowId });
                        this.showModal = false;
                        this.passwordInput = '';
                    } else {
                        alert('Password tidak boleh kosong!');
                    }
                }
            }
        }
    </script>
@endsection
