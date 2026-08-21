@extends('layouts.app')

@section('content')
<div x-data="envMonitoringData()" id="envContainer" class="grid grid-cols-1 gap-6 relative">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
    
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">
                Environment Monitoring
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Pantau suhu dan kelembaban ruangan secara real-time.
            </p>
        </div>
        
        <!-- Action Buttons & Filters -->
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Filter Perangkat -->
            <div class="relative flex flex-wrap sm:flex-nowrap gap-2 items-center">
                <select x-model="selectedMacAddress" @change="fetchData()" class="w-full sm:w-auto rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500 cursor-pointer transition-colors">
                    <option value="" class="dark:bg-gray-800">Semua Perangkat</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->mac_address }}" class="dark:bg-gray-800">{{ $device->nama_perangkat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Waktu -->
            <div class="relative flex flex-wrap sm:flex-nowrap gap-2 items-center">
                <select x-model="period" @change="updatePeriod($event)" class="w-full sm:w-auto rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-500 cursor-pointer transition-colors">
                    <option value="today" class="dark:bg-gray-800">Hari Ini</option>
                    <option value="7days" class="dark:bg-gray-800">7 Hari Terakhir</option>
                    <option value="30days" class="dark:bg-gray-800">30 Hari Terakhir</option>
                </select>

                <!-- Input Tanggal Kustom dengan DatePicker Component (Flatpickr) -->
                <div class="w-full sm:w-80">
                    <x-form.date-picker 
                        id="customDateRange"
                        mode="range"
                        placeholder="Pilih Rentang Tanggal"
                        dateFormat="d F Y"
                        @date-change="handleDateRange($event.detail)"
                    />
                </div>
            </div>

            <!-- Tombol Download Excel -->
            <button @click="downloadExcel()" class="flex-shrink-0 flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </button>
        </div>
    </div>

    <!-- Charts Container -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
        
        <!-- Chart Suhu -->
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-gray-800">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">Grafik Suhu</h2>
                    <p class="text-xs text-gray-500 mt-1">Dalam derajat Celcius (°C)</p>
                </div>
                <div class="p-3 bg-red-50 dark:bg-red-500/10 rounded-xl">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
            <div class="w-full min-h-[300px]" id="temperatureChart"></div>
        </div>

        <!-- Chart Kelembaban -->
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-gray-100 dark:border-gray-800">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-extrabold text-gray-900 dark:text-white">Grafik Kelembaban</h2>
                    <p class="text-xs text-gray-500 mt-1">Dalam persentase (%)</p>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-500/10 rounded-xl">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                </div>
            </div>
            <div class="w-full min-h-[300px]" id="humidityChart"></div>
        </div>

    </div>

    <!-- Data Table Container -->
    <!-- <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-800/50 dark:text-gray-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-medium">Waktu</th>
                        <th scope="col" class="px-6 py-4 font-medium">Suhu (°C)</th>
                        <th scope="col" class="px-6 py-4 font-medium">Kelembaban (%)</th>
                        <th scope="col" class="px-6 py-4 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <template x-if="tableData.length === 0">
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                Belum ada data dari sensor IoT.
                            </td>
                        </tr>
                    </template>
                    <template x-for="item in tableData" :key="item.id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4" x-text="item.waktu"></td>
                            <td class="px-6 py-4 font-medium" :class="item.is_danger ? 'text-red-500' : 'text-gray-900 dark:text-white'" x-text="item.suhu + ' °C'">
                            </td>
                            <td class="px-6 py-4" x-text="item.kelembaban + ' %'"></td>
                            <td class="px-6 py-4">
                                <template x-if="item.is_danger">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-600 dark:bg-red-500/10 dark:text-red-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                        Bahaya
                                    </span>
                                </template>
                                <template x-if="!item.is_danger">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Aman
                                    </span>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div> -->

    </div>
</div>
@endsection

