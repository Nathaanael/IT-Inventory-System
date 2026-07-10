@extends('layouts.app')

@section('content')
<div x-data="{ 
    showAddPanelModal: false, 
    showEditPanelModal: false,
    editPanelUrl: '',
    editPanelData: { name: '', location: '' },
    showAddSwitchModal: false, 
    showEditSwitchModal: false, 
    editSwitchUrl: '', 
    editSwitchData: { panel_id: '', merk: '', ip_address: '', notes: '' }, 
    showDetailModal: false, 
    showDeleteModal: false, 
    deleteUrl: '', 
    showDeletePanelModal: false, 
    deletePanelUrl: '', 
    selectedPanelId: null, 
    detailData: { id: '', merk: '', ip: '', status: '', panel: '', notes: '' }, 
    pingStats: {},
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
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newTable = doc.getElementById('dataswitch-table-container');
            if (newTable) document.getElementById('dataswitch-table-container').innerHTML = newTable.innerHTML;
            
            const newPagination = doc.getElementById('dataswitch-pagination');
            const pagEl = document.getElementById('dataswitch-pagination');
            if (newPagination && pagEl) {
                pagEl.innerHTML = newPagination.innerHTML;
            } else if (pagEl) {
                pagEl.innerHTML = '';
            }
        } catch (error) {
            console.error('Search failed:', error);
        } finally {
            this.isSearching = false;
        }
    }
}" @ping-update.window="pingStats[$event.detail.id] = $event.detail.status" class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto relative">
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Switch -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-5">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total switch</h3>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalSwitches }}</span>
            </div>
        </div>
        <!-- Online -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-5">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Online</h3>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-green-600 dark:text-green-500" x-text="Object.values(pingStats).filter(s => s === 'online').length">0</span>
            </div>
        </div>
        <!-- Offline -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-5">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Offline</h3>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-red-600 dark:text-red-500" x-text="Object.values(pingStats).filter(s => s === 'offline').length">0</span>
            </div>
        </div>
        <!-- Total Panel -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700/60 p-5">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total panel</h3>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalPanels }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6 relative">
        
        <!-- Loading Overlay -->
        <div x-show="isSearching" x-transition.opacity class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 z-50 flex flex-col items-center justify-center rounded-2xl backdrop-blur-sm" style="display: none;">
            <svg class="animate-spin h-8 w-8 text-brand-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Mencari data...</span>
        </div>
        <!-- Header & Action -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Data Switch & Panel</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola daftar switch dan penempatannya pada setiap panel.</p>
            </div>
            
            <!-- Controls -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">

                <form action="{{ route('dataswitch.index') }}" method="GET" class="flex w-full sm:w-auto" @submit.prevent="performSearch($event.target)">
                    <div class="relative flex-1 sm:w-56">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari switch atau panel..." @input.debounce.500ms="performSearch($event.target.form)" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500" />
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-brand-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Add Button -->
                <button @click="showAddPanelModal = true" class="flex-shrink-0 flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-colors w-full sm:w-auto shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Panel
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-900/20">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 text-red-600 dark:text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <h4 class="text-sm font-semibold text-red-800 dark:text-red-400">Terdapat kesalahan pada isian form:</h4>
                        <ul class="mt-1 list-inside list-disc text-sm text-red-700 dark:text-red-300">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/50 dark:bg-green-900/20">
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-green-600 dark:text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm font-medium text-green-800 dark:text-green-400">{{ session('success') }}</p>
                </div>
            </div>
        @endif

    <!-- List Panels (Table) -->
    <div id="dataswitch-table-container" class="flex flex-col gap-4">
        @forelse($panels as $panel)
        @php
            $newSwitches = $panel->dataSwitches->filter(function($switch) {
                return $switch->created_at && $switch->created_at->diffInDays(now()) <= 3;
            });
            $hasNewSwitch = $newSwitches->isNotEmpty();
            $latestNewSwitchId = $hasNewSwitch ? $newSwitches->sortByDesc('created_at')->first()->id : 0;
        @endphp
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <tbody x-data="{ 
                        open: true, 
                        seenNew: localStorage.getItem('panel_seen_{{ $panel->id }}_{{ $latestNewSwitchId }}') === 'true',
                        markSeen() {
                            this.seenNew = true;
                            if ({{ $latestNewSwitchId }} > 0) {
                                localStorage.setItem('panel_seen_{{ $panel->id }}_{{ $latestNewSwitchId }}', 'true');
                            }
                        }
                    }" class="divide-y divide-gray-200 dark:divide-gray-700/60">
                        
                        <!-- Group Header -->
                        <tr @click="open = !open; markSeen()" class="bg-gray-50 dark:bg-gray-800/80 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors" :class="{'border-b border-gray-200 dark:border-gray-700/60': open}">
                        <td colspan="6" class="px-5 py-3">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-gray-400 transform transition-transform" :class="{'rotate-90': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    <svg class="w-5 h-5 text-brand-500 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    <h3 class="text-[15px] font-bold text-gray-900 dark:text-white">
                                        {{ $panel->name }}
                                        @if($panel->location)
                                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400 ml-1">({{ $panel->location }})</span>
                                        @endif

                                        @if($hasNewSwitch)
                                            <span x-show="!seenNew" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-brand-100 text-brand-800 dark:bg-brand-900/30 dark:text-brand-300 border border-brand-200 dark:border-brand-800">
                                                Baru
                                            </span>
                                        @endif
                                    </h3>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $panel->dataSwitches->count() }} switch</span>
                                    <button @click.stop="$dispatch('open-edit-panel-modal', { id: {{ $panel->id }}, name: {{ Js::from($panel->name) }}, location: {{ Js::from($panel->location ?? '') }} })" class="flex items-center justify-center rounded-md bg-white dark:bg-gray-800 px-2 py-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 border border-gray-200 dark:border-gray-700 transition-colors shadow-sm" title="Edit Panel">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button @click.stop="selectedPanelId = {{ $panel->id }}; showAddSwitchModal = true; $refs.addSwitchForm?.reset()" class="flex items-center justify-center gap-1.5 rounded-md bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        Tambah Switch
                                    </button>
                                    <button @click.stop="$dispatch('open-delete-panel-modal', { id: {{ $panel->id }} })" class="flex items-center justify-center rounded-md bg-white dark:bg-gray-800 px-2 py-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 border border-gray-200 dark:border-gray-700 transition-colors shadow-sm" title="Hapus Panel">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!-- Group Columns Header -->
                    <tr x-show="open" class="bg-white dark:bg-gray-800/50 text-xs uppercase text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700/60">
                        <th class="px-5 py-3 font-semibold w-16">No</th>
                        <th class="px-5 py-3 font-semibold">Merk</th>
                        <th class="px-5 py-3 font-semibold">IP Address</th>
                        <th class="px-5 py-3 font-semibold">Panel</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                    
                    @forelse($panel->dataSwitches as $index => $switch)
                    <!-- Items -->
                    <tr x-show="open" x-data="switchRow({{ $switch->id }})" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                        <td class="px-5 py-4 text-sm font-medium text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-5 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ $switch->merk }}</td>
                        <td class="px-5 py-4 text-sm font-medium text-gray-600 dark:text-gray-300">{{ $switch->ip_address }}</td>
                        <td class="px-5 py-4 text-sm font-medium text-gray-600 dark:text-gray-300">
                            {{ $panel->name }}
                            @if($panel->location)
                                <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ $panel->location }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
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
                                <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="pingStatus === 'checking' ? 'Mengecek...' : (pingStatus === 'online' ? 'Online' : 'Offline')"></span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-3">
                                <button @click="detailData = { merk: '{{ $switch->merk }}', ip: '{{ $switch->ip_address }}', status: pingStatus, panel: '{{ $panel->name }}', notes: {{ Js::from($switch->notes ?? '-') }} }; showDetailModal = true" class="text-indigo-500 hover:text-indigo-700 transition-colors" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </button>
                                <button @click="$dispatch('open-edit-modal', { 
                                    id: {{ $switch->id }},
                                    panel_id: {{ $panel->id }},
                                    merk: {{ Js::from($switch->merk) }},
                                    ip_address: {{ Js::from($switch->ip_address) }},
                                    notes: {{ Js::from($switch->notes ?? '') }}
                                })" class="text-blue-500 hover:text-blue-700 transition-colors" title="Edit Data">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <button @click="$dispatch('open-delete-modal', { id: {{ $switch->id }} })" class="text-red-500 hover:text-red-700 transition-colors" title="Hapus Data">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr x-show="open">
                        <td colspan="6" class="px-5 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada switch di panel ini.
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-2xl p-8 text-center text-sm text-gray-500 dark:text-gray-400">
            Belum ada data panel switch.
        </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div id="dataswitch-pagination" class="mt-6">
        @if($panels->hasPages())
            {{ $panels->links() }}
        @endif
    </div>
    <!-- End Main Content Wrapper -->

    <!-- ==================== MODALS ==================== -->

    <!-- Add Panel Modal -->
    <template x-teleport="body">
        <div
            x-show="showAddPanelModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div
                @click.away="showAddPanelModal = false; $refs.addPanelForm.reset()"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-brand-100 dark:bg-brand-500/20 rounded-full">
                            <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Tambah Panel Baru</h3>
                    </div>
                    <button @click="showAddPanelModal = false; $refs.addPanelForm.reset()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form x-ref="addPanelForm" action="{{ route('dataswitch.storePanel') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Panel <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors" placeholder="e.g. Panel lantai 2 - produksi">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Lokasi</label>
                            <input type="text" name="location" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors" placeholder="e.g. Gedung A, lantai 2">
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showAddPanelModal = false; $refs.addPanelForm.reset()" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors shadow-theme-md">
                            Simpan Panel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Edit Panel Modal -->
    <template x-teleport="body">
        <div
            x-show="showEditPanelModal"
            @open-edit-panel-modal.window="
                showEditPanelModal = true;
                editPanelUrl = '{{ url('dataswitch/panel') }}/' + $event.detail.id;
                editPanelData = {
                    name: $event.detail.name,
                    location: $event.detail.location
                };
            "
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div
                @click.away="showEditPanelModal = false; $refs.editPanelForm.reset()"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-500/20 rounded-full">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Edit Panel</h3>
                    </div>
                    <button @click="showEditPanelModal = false; $refs.editPanelForm.reset()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form x-ref="editPanelForm" :action="editPanelUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Panel <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="editPanelData.name" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors" placeholder="e.g. Panel lantai 2 - produksi">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Lokasi</label>
                            <input type="text" name="location" x-model="editPanelData.location" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors" placeholder="e.g. Gedung A, lantai 2">
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showEditPanelModal = false; $refs.editPanelForm.reset()" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="rounded-lg bg-blue-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-600 transition-colors shadow-theme-md">
                            Update Panel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Add Switch Modal -->
    <template x-teleport="body">
        <div
            x-show="showAddSwitchModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div
                @click.away="showAddSwitchModal = false; $refs.addSwitchForm.reset()"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 dark:bg-green-500/20 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Tambah Switch</h3>
                    </div>
                    <button @click="showAddSwitchModal = false; $refs.addSwitchForm.reset()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form x-ref="addSwitchForm" action="{{ route('dataswitch.storeSwitch') }}" method="POST">
                    @csrf
                    <!-- Hidden input to store panel_id when 'Tambah Switch' is clicked for a specific panel -->
                    <input type="hidden" name="panel_id" x-model="selectedPanelId">
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Merk <span class="text-red-500">*</span></label>
                                <input type="text" name="merk" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors" placeholder="e.g. Cisco, TP-Link">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">IP Address <span class="text-red-500">*</span></label>
                                <input type="text" name="ip_address" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors" placeholder="e.g. 10.126.20.10">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notes</label>
                            <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors resize-none" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showAddSwitchModal = false; $refs.addSwitchForm.reset()" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors shadow-theme-md">
                            Simpan Switch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Detail Switch Modal -->
    <template x-teleport="body">
        <div
            x-show="showDetailModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div
                @click.away="showDetailModal = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800"
            >
                <div class="mb-5 flex items-center justify-between text-indigo-500">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-100 dark:bg-indigo-500/20 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Detail Switch</h3>
                    </div>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-800 dark:text-gray-200 mb-6">
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Merk</p>
                        <p class="font-semibold" x-text="detailData.merk"></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">IP Address</p>
                        <p class="font-mono bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded inline-block" x-text="detailData.ip"></p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Status</p>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full" :class="detailData.status === 'Online' ? 'bg-green-500' : 'bg-red-500'"></span>
                            <span class="font-semibold" x-text="detailData.status"></span>
                        </div>
                    </div>
                    <div class="col-span-2 space-y-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Panel</p>
                        <p class="font-semibold" x-text="detailData.panel"></p>
                    </div>
                </div>
                
                <div class="space-y-1 mb-6">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Notes</p>
                    <div class="p-3 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-100 dark:border-gray-800">
                        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap" x-text="detailData.notes"></p>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button @click="showDetailModal = false" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Edit Switch Modal -->
    <template x-teleport="body">
        <div
            x-show="showEditSwitchModal"
            @open-edit-modal.window="
                showEditSwitchModal = true; 
                editSwitchUrl = '{{ url('dataswitch/switch') }}/' + $event.detail.id;
                editSwitchData = {
                    panel_id: $event.detail.panel_id,
                    merk: $event.detail.merk,
                    ip_address: $event.detail.ip_address,
                    notes: $event.detail.notes
                };
            "
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm"
            style="display: none;"
        >
            <div
                @click.away="showEditSwitchModal = false; $refs.editSwitchForm.reset()"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-500/20 rounded-full">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Edit Switch</h3>
                    </div>
                    <button @click="showEditSwitchModal = false; $refs.editSwitchForm.reset()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form x-ref="editSwitchForm" :action="editSwitchUrl" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="panel_id" x-model="editSwitchData.panel_id">
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Merk <span class="text-red-500">*</span></label>
                                <input type="text" name="merk" x-model="editSwitchData.merk" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors" placeholder="e.g. Cisco, TP-Link">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">IP Address <span class="text-red-500">*</span></label>
                                <input type="text" name="ip_address" x-model="editSwitchData.ip_address" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors" placeholder="e.g. 10.126.20.10">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notes</label>
                            <textarea name="notes" x-model="editSwitchData.notes" rows="3" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500 transition-colors resize-none" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" @click="showEditSwitchModal = false; $refs.editSwitchForm.reset()" class="rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="rounded-lg bg-blue-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-600 transition-colors shadow-theme-md">
                            Update Switch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Delete Modal -->
    <template x-teleport="body">
        <div 
            x-show="showDeleteModal" 
            @open-delete-modal.window="showDeleteModal = true; deleteUrl = '{{ url('dataswitch/switch') }}/' + $event.detail.id;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm" 
            style="display: none;"
        >
            <div 
                @click.away="showDeleteModal = false" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800" 
            >
                <div class="mb-5 flex items-center gap-3 text-red-500">
                    <div class="p-2 bg-red-100 dark:bg-red-500/20 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Konfirmasi Hapus</h3>
                </div>
                
                <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus data switch ini? Data yang sudah dihapus tidak dapat dikembalikan.
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

    <!-- Delete Panel Modal -->
    <template x-teleport="body">
        <div 
            x-show="showDeletePanelModal" 
            @open-delete-panel-modal.window="showDeletePanelModal = true; deletePanelUrl = '{{ url('dataswitch/panel') }}/' + $event.detail.id;"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm" 
            style="display: none;"
        >
            <div 
                @click.away="showDeletePanelModal = false" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 sm:p-8 border border-gray-100 dark:border-gray-800" 
            >
                <div class="mb-5 flex items-center gap-3 text-red-500">
                    <div class="p-2 bg-red-100 dark:bg-red-500/20 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white/90">Hapus Panel</h3>
                </div>
                
                <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus panel ini? <span class="font-bold text-red-500">Semua switch di dalam panel ini juga akan ikut terhapus</span> dan tidak dapat dikembalikan.
                </p>
                
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showDeletePanelModal = false" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">Batal</button>
                    <form :action="deletePanelUrl" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg bg-red-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-red-600 transition-colors shadow-theme-md">Ya, Hapus Panel</button>
                    </form>
                </div>
            </div>
        </div>
    </template>

</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('switchRow', (switchId) => ({
            pingStatus: 'checking',
            
            init() {
                // Stagger delay slightly to prevent all pings firing at the exact same millisecond
                const delay = 200 + Math.random() * 1000;
                setTimeout(() => {
                    this.checkPing();
                }, delay);
            },
            
            checkPing() {
                this.pingStatus = 'checking';
                fetch(`/dataswitch/${switchId}/ping`)
                    .then(response => response.json())
                    .then(data => {
                        this.pingStatus = data.status;
                        this.$dispatch('ping-update', { id: switchId, status: data.status });
                    })
                    .catch(() => {
                        this.pingStatus = 'offline';
                        this.$dispatch('ping-update', { id: switchId, status: 'offline' });
                    });
            }
        }));
    });
</script>
@endpush
@endsection