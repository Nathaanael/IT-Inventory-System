@extends('layouts.app')

@section('content')
    <!-- Toast Notification -->
    @if (session('success'))
    <div class="mb-6 rounded-xl border border-success-200 bg-success-50 p-4 text-success-700 text-theme-sm dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-400">
            {{ session('success') }}
        </div>
    @endif

    <div x-data="inventoryManager()" class="grid grid-cols-1 gap-6 relative">
        
        <!-- Loading Overlay -->
        <div x-show="isSearching" x-transition.opacity class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 z-50 flex flex-col items-center justify-center rounded-2xl backdrop-blur-sm" style="display: none;">
            <svg class="animate-spin h-8 w-8 text-brand-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Memuat data...</span>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            
            <!-- Header & Action -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Data PC & Remote</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Daftar inventory IP Address dan kredensial Remote.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <form action="{{ route('inventory.index') }}" method="GET" class="flex flex-wrap sm:flex-nowrap gap-3" @submit.prevent="performSearch($event.target)">
                        <div class="relative">
                            <select name="sort" @change="performSearch($event.target.form)" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                                <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            </select>
                        </div>
                        <div class="relative">
                            <select name="per_page" @change="performSearch($event.target.form)" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500">
                                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 Baris</option>
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 Baris</option>
                                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 Baris</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Baris</option>
                            </select>
                        </div>
                        <div class="relative flex-1 sm:w-56">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari data..." @input.debounce.500ms="performSearch($event.target.form)" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500" />
                            <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-brand-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                    <a href="{{ route('inventory.create') }}" class="flex-shrink-0 flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Data
                    </a>
                </div>
            </div>

            <!-- Table Data -->
            <div id="inventory-table-container" class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800 relative">
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
                        @forelse ($inventories as $index => $inventory)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20" x-data="inventoryRow({{ $inventory->id }})" @password-verified.window="if($event.detail.id === rowId) { showPassword = true; revealedPassword = $event.detail.password; }">
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $inventories->firstItem() + $index }}</td>
                            <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">{{ $inventory->nama_user }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">{{ $inventory->department->name ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center gap-2">
                                    <span class="relative flex h-3 w-3" :title="pingStatus === 'checking' ? 'Mengecek status...' : (pingStatus === 'online' ? 'Online' : 'Offline')">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-400 opacity-75" x-show="pingStatus === 'checking'"></span>
                                        <span class="relative inline-flex rounded-full h-3 w-3 transition-colors duration-300"
                                            :class="{
                                                'bg-gray-400': pingStatus === 'checking',
                                                'bg-success-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]': pingStatus === 'online',
                                                'bg-red-500': pingStatus === 'offline'
                                            }"></span>
                                    </span>
                                    <span>{{ $inventory->ip_address }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono tracking-widest text-lg mt-1 leading-none" x-text="showPassword ? revealedPassword : '********'"></span>
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
                                    <a href="{{ route('inventory.rdp', $inventory->id) }}" class="text-green-500 hover:text-green-700 transition-colors" title="Download RDP (One-Click Remote)" target="_blank">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </a>
                                    <button @click="$dispatch('open-auth-modal', { id: rowId, action: 'edit' })" class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button @click="$dispatch('open-delete-modal', { id: rowId })" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada data inventory yang ditemukan.
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div id="inventory-pagination" class="mt-5">
                {{ $inventories->links() }}
            </div>

        </div>

        <!-- Modal Setup PIN (Otomatis muncul jika belum set) -->
        <template x-teleport="body">
            <div 
                x-show="showSetupModal" 
                class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm" 
                x-transition.opacity 
                style="display: none;"
            >
                <div 
                    class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800" 
                >
                    <div class="mb-5 flex items-center gap-3 text-brand-500">
                        <div class="p-2 bg-brand-100 dark:bg-brand-500/20 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Setup Vault PIN</h3>
                    </div>
                    
                    <p class="mb-5 text-sm text-gray-600 dark:text-gray-400">
                        Untuk menjaga kerahasiaan *password remote*, Anda diwajibkan untuk mengatur <strong>6 Digit PIN Khusus</strong>. PIN ini akan digunakan setiap kali Anda melihat atau mengubah data.
                    </p>
                    
                    <div class="mb-4 relative">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Masukkan 6 Digit PIN</label>
                        <input type="password" maxlength="6" inputmode="numeric" pattern="[0-9]*" x-model="setupPin" class="w-full text-center tracking-[1em] font-mono text-2xl rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90" placeholder="••••••" />
                    </div>

                    <div class="mb-6 relative">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi PIN</label>
                        <input type="password" maxlength="6" inputmode="numeric" pattern="[0-9]*" x-model="setupPinConfirm" class="w-full text-center tracking-[1em] font-mono text-2xl rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90" placeholder="••••••" />
                    </div>

                    <div x-show="setupError" class="mb-4 text-sm text-red-500 text-center" x-text="setupError"></div>
                    
                    <div class="flex justify-end gap-3">
                        <button @click="saveSetupPin()" class="w-full rounded-lg bg-brand-500 px-5 py-3 text-sm font-medium text-white hover:bg-brand-600 transition-colors shadow-theme-md" :disabled="isSavingPin" :class="{'opacity-50 cursor-not-allowed': isSavingPin}">
                            <span x-show="!isSavingPin">Simpan PIN</span>
                            <span x-show="isSavingPin">Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <!-- Modal Password Overlay -->
        <template x-teleport="body">
            <div 
                x-show="showModal" 
                @open-auth-modal.window="showModal = true; activeRowId = $event.detail.id; authAction = $event.detail.action || 'view'; passwordInput = ''; authError = ''"
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
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Otorisasi Vault</h3>
                    </div>
                    
                    <p class="mb-5 text-sm text-gray-600 dark:text-gray-400">
                        Silakan masukkan <strong>6 Digit Vault PIN</strong> Anda untuk <span x-text="authAction === 'edit' ? 'mengubah data ini' : 'melihat password remote ini'"></span>.
                    </p>
                    
                    <div class="mb-6 relative">
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Vault PIN</label>
                        <input type="password" maxlength="6" inputmode="numeric" pattern="[0-9]*" x-model="passwordInput" class="w-full text-center tracking-[1em] font-mono text-2xl rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90" placeholder="••••••" @keyup.enter="verifyPassword()" x-ref="pwdInput" x-effect="if(showModal) setTimeout(() => $refs.pwdInput.focus(), 100)" />
                    </div>

                    <div x-show="authError" class="mb-4 text-sm text-red-500 text-center" x-text="authError"></div>
                    
                    <div class="flex justify-end gap-3">
                        <button @click="showModal = false" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors" :disabled="isVerifying">Batal</button>
                        <button @click="verifyPassword()" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors shadow-theme-md" :disabled="isVerifying">
                            <span x-show="!isVerifying">Konfirmasi</span>
                            <span x-show="isVerifying">Mengecek...</span>
                        </button>
                    </div>
                </div>
            </div>
        </template>
        <!-- Modal Delete Overlay -->
        <template x-teleport="body">
            <div 
                x-show="showDeleteModal" 
                @open-delete-modal.window="showDeleteModal = true; deleteId = $event.detail.id; deleteUrl = '{{ url('inventory') }}/' + deleteId;"
                class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm" 
                x-transition.opacity 
                style="display: none;"
            >
                <div 
                    class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800" 
                    @click.away="showDeleteModal = false" 
                    x-show="showDeleteModal"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                >
                    <div class="mb-5 flex items-center gap-3 text-red-500">
                        <div class="p-2 bg-red-100 dark:bg-red-500/20 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Konfirmasi Hapus</h3>
                    </div>
                    
                    <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                        Apakah Anda yakin ingin menghapus data inventory ini? Data yang sudah dihapus tidak dapat dikembalikan.
                    </p>
                    
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showDeleteModal = false" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">Batal</button>
                        <form :action="deleteUrl" method="POST" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg bg-red-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-600 transition-colors shadow-theme-md">Ya, Hapus Data</button>
                        </form>
                    </div>
                </div>
            </div>
        </template>

    </div>

    <!-- Alpine Logic -->
    <script>
        function inventoryRow(id) {
            return {
                rowId: id,
                showPassword: false,
                revealedPassword: '',
                pingStatus: 'checking',

                init() {
                    // Stagger delay between 0.5s and 2.5s to prevent overwhelming the server on bulk load
                    const delay = 500 + Math.random() * 2000;
                    setTimeout(() => {
                        this.checkPing();
                    }, delay);
                },

                async checkPing() {
                    try {
                        const response = await fetch('{{ url('inventory') }}/' + this.rowId + '/ping', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const result = await response.json();
                        this.pingStatus = result.status;
                    } catch (error) {
                        this.pingStatus = 'offline';
                    }
                }
            }
        }

        function inventoryManager() {
            return {
                showSetupModal: {{ auth()->user()->vault_pin === null ? 'true' : 'false' }},
                setupPin: '',
                setupPinConfirm: '',
                setupError: '',
                isSavingPin: false,

                showModal: false,
                activeRowId: null,
                passwordInput: '',
                authAction: 'view',
                authError: '',
                isVerifying: false,

                showDeleteModal: false,
                deleteId: null,
                deleteUrl: '',
                isSearching: false,
                
                async performSearch(form) {
                    this.isSearching = true;
                    const url = new URL(form.action);
                    const formData = new FormData(form);
                    for (const [key, value] of formData.entries()) {
                        if(value) url.searchParams.append(key, value);
                    }
                    
                    window.history.pushState({}, '', url);
                    
                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const html = await response.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        const newTable = doc.getElementById('inventory-table-container');
                        const newPagination = doc.getElementById('inventory-pagination');
                        
                        if (newTable) {
                            document.getElementById('inventory-table-container').innerHTML = newTable.innerHTML;
                        }
                        if (newPagination) {
                            document.getElementById('inventory-pagination').innerHTML = newPagination.innerHTML;
                        }
                    } catch (error) {
                        console.error('Search failed:', error);
                    } finally {
                        this.isSearching = false;
                    }
                },

                async saveSetupPin() {
                    this.setupError = '';
                    if (this.setupPin.length !== 6 || this.setupPinConfirm.length !== 6) {
                        this.setupError = 'PIN harus terdiri dari 6 digit angka.';
                        return;
                    }
                    if (this.setupPin !== this.setupPinConfirm) {
                        this.setupError = 'Konfirmasi PIN tidak cocok.';
                        return;
                    }

                    this.isSavingPin = true;
                    try {
                        const response = await fetch('{{ route('inventory.vault.set-pin') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                pin: this.setupPin,
                                pin_confirmation: this.setupPinConfirm
                            })
                        });
                        
                        const result = await response.json();
                        
                        if (response.ok && result.success) {
                            this.showSetupModal = false;
                        } else {
                            this.setupError = result.message || result.errors?.pin?.[0] || 'Terjadi kesalahan saat menyimpan PIN.';
                        }
                    } catch (error) {
                        this.setupError = 'Terjadi kesalahan koneksi jaringan.';
                    } finally {
                        this.isSavingPin = false;
                    }
                },
                
                async verifyPassword() {
                    this.authError = '';
                    if (this.passwordInput.length !== 6) {
                        this.authError = 'PIN harus terdiri dari 6 digit angka.';
                        return;
                    }

                    this.isVerifying = true;

                    try {
                        if (this.authAction === 'edit') {
                            // Cukup verifikasi PIN untuk membuka sesi edit
                            const response = await fetch('{{ route('inventory.vault.verify-pin') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ pin: this.passwordInput })
                            });
                            
                            const result = await response.json();
                            
                            if (response.ok && result.success) {
                                window.location.href = '{{ url('inventory') }}/' + this.activeRowId + '/edit';
                            } else {
                                this.authError = result.message || 'PIN yang Anda masukkan salah.';
                            }
                        } else {
                            // Tarik password asli jika mode view
                            const response = await fetch('{{ url('inventory') }}/' + this.activeRowId + '/reveal', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ pin: this.passwordInput })
                            });
                            
                            const result = await response.json();
                            
                            if (response.ok && result.success) {
                                this.$dispatch('password-verified', { id: this.activeRowId, password: result.password });
                                this.showModal = false;
                                this.passwordInput = '';
                            } else {
                                this.authError = result.message || 'PIN yang Anda masukkan salah.';
                            }
                        }
                    } catch (error) {
                        this.authError = 'Terjadi kesalahan jaringan.';
                    } finally {
                        this.isVerifying = false;
                    }
                }
            }
        }
    </script>
@endsection
