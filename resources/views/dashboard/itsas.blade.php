@extends('layouts.app')

@section('content')
<!-- Wrap everything in dashboardData Alpine component -->
<div x-data="dashboardData()" id="dashboardContainer" class="relative">
    
    <!-- Global Loading Overlay -->
    <div x-show="isLoading" x-transition.opacity class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 z-50 flex flex-col items-center justify-center rounded-[2rem] backdrop-blur-sm" style="display: none;">
        <svg class="animate-spin h-8 w-8 text-brand-500 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Memuat data...</span>
    </div>

    <!-- The swappable content area -->
    <div id="dashboardContent">
        <!-- Top Header / Greeting -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    Halo, {{ auth()->user()?->name ?? 'User' }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1.5 font-medium">
                    Berikut adalah ringkasan data inventori IT SAS Anda.
                </p>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select @change="updatePeriod($event)" class="flex-1 sm:flex-none bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl px-5 py-3 text-sm font-bold text-gray-700 dark:text-gray-300 shadow-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 cursor-pointer outline-none hover:border-gray-300 dark:hover:border-gray-700 transition-colors">
                    <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="7days" {{ request('period') == '7days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="30days" {{ request('period') == '30days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="lastmonth" {{ request('period') == 'lastmonth' ? 'selected' : '' }}>Bulan Lalu</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <!-- Left Column (Overview + Chart) -->
            <div class="xl:col-span-2 flex flex-col gap-8">
                
                <!-- Overview Bento Box -->
                <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-gray-800">
            <!-- Stat Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                
                <!-- Stat Card 1 -->
                <div class="rounded-3xl border border-gray-100 dark:border-gray-800 p-7 flex flex-col hover:border-brand-500/30 transition-colors bg-gray-50/50 dark:bg-gray-800/20">
                    <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400 mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="font-bold text-sm tracking-wide">Total PC / IP</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <h3 class="text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $totalInventory }}</h3>
                        <div class="flex flex-col">
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 font-semibold mt-1 uppercase tracking-wider">vs last month</span>
                        </div>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="rounded-3xl border border-gray-100 dark:border-gray-800 p-7 flex flex-col hover:border-emerald-500/30 transition-colors bg-gray-50/50 dark:bg-gray-800/20">
                    <div class="flex items-center gap-3 text-gray-500 dark:text-gray-400 mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="font-bold text-sm tracking-wide">Departemen</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <h3 class="text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $totalDepartments }}</h3>
                        <div class="flex flex-col">
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 font-semibold mt-1 uppercase tracking-wider">vs last month</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Chart Bento Box -->
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Sebaran per Departemen</h2>
                <div class="relative">
                    <select @change="updatePeriod($event)" class="appearance-none bg-gray-50 border border-gray-200 text-gray-700 py-2 pl-4 pr-10 rounded-xl leading-tight focus:outline-none focus:bg-white focus:border-brand-500 text-sm font-medium dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                        <option value="all" {{ request('period') == 'all' || !request('period') ? 'selected' : '' }}>Semua Data</option>
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-200 dark:border-gray-800 mb-6 w-full gap-2">
                <template x-for="tab in ['Semua', 'HO', 'BP', 'PR']" :key="tab">
                    <button type="button" @click="changeChartTab(tab)"
                            :class="activeChartTab === tab ? 'border-brand-500 text-brand-600 dark:text-brand-400 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 font-medium'"
                            class="px-6 py-2.5 border-b-2 text-sm transition-colors uppercase tracking-widest"
                            x-text="tab"></button>
                </template>
            </div>

            <div class="flex-1 w-full min-h-[300px]">
                <div id="departmentChart" class="w-full h-full"></div>
            </div>
        </div>

    </div>

    <!-- Right Column (Activities) -->
    <div class="flex flex-col gap-8">
        
        <!-- Log Aktivitas Bento Box -->
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-gray-800 h-full flex flex-col">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Log Aktivitas</h2>
                <select @change="updateLogPerPage($event)" class="bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-lg px-3 py-1.5 text-xs font-semibold text-gray-600 dark:text-gray-300 focus:ring-0 cursor-pointer outline-none">
                    <option value="5" {{ request('log_per_page') == 5 ? 'selected' : '' }}>5 Data</option>
                    <option value="10" {{ request('log_per_page') == 10 ? 'selected' : '' }}>10 Data</option>
                    <option value="20" {{ request('log_per_page') == 20 ? 'selected' : '' }}>20 Data</option>
                </select>
            </div>
            
            <div class="flex flex-col gap-6 flex-1">
                @forelse($recentActivities as $log)
                <div class="flex items-center gap-4 group cursor-pointer p-2 -m-2 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-extrabold text-gray-900 dark:text-white truncate">{{ $log->user->name ?? 'User' }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium truncate mt-0.5">{{ $log->description }}</p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-semibold mt-1 uppercase tracking-wider">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                    
                    <!-- Status Badge -->
                    <div class="text-right">
                        <span class="inline-flex items-center text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider 
                            {{ $log->action == 'create' ? 'text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10' : 
                              ($log->action == 'delete' ? 'text-red-600 bg-red-50 dark:bg-red-500/10' : 
                              'text-blue-600 bg-blue-50 dark:bg-blue-500/10') }}">
                            {{ $log->action }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-10 flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Belum Ada Aktivitas</p>
                    <p class="text-xs text-gray-500 mt-1">Aktivitas penambahan IP akan muncul di sini.</p>
                </div>
                @endforelse
            </div>
            
            <style>
                .log-pagination nav > div.sm\:flex {
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: center !important;
                    gap: 0.75rem !important;
                }
            </style>
            <div class="mt-8 pt-2 log-pagination">
                {{ $recentActivities->onEachSide(0)->links() }}
            </div>
        </div>

    </div>
</div>
</div>
<div id="chartDataStore" data-chart="{{ json_encode($chartData) }}" style="display: none;"></div>

@endsection

@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let dashboardChartInstance = null;

    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardData', () => ({
            isLoading: false,
            activeChartTab: 'Semua',
            
            init() {
                this.renderChart();
                this.$el.addEventListener('click', (e) => {
                    const link = e.target.closest('nav[role="navigation"] a');
                    if (link) {
                        e.preventDefault();
                        this.fetchData(link.href);
                    }
                });
            },
            
            changeChartTab(tab) {
                this.activeChartTab = tab;
                if (dashboardChartInstance) {
                    const store = document.getElementById('chartDataStore');
                    if (!store) return;
                    const allData = JSON.parse(store.dataset.chart || '{}');
                    const activeData = allData[tab] || {labels: [], series: []};
                    
                    const treemapData = activeData.labels.map((label, i) => ({
                        x: label,
                        y: activeData.series[i]
                    }));

                    dashboardChartInstance.updateSeries([{
                        data: treemapData
                    }]);
                }
            },

            renderChart() {
                const chartElement = document.querySelector("#departmentChart");
                if (!chartElement) return;

                const store = document.getElementById('chartDataStore');
                if (!store) return;
                
                const allData = JSON.parse(store.dataset.chart || '{}');
                const activeData = allData[this.activeChartTab] || {labels: [], series: []};
                
                const labels = activeData.labels;
                const series = activeData.series;

                if (dashboardChartInstance) {
                    dashboardChartInstance.destroy();
                }

                const isDark = document.documentElement.classList.contains('dark');

                // Build treemap data: [{x: 'Dept Name', y: count}, ...]
                const treemapData = labels.map((label, i) => ({
                    x: label,
                    y: series[i]
                }));

                const palette = [
                    '#6366f1', '#8b5cf6', '#a78bfa', '#c4b5fd',
                    '#818cf8', '#7c3aed', '#5b21b6', '#4f46e5',
                    '#4338ca', '#3730a3', '#6d28d9', '#9333ea'
                ];

                const options = {
                    series: [{ data: treemapData }],
                    chart: { 
                        type: 'treemap', 
                        height: 320, 
                        toolbar: { show: false }, 
                        fontFamily: 'inherit',
                        animations: { enabled: true, easing: 'easeinout', speed: 800 }
                    },
                    colors: palette,
                    plotOptions: {
                        treemap: {
                            distributed: true,
                            enableShades: false
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        style: { fontSize: '14px', fontWeight: 700 },
                        formatter: function(text, op) {
                            return [text, op.value + ' PC'];
                        },
                        offsetY: -2
                    },
                    legend: { show: false },
                    tooltip: { 
                        y: { formatter: function (val) { return val + " PC terdaftar" } },
                        theme: isDark ? 'dark' : 'light',
                        style: { fontSize: '13px', fontFamily: 'inherit' }
                    }
                };

                dashboardChartInstance = new ApexCharts(chartElement, options);
                dashboardChartInstance.render();
            },
            
            updatePeriod(event) {
                let url = new URL(window.location.href);
                url.searchParams.set('period', event.target.value);
                url.searchParams.delete('page');
                this.fetchData(url.toString());
            },

            updateLogPerPage(event) {
                let url = new URL(window.location.href);
                url.searchParams.set('log_per_page', event.target.value);
                url.searchParams.delete('page');
                this.fetchData(url.toString());
            },
            
            fetchData(url) {
                this.isLoading = true;
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.querySelector('#dashboardContent');
                    if (newContent) {
                        document.querySelector('#dashboardContent').innerHTML = newContent.innerHTML;
                        const newStore = doc.querySelector('#chartDataStore');
                        if(newStore) {
                            document.querySelector('#chartDataStore').dataset.chart = newStore.dataset.chart;
                            this.renderChart();
                        }
                    }
                    window.history.pushState({}, '', url);
                })
                .finally(() => { this.isLoading = false; });
            }
        }));
    });
</script>
