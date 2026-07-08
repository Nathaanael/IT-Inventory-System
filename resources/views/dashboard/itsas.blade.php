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
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-red-500 bg-red-100 dark:bg-red-500/20 px-2.5 py-1 rounded-md w-max">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                2.4%
                            </span>
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
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-500 bg-emerald-100 dark:bg-emerald-500/20 px-2.5 py-1 rounded-md w-max">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                12.8%
                            </span>
                            <span class="text-[10px] text-gray-400 dark:text-gray-500 font-semibold mt-1 uppercase tracking-wider">vs last month</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Team Avatars Section -->
            <!-- <div class="pt-7 border-t border-gray-100 dark:border-gray-800/60 flex items-center justify-between">
                <div>
                    <h4 class="text-base font-extrabold text-gray-900 dark:text-white mb-1">Aktivitas Tim IT Hari Ini!</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Beberapa staf IT telah mengubah data IP.</p>
                </div>
                <div class="flex items-center gap-5">
                    <div class="flex -space-x-4">
                        <img class="w-12 h-12 rounded-full border-4 border-white dark:border-gray-900 object-cover" src="https://ui-avatars.com/api/?name=IT+Admin&background=4F46E5&color=fff&bold=true" alt="User">
                        <img class="w-12 h-12 rounded-full border-4 border-white dark:border-gray-900 object-cover" src="https://ui-avatars.com/api/?name=Nathan&background=10B981&color=fff&bold=true" alt="User">
                        <img class="w-12 h-12 rounded-full border-4 border-white dark:border-gray-900 object-cover" src="https://ui-avatars.com/api/?name=Siti&background=F59E0B&color=fff&bold=true" alt="User">
                        <img class="w-12 h-12 rounded-full border-4 border-white dark:border-gray-900 object-cover" src="https://ui-avatars.com/api/?name=Budi&background=EF4444&color=fff&bold=true" alt="User">
                    </div>
                    <button class="w-12 h-12 rounded-full border-2 border-gray-100 dark:border-gray-800 flex items-center justify-center text-gray-400 hover:text-gray-900 hover:border-gray-300 dark:hover:text-white dark:hover:border-gray-600 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div> -->
        </div>

        <!-- Chart Bento Box -->
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-gray-800">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Sebaran per Departemen</h2>
                <select class="bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full px-5 py-2.5 text-sm font-semibold text-gray-600 dark:text-gray-300 focus:ring-0 cursor-pointer outline-none">
                    <option>Semua Data</option>
                </select>
            </div>
            
            <div class="relative w-full">
                <!-- Large Background Text (like the $10.2m in the image) -->
                <div class="absolute bottom-4 left-0 text-gray-100 dark:text-gray-800/50 font-black text-6xl tracking-tighter pointer-events-none z-0">
                    {{ $totalInventory }} IP
                </div>
                <!-- The Chart -->
                <div id="chartDepartments" class="relative z-10"></div>
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
            
            <div class="mt-8 pt-2">
                {{ $recentActivities->links() }}
            </div>
        </div>

    </div>
</div>
</div>
<!-- End wrapper dashboardContainer -->
<div id="chartDataStore" data-labels="{{ json_encode($chartLabels) }}" data-series="{{ json_encode($chartSeries) }}" style="display: none;"></div>

@endsection

