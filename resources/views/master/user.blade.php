@extends('layouts.app')

@section('content')
    <!-- Toast Notification -->
    @if (session('success'))
    <div class="mb-6 rounded-xl border border-success-200 bg-success-50 p-4 text-success-700 text-theme-sm dark:border-success-500/20 dark:bg-success-500/10 dark:text-success-400">
        {{ session('success') }}
    </div>
    @endif
    
    @if (session('error'))
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 text-theme-sm dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 text-theme-sm dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

<div x-data="userManager()" class="grid grid-cols-1 gap-6 relative">
    
    <!-- Loading Overlay -->
    <div x-show="isLoading" x-transition.opacity class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 z-50 flex flex-col items-center justify-center rounded-2xl backdrop-blur-sm" style="display: none;">
        <svg class="animate-spin h-8 w-8 text-brand-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Memuat data...</span>
    </div>

    <!-- Main Container -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
        
        <!-- Header & Action -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 flex items-center gap-2">
                    Manajemen User (IT Staff)
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Kelola akun staf IT. Akun baru akan menggunakan Username AD sebagai password sementara.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <form action="{{ route('master.users.index') }}" method="GET" class="flex flex-wrap sm:flex-nowrap gap-3 w-full lg:w-auto" @submit.prevent="fetchData()">
                    <!-- Sort Filter -->
                    <div class="relative">
                        <select name="sort" x-model="sortQuery" @change="fetchData()" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500 cursor-pointer">
                            <option value="latest">Waktu (Terbaru)</option>
                            <option value="oldest">Waktu (Terlama)</option>
                        </select>
                    </div>

                    <!-- Per Page Filter -->
                    <div class="relative">
                        <select name="per_page" x-model="perPageQuery" @change="fetchData()" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500 cursor-pointer">
                            <option value="5">5 Baris</option>
                            <option value="10">10 Baris</option>
                            <option value="20">20 Baris</option>
                            <option value="50">50 Baris</option>
                            <option value="100">100 Baris</option>
                        </select>
                    </div>

                    <!-- Live Search -->
                    <div class="relative flex-1 sm:w-64">
                        <input 
                            type="text" 
                            name="search"
                            x-model="searchQuery" 
                            @input.debounce.500ms="fetchData()"
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500" 
                            placeholder="Cari user...">
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-brand-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>
                </form>

                <button @click="$dispatch('open-modal', { action: 'create' })" class="flex-shrink-0 flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-colors shadow-theme-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah User
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div id="tableContainer">
            @include('master.partials.user_table')
        </div>
        
    </div>

    <!-- Modal Form User -->
    <template x-teleport="body">
        <div 
            x-show="showFormModal" 
            @open-modal.window="
                showFormModal = true; 
                action = $event.detail.action;
                if(action === 'edit') {
                    formData = {
                        id: $event.detail.id,
                        id_karyawan: $event.detail.id_karyawan,
                        name: $event.detail.name,
                        username_ad: $event.detail.username_ad,
                        role: $event.detail.role
                    };
                    formAction = '{{ route('master.users.update', 'REPLACE_ID') }}'.replace('REPLACE_ID', formData.id);
                } else {
                    formData = { id: null, id_karyawan: '', name: '', username_ad: '', role: 'IT Support' };
                    formAction = '{{ route('master.users.store') }}';
                }
            "
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm" 
            x-transition.opacity 
            style="display: none;"
        >
            <div 
                class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800" 
                @click.away="showFormModal = false" 
                x-show="showFormModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white/90" x-text="action === 'create' ? 'Tambah User Baru' : 'Edit User'"></h3>
                    <button @click="showFormModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form :action="formAction" method="POST">
                    @csrf
                    <template x-if="action === 'edit'">
                        @method('PUT')
                    </template>
                    
                    <div class="space-y-4">
                        <!-- ID Karyawan -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">ID Karyawan</label>
                            <input type="number" inputmode="numeric" pattern="[0-9]*" name="id_karyawan" x-model="formData.id_karyawan" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 transition-colors" placeholder="Masukkan ID Karyawan berupa angka">
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                            <input type="text" name="name" x-model="formData.name" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 transition-colors" placeholder="Contoh: Nathanael Prasetyo">
                        </div>

                        <!-- Username AD -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Username AD</label>
                            <input type="text" name="username_ad" x-model="formData.username_ad" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 transition-colors" placeholder="Contoh: nathanael.prasetyo">
                            <p x-show="action === 'create'" class="mt-1 text-xs text-brand-600 dark:text-brand-400">
                                Info: Saat pertama kali dibuat, password otomatis disamakan dengan Username AD ini.
                            </p>
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Role Sistem</label>
                            <select name="role" x-model="formData.role" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 transition-colors">
                                <option value="Super Admin">Super Admin</option>
                                <option value="IT Support">IT Support</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showFormModal = false" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">Batal</button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors shadow-theme-md" x-text="action === 'create' ? 'Simpan Data' : 'Perbarui Data'"></button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Delete -->
    <template x-teleport="body">
        <div 
            x-show="showDeleteModal" 
            @open-delete-modal.window="
                showDeleteModal = true;
                deleteId = $event.detail.id;
                deleteRole = $event.detail.role;
                deleteUrl = '{{ route('master.users.destroy', 'REPLACE_ID') }}'.replace('REPLACE_ID', deleteId);
            "
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
            >
                <div class="mb-5 flex items-center gap-3 text-red-500">
                    <div class="p-2 bg-red-100 dark:bg-red-500/20 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Konfirmasi Hapus</h3>
                </div>
                
                <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus akun ini? Staff IT terkait tidak akan bisa lagi mengakses sistem IT Inventory.
                </p>

                <template x-if="deleteRole === 'Super Admin'">
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 text-sm dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400 font-medium flex gap-3 items-start">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <div>
                            <strong>PERINGATAN:</strong> Anda akan menghapus akun tingkat Super Admin. Pastikan masih ada Super Admin lain yang aktif untuk mengelola sistem.
                        </div>
                    </div>
                </template>
                
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showDeleteModal = false" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">Batal</button>
                    <form :action="deleteUrl" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg bg-red-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-600 transition-colors shadow-theme-md">Ya, Hapus Akun</button>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Modal Reset Password -->
    <template x-teleport="body">
        <div 
            x-show="showResetPasswordModal" 
            @open-reset-password-modal.window="
                showResetPasswordModal = true;
                resetPasswordId = $event.detail.id;
                resetPasswordName = $event.detail.name;
                resetPasswordUrl = '{{ route('master.users.reset-password', 'REPLACE_ID') }}'.replace('REPLACE_ID', resetPasswordId);
            "
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm" 
            x-transition.opacity 
            style="display: none;"
        >
            <div 
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800" 
                @click.away="showResetPasswordModal = false" 
                x-show="showResetPasswordModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
            >
                <div class="mb-5 flex items-center gap-3 text-orange-500">
                    <div class="p-2 bg-orange-100 dark:bg-orange-500/20 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Reset Password Login</h3>
                </div>
                
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    Apakah Anda yakin ingin mereset <strong class="text-gray-800 dark:text-white">Password Login</strong> untuk user <strong x-text="resetPasswordName"></strong>?
                </p>
                <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                    Setelah direset, user tersebut akan menggunakan `Username AD` sebagai password sementara dan wajib membuat password baru saat login kembali.
                </p>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="showResetPasswordModal = false" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">Batal</button>
                    <form :action="resetPasswordUrl" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="rounded-lg bg-orange-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-orange-600 transition-colors shadow-theme-md">Ya, Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </template>

    <!-- Modal Reset PIN -->
    <template x-teleport="body">
        <div 
            x-show="showResetPinModal" 
            @open-reset-pin-modal.window="
                showResetPinModal = true;
                resetPinId = $event.detail.id;
                resetPinName = $event.detail.name;
                resetPinUrl = '{{ route('master.users.reset-pin', 'REPLACE_ID') }}'.replace('REPLACE_ID', resetPinId);
            "
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm" 
            x-transition.opacity 
            style="display: none;"
        >
            <div 
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800" 
                @click.away="showResetPinModal = false" 
                x-show="showResetPinModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
            >
                <div class="mb-5 flex items-center gap-3 text-yellow-500">
                    <div class="p-2 bg-yellow-100 dark:bg-yellow-500/20 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Reset Vault PIN</h3>
                </div>
                
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    Apakah Anda yakin ingin mereset <strong class="text-gray-800 dark:text-white">Vault PIN</strong> untuk user <strong x-text="resetPinName"></strong>?
                </p>
                <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                    Setelah direset, Vault PIN akan dikosongkan. User dapat membuat PIN baru yang aman melalui menu Vault.
                </p>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="showResetPinModal = false" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">Batal</button>
                    <form :action="resetPinUrl" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="rounded-lg bg-yellow-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-yellow-600 transition-colors shadow-theme-md">Ya, Reset PIN</button>
                    </form>
                </div>
            </div>
        </div>
    </template>

