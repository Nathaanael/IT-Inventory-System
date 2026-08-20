@extends('layouts.app')

@section('content')
<div x-data="queueBoard()" class="space-y-6">

    {{-- Outer Wrapper --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-5 sm:p-6">

        {{-- Header & Search --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Queue Board</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Antrian tiket helpdesk IT Support yang sedang berjalan.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search --}}
                <div class="relative flex-1 sm:w-64">
                    <input type="text" x-model="searchQuery" @input="filterQueue()" placeholder="Cari token atau issue..." class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 pl-10 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500" />
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Live Indicator --}}
                <div class="flex items-center gap-2 px-4 py-2 rounded-lg bg-success-50 dark:bg-success-500/10 border border-success-200 dark:border-success-500/20">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-success-500"></span>
                    </span>
                    <span class="text-xs font-bold text-success-600 dark:text-success-400 uppercase tracking-wider">Live</span>
                </div>
            </div>
        </div>

        {{-- Token + In Progress (Side by Side) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            {{-- LEFT: Nomor Antrian Anda --}}
            <div class="lg:col-span-1">
                <div class="rounded-2xl border border-brand-100 bg-brand-50 dark:bg-brand-500/[0.08] dark:border-brand-500/20 p-5 sm:p-6 h-full flex flex-col items-center justify-center text-center">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-500 shadow-md mb-5">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-brand-500 dark:text-brand-400 uppercase tracking-widest mb-3">Nomor Antrian Anda</p>
                    <span id="yourToken" class="inline-flex items-center justify-center rounded-2xl bg-white dark:bg-gray-900 border-2 border-brand-200 dark:border-brand-500/30 px-6 py-3 text-3xl font-extrabold text-brand-600 dark:text-brand-400 tracking-wider shadow-sm">
                        Q-007
                    </span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-4 font-medium">Token ini diberikan saat Anda<br>melaporkan issue</p>
                </div>
            </div>

            {{-- RIGHT: In Progress --}}
            <div class="lg:col-span-2">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-5 sm:p-6 h-full flex flex-col justify-between relative overflow-hidden">
                    
                    {{-- Decorative gradient accent --}}
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-500 via-brand-400 to-brand-600"></div>

                    <div>
                        {{-- Token + Status Label --}}
                        <div class="flex items-center gap-3 mb-5">
                            <span class="inline-flex items-center gap-1.5 rounded-md bg-brand-50 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-500 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                                </span>
                                In Progress
                            </span>
                            <span class="inline-flex items-center rounded-lg bg-gray-100 dark:bg-gray-800 px-3 py-1.5 text-sm font-extrabold text-gray-600 dark:text-gray-300 tracking-wider">
                                Q-004
                            </span>
                        </div>

                        {{-- Issue Title --}}
                        <h2 id="currentIssueTitle" class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight mb-4">
                            Penanganan Hardware
                        </h2>

                        {{-- Location & Category --}}
                        <div class="flex flex-wrap items-center gap-3 mb-6">
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 px-3 py-1.5 text-sm font-semibold text-gray-600 dark:text-gray-300">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                                HR room, 2nd floor
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 dark:bg-blue-500/10 px-3 py-1.5 text-sm font-semibold text-blue-600 dark:text-blue-400">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" /></svg>
                                Hardware
                            </span>
                        </div>
                    </div>

                    {{-- Bottom: Footer + SLA --}}
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 pt-6 border-t border-gray-100 dark:border-gray-800">
                        {{-- Footer info --}}
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            {{-- Assigned Technician --}}
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-500/10">
                                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                </div>
                                <span id="currentTechnician" class="text-sm font-bold text-gray-800 dark:text-white/90">Andi</span>
                            </div>
                            <div class="hidden sm:block w-px h-5 bg-gray-200 dark:bg-gray-700"></div>
                            {{-- Time Info --}}
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                                <div class="flex flex-col">
                                    <span id="currentStartTime" class="text-sm font-bold text-gray-800 dark:text-white/90">started 09:14</span>
                                    <span id="currentElapsed" class="text-[11px] font-semibold text-gray-400 dark:text-gray-500">22 min elapsed</span>
                                </div>
                            </div>
                        </div>

                        {{-- SLA Mini --}}
                        <div class="flex items-center gap-3 rounded-xl border border-gray-100 bg-gray-50 px-4 py-2.5 dark:border-gray-800 dark:bg-gray-800/50">
                            <div class="text-right">
                                <div class="flex items-end gap-1.5">
                                    <span class="text-2xl font-extrabold text-gray-900 dark:text-white leading-none">15</span>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-0.5">min lagi</span>
                                </div>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium mt-1">SLA: 45 min</p>
                            </div>
                            <span class="inline-flex items-center rounded-md bg-success-50 px-2 py-0.5 text-[10px] font-bold text-success-600 dark:bg-success-500/10 dark:text-success-400">
                                On Track
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Up Next --}}
        <div>
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6 sm:p-8 flex flex-col">
                    
                    {{-- Section Title + Page Info --}}
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">Up Next</h3>
                        <span class="text-[11px] font-semibold text-gray-400 dark:text-gray-500" x-text="`${queuePage} / ${totalQueuePages}`"></span>
                    </div>

                    {{-- Queue List --}}
                    <div class="flex-1 space-y-1" id="queueList">

                        <template x-for="(item, idx) in paginatedQueue" :key="item.token">
                            <div class="group flex items-center gap-4 py-2.5 px-3 -mx-3 rounded-xl hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-colors cursor-default">
                                <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-800 text-base font-extrabold text-gray-500 dark:text-gray-400" x-text="String((queuePage - 1) * perPage + idx + 1).padStart(2, '0')">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-bold text-gray-800 dark:text-white/90 truncate" x-text="item.title"></h4>
                                        <span class="flex-shrink-0 inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 text-[10px] font-bold text-gray-500 dark:text-gray-400 tracking-wider" x-text="item.token"></span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium truncate mt-0.5" x-text="item.location + ' · ' + item.reporter"></p>
                                </div>
                                <span x-show="item.priority" class="flex-shrink-0 inline-flex items-center rounded-md bg-error-50 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-error-600 dark:bg-error-500/10 dark:text-error-400">
                                    Priority
                                </span>
                            </div>
                        </template>

                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-center justify-between">
                            <button @click="prevQueuePage()" :disabled="queuePage <= 1"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
                                Prev
                            </button>

                            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500">
                                <span x-text="queueItems.length"></span> waiting · est. ±<span x-text="queueItems.length * 15"></span> min
                            </p>

                            <button @click="nextQueuePage()" :disabled="queuePage >= totalQueuePages"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                Next
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- Empty State (hidden by default, shown when no tickets) --}}
        <div id="emptyState" class="hidden mt-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-12 sm:p-16 flex flex-col items-center justify-center text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-success-50 dark:bg-success-500/10 mb-6">
                    <svg class="w-10 h-10 text-success-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mb-2">Semua Beres!</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm">Tidak ada antrian yang menunggu saat ini. Tiket baru akan muncul secara otomatis di sini.</p>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('queueBoard', () => ({
            searchQuery: '',
            queuePage: 1,
            perPage: 5,

            // Dummy data — will be replaced by backend data later
            queueItems: [
                { token: 'Q-005', title: "PC won't boot", location: 'Cashier 3', reporter: 'Rina', priority: false },
                { token: 'Q-006', title: 'Slow wifi', location: 'Meeting room A', reporter: 'Budi', priority: false },
                { token: 'Q-007', title: 'Attendance app error', location: 'HR', reporter: 'Sari', priority: true },
                { token: 'Q-008', title: 'Monitor flickering', location: 'Finance dept', reporter: 'Dewa', priority: false },
                { token: 'Q-009', title: 'Email not syncing', location: 'Marketing', reporter: 'Lisa', priority: false },
                { token: 'Q-010', title: 'VPN disconnects', location: 'Server room', reporter: 'Rudi', priority: true },
                { token: 'Q-011', title: 'Scanner not detected', location: 'Front office', reporter: 'Maya', priority: false },
                { token: 'Q-012', title: 'Laptop overheating', location: 'Warehouse', reporter: 'Agus', priority: false },
            ],

            get totalQueuePages() {
                return Math.ceil(this.queueItems.length / this.perPage) || 1;
            },

            get paginatedQueue() {
                const start = (this.queuePage - 1) * this.perPage;
                return this.queueItems.slice(start, start + this.perPage);
            },

            nextQueuePage() {
                if (this.queuePage < this.totalQueuePages) this.queuePage++;
            },

            prevQueuePage() {
                if (this.queuePage > 1) this.queuePage--;
            },

            init() {
                // Auto-refresh logic will go here
            },

            filterQueue() {
                // Search/filter logic will go here
            }
        }));
    });
</script>
@endpush
