@extends('layouts.app')

@section('content')

<div class="px-4 sm:px-6 lg:px-8 py-4 w-full max-w-9xl mx-auto" x-data="switchMonitor()">

    <!-- Main Content Wrapper -->
    <div id="fullscreen-container" 
         class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700/60 overflow-y-auto"
         :class="{'rounded-none border-none fixed inset-0 z-[50] h-screen w-screen': isFullscreen}">
        
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
            
            <!-- Clock + Fullscreen (right) -->
            <div class="flex items-center gap-4 flex-shrink-0">
                <!-- Clock -->
                <div class="text-right">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white font-mono tracking-wider" x-text="currentTime">--:--</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400" x-text="currentDate">-- --- ----</div>
                </div>
                
                <!-- Fullscreen Toggle -->
                <button @click="toggleFullscreen()" class="p-2 rounded-lg text-gray-400 hover:text-gray-900 dark:hover:text-white bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors ml-2" title="Toggle Fullscreen">
                    <svg x-show="!isFullscreen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                    <svg x-show="isFullscreen" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20v-4m0 0H5m4 0l-5 5m11-5v4m0-4h4m-4 0l5 5M9 4v4m0 0H5m4 0L4 3m11 5V4m0 4h4m-4 0l5-5"></path></svg>
                </button>
            </div>
        </div>

        <!-- Panel Groups -->
        <div id="switchmonitor-content" class="p-5 md:p-6">
            <!-- Switch Cards Grid (Flattened) -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3">
                @php $hasSwitches = false; @endphp
                @foreach($panels as $panel)
                    @foreach($panel->dataSwitches as $switch)
                        @php $hasSwitches = true; @endphp
                        <div 
                            x-data="switchCard({{ $switch->id }}, {{ Js::from($panel->name) }}, {{ Js::from($switch->merk) }}, {{ Js::from($switch->ip_address) }}, {{ Js::from($switch->notes ?? '') }})"
                            @click="openDetails()"
                            @status-update.window="updateStatus($event.detail)"
                            class="rounded-lg p-3 border transition-colors duration-300 cursor-pointer hover:brightness-110 shadow-sm"
                            :class="{
                                'bg-gray-100 border-gray-200 dark:bg-gray-800 dark:border-gray-700': status === 'checking',
                                'bg-green-50 border-green-200 dark:bg-[#0a2718] dark:border-[#103d25]': status === 'online',
                                'bg-red-50 border-red-200 dark:bg-[#3a0f14] dark:border-[#5c1820]': status === 'offline'
                            }"
                        >
                            <div class="flex justify-between items-start mb-1">
                                <div class="text-xs font-semibold truncate pr-2" 
                                    :class="{
                                        'text-gray-600 dark:text-gray-400': status === 'checking',
                                        'text-green-700 dark:text-green-500': status === 'online',
                                        'text-red-700 dark:text-red-400': status === 'offline'
                                    }"
                                    x-text="panelName"></div>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider"
                                    :class="{
                                        'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300': status === 'checking',
                                        'bg-green-200 text-green-800 dark:bg-green-900/60 dark:text-green-300': status === 'online',
                                        'bg-red-200 text-red-800 dark:bg-red-900/60 dark:text-red-300': status === 'offline'
                                    }"
                                    x-text="status === 'online' ? 'ON' : (status === 'offline' ? 'OFF' : 'CHK')"></span>
                            </div>
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
    </div>
    <!-- Detail Modal -->
    <template x-teleport="body">
        <div 
            x-show="showModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm"
            style="display: none;"
        >
             
             <!-- Modal Content -->
             <div 
                  class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-gray-100 dark:border-gray-800"
                  @click.away="showModal = false"
                  x-show="showModal"
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0 scale-95"
                  x-transition:enter-end="opacity-100 scale-100"
                  x-transition:leave="transition ease-in duration-150"
                  x-transition:leave-start="opacity-100 scale-100"
                  x-transition:leave-end="opacity-0 scale-95"
             >
                 
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
                                 <span x-text="selectedSwitch.status === 'online' ? 'Online' : (selectedSwitch.status === 'checking' ? 'Checking...' : 'Offline')"></span>
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
    </template>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Individual switch card — no longer pings on its own.
        // Status is received from the parent switchMonitor via 'status-update' event.
        Alpine.data('switchCard', (id, panelName, merk, ip, notes) => ({
            id: id,
            panelName: panelName,
            merk: merk,
            ip: ip,
            notes: notes,
            status: 'checking',

            updateStatus(statuses) {
                if (statuses && statuses[this.id] !== undefined) {
                    // Only 'online' is treated as online; anything else → offline (fail-safe)
                    this.status = statuses[this.id] === 'online' ? 'online' : 'offline';
                }
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

        // Main monitor component — handles centralized status polling, stats, clock
        Alpine.data('switchMonitor', () => ({
            showModal: false,
            isFullscreen: false,
            selectedSwitch: { panelName: '', merk: '', ip: '', status: '', notes: '' },
            currentTime: '',
            currentDate: '',
            totalSwitches: {{ $totalSwitchesAll }},
            onlineCount: 0,
            offlineCount: 0,
            uptimePercentage: '0.0',
            pollInterval: null,
            wakeLock: null,
            
            init() {
                // Clock
                this.updateTime();
                setInterval(() => this.updateTime(), 1000);

                // Listen for modal open event from child cards
                this.$el.addEventListener('open-modal', (e) => {
                    this.selectedSwitch = e.detail;
                    this.showModal = true;
                });

                // Centralized status polling — fetch all statuses at once
                this.fetchAllStatuses();
                this.pollInterval = setInterval(() => this.fetchAllStatuses(), 30000);
                
                // Watch for fullscreen changes (e.g., if user presses ESC key)
                document.addEventListener('fullscreenchange', async () => {
                    this.isFullscreen = !!document.fullscreenElement;
                    
                    // Manage Screen Wake Lock to prevent PC from sleeping
                    if (this.isFullscreen) {
                        try {
                            if ('wakeLock' in navigator) {
                                this.wakeLock = await navigator.wakeLock.request('screen');
                                console.log('Screen Wake Lock is active');
                            }
                        } catch (err) {
                            console.error('Wake Lock error:', err);
                        }
                    } else {
                        if (this.wakeLock !== null) {
                            this.wakeLock.release().then(() => {
                                this.wakeLock = null;
                                console.log('Screen Wake Lock released');
                            });
                        }
                    }
                });
            },

            destroy() {
                if (this.pollInterval) clearInterval(this.pollInterval);
            },

            async fetchAllStatuses() {
                try {
                    const response = await fetch('/switchmonitoring/status', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const statuses = await response.json();

                    // Dispatch status data to all child switchCard components
                    this.$dispatch('status-update', statuses);

                    // Calculate stats directly from the response
                    this.calculateStatsFromData(statuses);
                } catch (e) {
                    console.warn('Failed to fetch switch statuses:', e);
                    // On failure, set all to offline (fail-safe)
                    this.setAllOffline();
                }
            },

            calculateStatsFromData(statuses) {
                let online = 0;
                let offline = 0;

                for (const [id, status] of Object.entries(statuses)) {
                    if (status === 'online') {
                        online++;
                    } else {
                        offline++;
                    }
                }

                this.onlineCount = online;
                this.offlineCount = offline;

                const total = online + offline;
                if (total > 0) {
                    this.uptimePercentage = ((online / total) * 100).toFixed(1);
                } else {
                    this.uptimePercentage = '0.0';
                }
            },

            setAllOffline() {
                // Dispatch empty object so cards default to offline
                this.$dispatch('status-update', {});
                this.onlineCount = 0;
                this.offlineCount = this.totalSwitches;
                this.uptimePercentage = '0.0';
            },
            
            toggleFullscreen() {
                const elem = document.getElementById('fullscreen-container');
                if (!this.isFullscreen) {
                    if (elem.requestFullscreen) {
                        elem.requestFullscreen();
                    } else if (elem.webkitRequestFullscreen) { /* Safari */
                        elem.webkitRequestFullscreen();
                    } else if (elem.msRequestFullscreen) { /* IE11 */
                        elem.msRequestFullscreen();
                    }
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) { /* Safari */
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) { /* IE11 */
                        document.msExitFullscreen();
                    }
                }
            },
            
            updateTime() {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace(/:/g, '.');
                this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            }
        }));
    });
</script>
@endpush
@endsection