</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('userManager', () => ({
            isLoading: false,
            searchQuery: '{{ request("search") }}',
            perPageQuery: '{{ request("per_page", "5") }}',
            sortQuery: '{{ request("sort", "latest") }}',
            showFormModal: false,
            showDeleteModal: false,
            showResetPasswordModal: false,
            showResetPinModal: false,
            action: 'create',
            formAction: '',
            deleteUrl: '',
            deleteRole: '',
            resetPasswordUrl: '',
            resetPasswordId: null,
            resetPasswordName: '',
            resetPinUrl: '',
            resetPinId: null,
            resetPinName: '',
            formData: {
                id: null,
                id_karyawan: '',
                name: '',
                username_ad: '',
                role: 'IT Support'
            },
            
            init() {
                // Intercept pagination clicks
                this.$el.addEventListener('click', (e) => {
                    const link = e.target.closest('nav[role="navigation"] a');
                    if (link) {
                        e.preventDefault();
                        this.fetchDataUrl(link.href);
                    }
                });
            },
            
            fetchData() {
                let url = new URL(window.location.href);
                if (this.searchQuery) {
                    url.searchParams.set('search', this.searchQuery);
                } else {
                    url.searchParams.delete('search');
                }
                
                if (this.perPageQuery && this.perPageQuery !== '5') {
                    url.searchParams.set('per_page', this.perPageQuery);
                } else {
                    url.searchParams.delete('per_page');
                }

                if (this.sortQuery && this.sortQuery !== 'latest') {
                    url.searchParams.set('sort', this.sortQuery);
                } else {
                    url.searchParams.delete('sort');
                }
                
                url.searchParams.delete('page');
                
                this.fetchDataUrl(url.toString());
            },

            fetchDataUrl(url) {
                this.isLoading = true;
                
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const tableContainer = document.querySelector('#tableContainer');
                    if (tableContainer) {
                        tableContainer.innerHTML = html;
                    }
                    window.history.pushState({}, '', url);
                })
                .finally(() => {
                    this.isLoading = false;
                });
            }
        }));
    });
</script>
@endpush
@endsection
