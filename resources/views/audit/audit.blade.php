@extends('layouts.app')

@section('content')
<div x-data="auditLogs()" id="auditMainContainer" class="grid grid-cols-1 gap-6 relative">
    
    <!-- Loading Overlay -->
    <div x-show="isLoading" x-transition.opacity class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 z-50 flex flex-col items-center justify-center rounded-2xl backdrop-blur-sm" style="display: none;">
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
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 flex items-center gap-2">
                    Audit & Activity Logs
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Audit Trail keamanan sistem untuk melacak rekam jejak aktivitas staf IT.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                <form action="{{ route('logs.index') }}" method="GET" class="flex flex-wrap sm:flex-nowrap gap-3 w-full lg:w-auto" @submit.prevent="fetchData()">
                    <!-- Sort Filter -->
                    <div class="relative">
                        <select name="sort" x-model="sortQuery" @change="fetchData()" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500 cursor-pointer">
                            <option value="latest">Waktu (Terbaru)</option>
                            <option value="oldest">Waktu (Terlama)</option>
                            <option value="user_asc">Staf IT (A-Z)</option>
                            <option value="user_desc">Staf IT (Z-A)</option>
                            <option value="action_asc">Aksi (A-Z)</option>
                            <option value="action_desc">Aksi (Z-A)</option>
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
                            placeholder="Cari user atau aktivitas...">
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-brand-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Container -->
        <div id="tableContainer">
            @include('audit.partials.table')
        </div>
    </div>

</div>

@endsection

@stack('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('auditLogs', () => ({
            isLoading: false,
            searchQuery: '{{ request("search") }}',
            sortQuery: '{{ request("sort", "latest") }}',
            perPageQuery: '{{ request("per_page", "5") }}',
            
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
                
                if (this.sortQuery && this.sortQuery !== 'latest') {
                    url.searchParams.set('sort', this.sortQuery);
                } else {
                    url.searchParams.delete('sort');
                }

                if (this.perPageQuery && this.perPageQuery !== '5') {
                    url.searchParams.set('per_page', this.perPageQuery);
                } else {
                    url.searchParams.delete('per_page');
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
