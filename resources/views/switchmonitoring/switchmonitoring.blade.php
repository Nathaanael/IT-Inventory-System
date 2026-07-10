@extends('layouts.app')

@section('content')

<div class="px-4 sm:px-6 lg:px-8 py-4 w-full max-w-9xl mx-auto" x-data="switchMonitor()">

    <!-- Main Content Wrapper -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700/60 overflow-hidden">
        
        <!-- Header + Stats Row -->
        <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3 px-6 py-4 border-b border-gray-200 dark:border-gray-700/60">
            <!-- Title (left) -->
            <div class="flex-shrink-0">
                <h1 class="text-lg font-bold text-gray-900 dark:text-white">Network Switch Monitor</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400">Auto-refresh tiap 30 detik</p>
            </div>
            
            <!-- Stats (center) -->
            <div class="flex items-center gap-6 divide-x divide-gray-200 dark:divide-gray-700/60">
                <div class="pr-6">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Total</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white" x-text="totalSwitches">{{ $totalSwitchesAll }}</div>
                </div>
                <div class="pl-6 pr-6">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-green-600 dark:text-green-500 flex items-center gap-1">
                        <span class="relative flex h-1.5 w-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-green-500"></span></span>
                        Online
                    </div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-400" x-text="onlineCount">0</div>
                </div>
                <div class="pl-6 pr-6">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-red-500 dark:text-red-400">Offline</div>
                    <div class="text-2xl font-bold" :class="offlineCount > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-300 dark:text-gray-600'" x-text="offlineCount">0</div>
                </div>
                <div class="pl-6">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Uptime</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white"><span x-text="uptimePercentage">0</span><span class="text-sm text-gray-400">%</span></div>
                </div>
            </div>
            
            <!-- Search + Clock (right) -->
            <div class="flex items-center gap-4 flex-shrink-0">
                <!-- Live Search -->
                <!-- <form action="" class="flex w-full sm:w-auto" @submit.prevent>
                    <div class="relative flex-1 sm:w-56">
                        <input 
                            type="text" 
                            x-model="searchQuery"
                            @input.debounce.500ms="liveSearch()"
                            placeholder="Cari switch atau panel..." 
                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500"
                        />
                        <button type="submit" class="absolute right-3 top-2.5 text-gray-400 hover:text-brand-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form> -->
                <!-- Clock -->
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white font-mono tracking-wider" x-text="currentTime">--:--</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400" x-text="currentDate">-- --- ----</div>
                </div>
            </div>
        </div>

        <!-- Panel Groups -->
        <div id="switchmonitor-content" class="p-5 md:p-6 space-y-6">
        <div id="switchmonitor-content" class="p-5 md:p-6">
            <!-- Switch Cards Grid (Flattened) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3">
                @php $hasSwitches = false; @endphp
                @foreach($panels as $panel)
                    @foreach($panel->dataSwitches as $switch)
                        @php 
                            $hasSwitches = true; 
                            $escapedNotes = htmlspecialchars($switch->notes ?? '', ENT_QUOTES, 'UTF-8');
                        @endphp
                        <div 
                            x-data="switchCard({{ $switch->id }}, '{{ addslashes($panel->name) }}', '{{ addslashes($switch->merk) }}', '{{ $switch->ip_address }}', '{{ $escapedNotes }}')"
                            @click="openDetails()"
                            class="rounded-lg p-3 border transition-colors duration-300 cursor-pointer hover:brightness-110 shadow-sm"
                            :class="{
                                'bg-gray-100 border-gray-200 dark:bg-gray-800 dark:border-gray-700': status === 'checking',
                                'bg-green-50 border-green-200 dark:bg-[#0a2718] dark:border-[#103d25]': status === 'online',
                                'bg-red-50 border-red-200 dark:bg-[#3a0f14] dark:border-[#5c1820]': status === 'offline'
                            }"
                        >
                            <div class="text-xs font-semibold mb-1 truncate" 
                                :class="{
                                    'text-gray-600 dark:text-gray-400': status === 'checking',
                                    'text-green-700 dark:text-green-500': status === 'online',
                                    'text-red-700 dark:text-red-400': status === 'offline'
                                }"
                                x-text="panelName"></div>
                            <div class="text-lg font-bold tracking-tight" 
                                :class="{
                                    'text-gray-800 dark:text-gray-300': status === 'checking',
                                    'text-gray-900 dark:text-white': status === 'online' || status === 'offline'
                                }"
                                x-text="ip"></div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            @if(!$hasSwitches)
                <!-- Empty State -->
                <div class="py-16 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Tidak ada switch ditemukan</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Coba ubah kata kunci pencarian atau tambahkan data switch baru.</p>
                </div>
            @endif
        </div>

        <!-- Detail Modal -->
        <div x-show="showModal" 
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-gray-900/50 backdrop-blur-sm transition-opacity"
             x-transition.opacity
             style="display: none;">
             
             <!-- Modal Content -->
             <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all"
                  @click.away="showModal = false"
                  x-transition:enter="ease-out duration-300"
                  x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                  x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                  x-transition:leave="ease-in duration-200"
                  x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                  x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                 
                 <div class="p-6 border-b border-gray-100 dark:border-gray-700/60 flex justify-between items-center">
                     <h3 class="text-lg font-bold text-gray-900 dark:text-white">Detail Switch</h3>
                     <button @click="showModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                     </button>
                 </div>
                 
                 <div class="p-6 space-y-4">
                     <div>
                         <label class="text-xs font-semibold text-gray-500 uppercase">Panel</label>
                         <p class="text-gray-900 dark:text-white font-medium text-lg" x-text="selectedSwitch.panelName"></p>
                     </div>
                     <div>
                         <label class="text-xs font-semibold text-gray-500 uppercase">Merk / Model</label>
                         <p class="text-gray-900 dark:text-white font-medium text-lg" x-text="selectedSwitch.merk"></p>
                     </div>
                     <div>
                         <label class="text-xs font-semibold text-gray-500 uppercase">IP Address</label>
                         <p class="text-gray-900 dark:text-white font-mono font-medium text-lg" x-text="selectedSwitch.ip"></p>
                     </div>
                     <div>
                         <label class="text-xs font-semibold text-gray-500 uppercase">Status</label>
                         <div class="mt-1">
                             <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-sm font-semibold"
                                   :class="{
                                       'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300': selectedSwitch.status === 'checking',
                                       'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': selectedSwitch.status === 'online',
                                       'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': selectedSwitch.status === 'offline'
                                   }">
                                 <span class="w-1.5 h-1.5 rounded-full" 
                                       :class="{
                                           'bg-gray-500': selectedSwitch.status === 'checking',
                                           'bg-green-500': selectedSwitch.status === 'online',
                                           'bg-red-500': selectedSwitch.status === 'offline'
                                       }"></span>
                                 <span x-text="selectedSwitch.status === 'offline' ? 'Offline' : (selectedSwitch.status === 'checking' ? 'Checking...' : 'Online')"></span>
                             </span>
                         </div>
                     </div>
                     <div x-show="selectedSwitch.notes">
                         <label class="text-xs font-semibold text-gray-500 uppercase">Notes</label>
                         <p class="text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-lg border border-gray-100 dark:border-gray-700/50 mt-1" x-text="selectedSwitch.notes"></p>
                     </div>
                 </div>
                 
                 <div class="p-6 border-t border-gray-100 dark:border-gray-700/60 bg-gray-50 dark:bg-gray-800/50 flex justify-end">
                     <button @click="showModal = false" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">Tutup</button>
                 </div>
             </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Individual switch card - handles its own ping
        Alpine.data('switchCard', (id, panelName, merk, ip, notes) => ({
            id: id,
            panelName: panelName,
            merk: merk,
            ip: ip,
            notes: notes,
            status: 'checking',
            pingInterval: null,

            init() {
                // Random delay to stagger requests
                const delay = Math.random() * 2000;
                setTimeout(() => this.doPing(), delay);
                
                // Auto-ping every 5 minutes (300000 ms)
                this.pingInterval = setInterval(() => {
                    this.status = 'checking';
                    const d = Math.random() * 2000;
                    setTimeout(() => this.doPing(), d);
                }, 300000);
            },

            destroy() {
                if (this.pingInterval) clearInterval(this.pingInterval);
            },

            async doPing() {
                try {
                    const response = await fetch(`/switchmonitoring/${this.id}/ping`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const result = await response.json();
                    this.status = result.status;
                } catch (e) {
                    this.status = 'offline';
                }
                // Dispatch event so parent can recalculate stats
                this.$dispatch('ping-updated');
            },
            
            openDetails() {
                this.$dispatch('open-modal', {
                    panelName: this.panelName,
                    merk: this.merk,
                    ip: this.ip,
                    status: this.status,
                    notes: this.notes
                });
            }
        }));

        // Main monitor component - handles search, stats, clock
        Alpine.data('switchMonitor', () => ({
            showModal: false,
            selectedSwitch: { panelName: '', merk: '', ip: '', status: '', notes: '' },
            currentTime: '',
            currentDate: '',
            searchQuery: '{{ request('search', '') }}',
            totalSwitches: {{ $totalSwitchesAll }},
            onlineCount: 0,
            offlineCount: 0,
            uptimePercentage: '0.0',
            
            init() {
                this.updateTime();
                setInterval(() => this.updateTime(), 1000);

                // Listen for ping updates from child cards to recalculate stats
                this.$el.addEventListener('ping-updated', () => this.calculateStats());
                
                // Listen for modal open event
                this.$el.addEventListener('open-modal', (e) => {
                    this.selectedSwitch = e.detail;
                    this.showModal = true;
                });

                // Initial stats calculation after a short delay for pings to complete
                setTimeout(() => this.calculateStats(), 5000);
            },
            
            updateTime() {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace(/:/g, '.');
                this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            },

            liveSearch() {
                const url = new URL(window.location.href);
                if (this.searchQuery) {
                    url.searchParams.set('search', this.searchQuery);
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.delete('page'); // Reset ke halaman 1

                fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newContent = doc.getElementById('switchmonitor-content');
                    const newPagination = doc.getElementById('switchmonitor-pagination');

                    if (newContent) {
                        document.getElementById('switchmonitor-content').innerHTML = newContent.innerHTML;
                    }
                    
                    const paginationEl = document.getElementById('switchmonitor-pagination');
                    if (newPagination) {
                        if (paginationEl) {
                            paginationEl.innerHTML = newPagination.innerHTML;
                        } else {
                            // Create pagination if it didn't exist
                            const wrapper = document.querySelector('#switchmonitor-content').parentElement;
                            const div = document.createElement('div');
                            div.id = 'switchmonitor-pagination';
                            div.className = 'px-6 py-4 border-t border-gray-200 dark:border-gray-700/60';
                            div.innerHTML = newPagination.innerHTML;
                            wrapper.appendChild(div);
                        }
                    } else if (paginationEl) {
                        paginationEl.remove();
                    }

                    // Update browser URL without reload
                    window.history.replaceState({}, '', url.toString());
                });
            },
            
            calculateStats() {
                // Count all switchCard components on the page
                const cards = this.$el.querySelectorAll('[x-data^="switchCard"]');
                let online = 0;
                let offline = 0;
                let total = cards.length;
                
                cards.forEach(card => {
                    const alpine = card._x_dataStack?.[0];
                    if (alpine) {
                        if (alpine.status === 'online') online++;
                        if (alpine.status === 'offline') offline++;
                    }
                });
                
                this.onlineCount = online;
                this.offlineCount = offline;
                
                const completed = online + offline;
                if (completed > 0) {
                    this.uptimePercentage = ((online / completed) * 100).toFixed(1);
                }
            }
        }));
    });
</script>
@endpush
@endsection