@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('envMonitoringData', () => ({
            tableData: [],
            chartCategories: [],
            tempChartData: [],
            humidChartData: [],
            period: 'today',
            startDate: '',
            endDate: '',
            selectedMacAddress: '',
            tempChart: null,
            humidChart: null,
            pollInterval: null,

            init() {
                this.fetchData();
                this.pollInterval = setInterval(() => {
                    this.fetchData();
                }, 15000); // Auto reload setiap 15 detik untuk testing
            },

            async fetchData() {
                try {
                    let url = '{{ route("envmonitoring.data") }}';
                    if (this.selectedMacAddress) {
                        url += '?mac_address=' + encodeURIComponent(this.selectedMacAddress);
                    }
                    const response = await fetch(url);
                    const data = await response.json();
                    
                    this.tableData = data.table;
                    this.chartCategories = data.chart.map(item => item.time);
                    this.tempChartData = data.chart.map(item => item.suhu);
                    this.humidChartData = data.chart.map(item => item.kelembaban);
                    
                    this.renderCharts();
                } catch (error) {
                    console.error('Failed to fetch sensor data:', error);
                }
            },

            updatePeriod(event) {
                this.period = event.target.value;
                console.log('Filter period:', this.period);
                if (this.period !== 'custom') {
                    // Logic to reload page with filter can go here
                }
            },

            handleDateRange(detail) {
                if (detail.selectedDates && detail.selectedDates.length === 2) {
                    const formatDate = (date) => {
                        const d = new Date(date);
                        let month = '' + (d.getMonth() + 1);
                        let day = '' + d.getDate();
                        const year = d.getFullYear();
                        if (month.length < 2) month = '0' + month;
                        if (day.length < 2) day = '0' + day;
                        return [year, month, day].join('-');
                    };
                    
                    this.startDate = formatDate(detail.selectedDates[0]);
                    this.endDate = formatDate(detail.selectedDates[1]);
                }
            },

            downloadExcel() {
                alert('Fitur Download Excel akan segera diimplementasikan.');
            },

            renderCharts() {
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#9ca3af' : '#6b7280';

                if (this.tempChart) this.tempChart.destroy();
                if (this.humidChart) this.humidChart.destroy();

                const commonOptions = {
                    chart: {
                        type: 'area',
                        height: 320,
                        toolbar: { show: false },
                        fontFamily: 'inherit',
                        animations: { enabled: true }
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    xaxis: {
                        categories: this.chartCategories.length > 0 ? this.chartCategories : ['Belum ada data'],
                        labels: { style: { colors: textColor, fontWeight: 500 } },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: { style: { colors: textColor, fontWeight: 500 } }
                    },
                    grid: {
                        borderColor: isDark ? '#374151' : '#f3f4f6',
                        strokeDashArray: 4,
                        padding: { top: 0, right: 0, bottom: 0, left: 10 }
                    }
                };

                // Suhu (Merah)
                const tempOptions = {
                    ...commonOptions,
                    series: [{ name: 'Suhu', data: this.tempChartData.length > 0 ? this.tempChartData : [0] }],
                    colors: ['#ef4444'],
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 100] }
                    },
                    tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: (val) => val + " °C" } }
                };

                // Kelembaban (Biru)
                const humidOptions = {
                    ...commonOptions,
                    series: [{ name: 'Kelembaban', data: this.humidChartData.length > 0 ? this.humidChartData : [0] }],
                    colors: ['#3b82f6'],
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05, stops: [0, 100] }
                    },
                    tooltip: { theme: isDark ? 'dark' : 'light', y: { formatter: (val) => val + " %" } }
                };

                this.tempChart = new ApexCharts(document.querySelector("#temperatureChart"), tempOptions);
                this.tempChart.render();

                this.humidChart = new ApexCharts(document.querySelector("#humidityChart"), humidOptions);
                this.humidChart.render();
            }
        }));
    });
</script>
