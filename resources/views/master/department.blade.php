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

<div x-data="departmentManager()" class="grid grid-cols-1 gap-6 relative">
    
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
                    Master Departemen
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Kelola standarisasi nama departemen agar konsisten dalam data inventory.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <form action="{{ route('master.departments.index') }}" method="GET" class="flex flex-wrap sm:flex-nowrap gap-3 w-full lg:w-auto" @submit.prevent="fetchData()">
                    
                    <!-- Sort Filter -->
                    <div class="relative">
                        <select name="sort" x-model="sortQuery" @change="fetchData()" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500 cursor-pointer">
                            <option value="latest">Terbaru</option>
                            <option value="oldest">Terlama</option>
                            <option value="name_asc">Nama (A-Z)</option>
                            <option value="name_desc">Nama (Z-A)</option>
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
                            placeholder="Cari departemen...">
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-brand-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>
                </form>

                <button @click="$dispatch('open-modal', { action: 'create' })" class="flex-shrink-0 flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-colors shadow-theme-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Departemen
                </button>
            </div>
        </div>

        <!-- Table Container -->
        <div id="tableContainer">
            @include('master.partials.department_table')
        </div>
        
    </div>

    <!-- Modal Form Departemen -->
    <template x-teleport="body">
        <div 
            x-show="showFormModal" 
            @open-modal.window="
                showFormModal = true; 
                action = $event.detail.action;
                if(action === 'edit') {
                    formData = {
                        id: $event.detail.id,
                        name: $event.detail.name,
                    };
                    formAction = '{{ route('master.departments.update', 'REPLACE_ID') }}'.replace('REPLACE_ID', formData.id);
                } else {
                    formData = { id: null, name: '' };
                    formAction = '{{ route('master.departments.store') }}';
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
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white/90" x-text="action === 'create' ? 'Tambah Departemen Baru' : 'Edit Departemen'"></h3>
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
                        <!-- Nama Departemen -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Departemen</label>
                            <input type="text" name="name" x-model="formData.name" required class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 transition-colors" placeholder="Contoh: Finance & Accounting">
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
                hasInventory = $event.detail.hasInventory;
                deleteUrl = '{{ route('master.departments.destroy', 'REPLACE_ID') }}'.replace('REPLACE_ID', deleteId);
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
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white/90" x-text="hasInventory ? 'Penghapusan Ditolak' : 'Konfirmasi Hapus'"></h3>
                </div>
                
                <template x-if="hasInventory">
                    <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                        Departemen ini <span class="font-bold text-red-500">tidak dapat dihapus</span> karena sedang digunakan oleh satu atau lebih PC dalam data inventory. Silakan ubah data inventory tersebut terlebih dahulu sebelum menghapus departemen ini.
                    </p>
                </template>

                <template x-if="!hasInventory">
                    <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                        Apakah Anda yakin ingin menghapus departemen ini? Nama departemen akan dihapus secara permanen dari sistem master data.
                    </p>
                </template>
                
                <div class="flex justify-end gap-3">
                    <template x-if="hasInventory">
                        <button type="button" @click="showDeleteModal = false" class="rounded-lg bg-gray-200 px-5 py-2.5 text-sm font-medium text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors shadow-theme-md">Saya Mengerti</button>
                    </template>
                    
                    <template x-if="!hasInventory">
                        <div class="flex gap-3">
                            <button type="button" @click="showDeleteModal = false" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">Batal</button>
                            <form :action="deleteUrl" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg bg-red-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-600 transition-colors shadow-theme-md">Ya, Hapus Departemen</button>
                            </form>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('departmentManager', () => ({
            isLoading: false,
            searchQuery: '{{ request("search") }}',
            perPageQuery: '{{ request("per_page", "5") }}',
            sortQuery: '{{ request("sort", "latest") }}',
            showFormModal: false,
            showDeleteModal: false,
            action: 'create',
            formAction: '',
            deleteUrl: '',
            hasInventory: false,
            formData: {
                id: null,
                name: '',
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