@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let dashboardChartInstance = null;

    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardData', () => ({
            isLoading: false,
            
            init() {
                // Intercept pagination clicks
                this.$el.addEventListener('click', (e) => {
                    const link = e.target.closest('nav[role="navigation"] a');
                    if (link) {
                        e.preventDefault();
                        this.fetchData(link.href);
                    }
                });
            },
            
            updatePeriod(event) {
                let url = new URL(window.location.href);
                url.searchParams.set('period', event.target.value);
                url.searchParams.delete('page'); // Reset to page 1
                this.fetchData(url.toString());
            },

            updateLogPerPage(event) {
                let url = new URL(window.location.href);
                url.searchParams.set('log_per_page', event.target.value);
                url.searchParams.delete('page'); // Reset to page 1
                this.fetchData(url.toString());
            },
            
            fetchData(url) {
                this.isLoading = true;
                
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Replace content 
                    const newContent = doc.querySelector('#dashboardContent');
                    if (newContent) {
                        document.querySelector('#dashboardContent').innerHTML = newContent.innerHTML;
                        
                        // Re-render chart since the DOM element was replaced
                        const chartDataStore = doc.querySelector('#chartDataStore');
                        if(chartDataStore) {
                            const newLabels = JSON.parse(chartDataStore.dataset.labels);
                            const newSeries = JSON.parse(chartDataStore.dataset.series);
                            window.renderDashboardChart(newLabels, newSeries);
                        }
                    }
                    
                    window.history.pushState({}, '', url);
                })
                .finally(() => {
                    this.isLoading = false;
                });
            }
        }));
    });

    window.renderDashboardChart = function(labels, series) {
        const chartElement = document.querySelector("#chartDepartments");
        if(!chartElement) return;

        if (dashboardChartInstance) {
            dashboardChartInstance.destroy();
        }
        
        // Find the index of the highest value to color it differently
        const maxVal = Math.max(...series);
        const maxIndex = series.indexOf(maxVal);

        const isDark = document.documentElement.classList.contains('dark');
        const inactiveBarColor = isDark ? '#1e293b' : '#f1f5f9'; // slate-800 or slate-100
        const activeBarColor = '#22c55e'; // Bright emerald green

        const options = {
            series: [{
                name: "Jumlah PC / IP",
                data: series
            }],
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'inherit',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: [
                function({ value, seriesIndex, dataPointIndex, w }) {
                    if (dataPointIndex === maxIndex) {
                        return activeBarColor;
                    }
                    return inactiveBarColor;
                }
            ],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '40%',
                    borderRadius: 8,
                    borderRadiusApplication: 'end',
                    distributed: true, // Allow different colors per bar
                },
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false
            },
            stroke: {
                show: false
            },
            xaxis: {
                categories: labels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        colors: isDark ? '#64748b' : '#94a3b8',
                        fontWeight: 700,
                        fontSize: '12px'
                    },
                    offsetY: 5
                }
            },
            yaxis: {
                show: false, // Hide Y axis completely to match the design image
            },
            grid: {
                show: false, // Hide all grid lines
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " PC terdaftar"
                    }
                },
                theme: isDark ? 'dark' : 'light',
                style: {
                    fontSize: '13px',
                    fontFamily: 'inherit'
                }
            }
        };

        dashboardChartInstance = new ApexCharts(chartElement, options);
        dashboardChartInstance.render();
        
        // Ensure we only attach the observer once globally
        if(!window.chartThemeObserver) {
            window.chartThemeObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === "class" && dashboardChartInstance) {
                        const newIsDark = document.documentElement.classList.contains('dark');
                        const newInactiveBarColor = newIsDark ? '#1e293b' : '#f1f5f9';
                        
                        dashboardChartInstance.updateOptions({
                            colors: [
                                function({ value, seriesIndex, dataPointIndex, w }) {
                                    return dataPointIndex === maxIndex ? activeBarColor : newInactiveBarColor;
                                }
                            ],
                            tooltip: { theme: newIsDark ? 'dark' : 'light' },
                            xaxis: {
                                labels: { style: { colors: newIsDark ? '#64748b' : '#94a3b8' } }
                            }
                        });
                    }
                });
            });
            window.chartThemeObserver.observe(document.documentElement, { attributes: true });
        }
    };

    document.addEventListener("DOMContentLoaded", function () {
        const initialLabels = JSON.parse(document.querySelector('#chartDataStore').dataset.labels);
        const initialSeries = JSON.parse(document.querySelector('#chartDataStore').dataset.series);
        window.renderDashboardChart(initialLabels, initialSeries);
    });
</script>
