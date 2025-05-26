<x-app-layout>
    
    <div class="p-3 sm:p-5">
        <div class="mx-auto max-w-screen-2xl">
            <div class="container mx-auto">

                 <!-- Top Section - Cards and Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Summary Cards -->
                <div class="bg-blue-600 text-white p-4 rounded-lg shadow-lg">
                    <h3 class="font-medium text-white/80">Total Findings</h3>
                    <p class="text-3xl font-bold">{{$totalFindings}}</p>
                    <div class="text-sm mt-2 {{ $totalFindingsChange >= 0 ? 'text-white-200' : 'text-white-200' }}">
                        {{ $totalFindingsChange >= 0 ? '↑' : '↓' }} {{ abs($totalFindingsChange) }}% from last month
                    </div>
                    
                </div>
                
                <div class="bg-green-600 text-white p-4 rounded-lg shadow-lg">
                    <h3 class="font-medium text-white/80">Resolved</h3>
                    <p class="text-3xl font-bold">{{$resolved}}</p>
                    <div class="text-sm mt-2 {{ $resolvedChange >= 0 ? 'text-white-200' : 'text-white-200' }}">
                        {{ $resolvedChange >= 0 ? '↑' : '↓' }} {{ abs($resolvedChange) }}% from last month
                    </div>
                </div>
                
                <div class="bg-amber-500 text-white p-4 rounded-lg shadow-lg">
                    <h3 class="font-medium text-white/80">Overdue</h3>
                    <p class="text-3xl font-bold">{{$overdue}}</p>
                    <div class="text-sm mt-2 {{ $overdueChange >= 0 ? 'text-white-200' : 'text-white-200' }}">
                        {{ $overdueChange >= 0 ? '↑' : '↓' }} {{ abs($overdueChange) }}% from last month
                    </div>
                </div>
                
                <div class="bg-red-600 text-white p-4 rounded-lg shadow-lg">
                    <h3 class="font-medium text-white/80">High Risk</h3>
                    <p class="text-3xl font-bold">{{$highRisk}}</p>
                    <div class="text-sm mt-2 {{ $highRiskChange >= 0 ? 'text-white-200' : 'text-white-200' }}">
                        {{ $highRiskChange >= 0 ? '↑' : '↓' }} {{ abs($highRiskChange) }}% from last month
                    </div>
                </div>
            </div>

                @php
                    // Cek apakah pengguna adalah EHS atau bukan
                    if (Auth::guard('ehs')->check()) {
                        // Jika pengguna adalah EHS, ambil role dari relasi 'roles' di model EhsUser
                        $userRole = optional(Auth::guard('ehs')->user()->roles->first())->name;
                    } else {
                        // Jika pengguna bukan EHS, ambil role dari model User dengan roleLct
                        $userRole = optional(auth()->user()->roleLct->first())->name;
                    }
                @endphp


                @php
                    $isEhs = $userRole === 'ehs';
                    $isUser = $userRole === 'user';
                    $isPic = $userRole === 'pic';
                    $isManajer = $userRole === 'manajer';

                    // Hitung jumlah tabel
                    $tableCount = 0;
                    if ($isEhs) $tableCount += 2;
                    if ($isManajer) $tableCount += 1;
                    if ($isUser) $tableCount += 1;
                    if ($isPic) $tableCount += 1;

                    $gridColsClass = $tableCount > 1 ? 'lg:grid-cols-2' : 'lg:grid-cols-1';
                @endphp

                <!-- Tabel Reports Section -->
                <div class="grid grid-cols-1 {{ $gridColsClass }} gap-3 items-start">

                    {{-- EHS --}}
                    @if($isEhs)
                        <!-- New Finding -->
                        <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                            <h2 class="text-base font-semibold mb-2">New Finding</h2>
                            <div class="overflow-auto h-auto"> <!-- Height diseragamkan -->
                                @include('partials.dashboard-tabel-new', [
                                    'laporans' => $laporanNew
                                ])
                            </div>
                        </div>

                        <!-- Report Awaiting Approval -->
                        <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                            <h2 class="text-base font-semibold mb-2">Report Awaiting Approval</h2>
                            <div class="overflow-auto h-auto"> <!-- Height diseragamkan -->
                                @include('partials.dashboard-tabel-approval-ehs', [
                                    'laporans' => $laporanNeedApproval
                                ])
                            </div>
                        </div>
                    @endif

                    {{-- Manager --}}
                    @if($isManajer)
                        <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                            <h2 class="text-base font-semibold mb-2">Report Awaiting Approval</h2>
                            <div class="overflow-auto h-auto"> <!-- Height diseragamkan -->
                                @include('partials.dashboard-tabel-approval-taskbudget', [
                                    'laporans' => $laporanNeedApprovalBudget
                                ])
                            </div>
                        </div>
                    @endif

                    @if($isUser)
                        <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                            <h2 class="text-base font-semibold mb-2">Reporting</h2>
                            <div class="overflow-auto h-auto"> <!-- Height diseragamkan -->
                                @include('partials.dashboard-tabel', [
                                    'laporans' => $laporanUser
                                ])
                            </div>
                        </div>
                    @endif

                    @if($isPic)
                        <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                            <h2 class="text-base font-semibold mb-2">Unread Reports</h2>
                            <div class="overflow-auto h-auto"> <!-- Height diseragamkan -->
                                @include('partials.dashboard-tabel', [
                                    'laporans' => $laporanInProgress
                                ])
                            </div>
                        </div>
                    @endif

                </div>


                <!-- Tabel Reports Section (Overdue & Medium-High Risk) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-4 items-start">
                    
                    <!-- Overdue Reports Table -->
                    <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                        <h2 class="text-base font-semibold mb-2">Overdue Reports</h2>
                        <div class="overflow-auto max-h-[600px]"> <!-- ditingkatkan -->
                            @include('partials.dashboard-tabel', [
                                'laporans' => $laporanOverdue
                            ])
                        </div>
                    </div>
                    
                    <!-- Medium & High Hazard Reports Table -->
                    <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                        <h2 class="text-base font-semibold mb-2">Medium & High Hazard Reports</h2>
                        <div class="overflow-auto max-h-[600px]"> <!-- ditingkatkan -->
                            @include('partials.dashboard-tabel-medium-high',[
                                'laporans' => $laporanMediumHigh
                            ])
                        </div>
                    </div>

                </div>

                @if(!$isUser)
                    <!-- Grid Utama untuk Chart Atas -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-3">

                        <!-- Chart Garis: Findings per Bulan -->
                        <div class="bg-white rounded-2xl shadow-md p-6 lg:col-span-2 relative">
                            <h2 class="text-base font-semibold text-gray-800 mb-4">Findings Per Month</h2>
                                <div class="absolute top-4 right-4 w-28">
                                    <select id="month-select" class="w-full px-2 py-1 rounded border border-gray-300 bg-white text-xs focus:ring focus:ring-blue-400">
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                            <div class="h-[200px]">
                                <canvas id="monthlyChart"></canvas>
                            </div>
                        </div>

                        <!-- Chart Pie: Status Closed/Non-Closed -->
                        <div class="bg-white rounded-2xl shadow-md p-6 relative">
                            <h2 class="text-base font-semibold text-gray-800 mb-4">Findings by Status</h2>
                            <div class="absolute top-4 right-4 w-28">
                                <select id="month-select-status" class="w-full px-2 py-1 rounded border border-gray-300 bg-white text-xs focus:ring focus:ring-blue-400">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="flex justify-center items-center h-[200px]">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Grid untuk Area dan Kategori -->
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mt-4">

                        <!-- Chart Horizontal: Area -->
                        <div class="bg-white rounded-2xl shadow-md p-6 lg:col-span-3 relative">
                            <h2 class="text-base font-semibold text-gray-800 mb-4">Findings by Area</h2>
                            <div class="absolute top-4 right-4 w-28">
                                <select id="month-select-area" class="w-full px-2 py-1 rounded border border-gray-300 bg-white text-xs focus:ring focus:ring-blue-400">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="h-[300px]">
                                <canvas id="areaChart"></canvas>
                            </div>
                        </div>

                        <!-- Chart Vertikal: Kategori -->
                        <div class="bg-white rounded-2xl shadow-md p-6 lg:col-span-2 relative">
                            <h2 class="text-base font-semibold text-gray-800 mb-4">Findings by Category</h2>
                            <div class="absolute top-4 right-4 w-28">
                                <select id="month-select-category" class="w-full px-2 py-1 rounded border border-gray-300 bg-white text-xs focus:ring focus:ring-blue-400">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="h-[300px]">
                                <canvas id="categoryChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Department -->
                    <div class="grid grid-cols-1 mt-4">
                        <div class="bg-white rounded-2xl shadow-md p-6 relative">
                            <h2 class="text-base font-semibold text-gray-800 mb-4">Findings by Department</h2>
                            <div class="absolute top-6 right-6 w-40">
                                <select id="month-department" class="w-full px-2 py-1 rounded border border-gray-300 bg-white text-xs focus:ring focus:ring-blue-400">
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="h-[300px]">
                                <canvas id="departmentChart"></canvas>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

    const userRole = @json($roleName);
    
    let monthlyChart;

    const currentYearFinding = new Date().getFullYear();
    const currentMonthFinding = (new Date().getMonth() + 1).toString().padStart(2, '0');

    
    function renderChart(labels, data, isMonthly = true) {
        const ctx = document.getElementById('monthlyChart').getContext('2d');

        if (monthlyChart) {
            monthlyChart.destroy();
        }

        monthlyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Number of Findings',
                    data: data,
                    backgroundColor: 'rgba(0, 105, 170, 0.2)', // warna biru muda transparan
                    borderColor: '#0069AA', // warna biru utama
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: isMonthly ? 'Months' : 'Dates'
                        },
                        ticks: {
                            callback: function(value, index, ticks) {
                                const label = this.getLabelForValue(value);
                                if (isMonthly) {
                                    return label;
                                } else {
                                    const date = new Date(label);
                                    return date.getDate().toString().padStart(2, '0');
                                }
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Findings'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }



    function fetchChartData(year, month = '') {
        const url = userRole === 'ehs' ? '/ehs/dashboard/chart-data' : '/dashboard/chart-data';

        $.ajax({
            url: url,
            data: { year, month },
            success: function (res) {
                const isMonthly = !month; 
                renderChart(res.labels, res.data, isMonthly);
            },
            error: function (xhr) {
                console.error('Error fetching chart data:', xhr.responseText);
            }
        });
    }

    $('#month-select').change(function () {
        const month = $(this).val();
        const year = new Date().getFullYear(); // ambil tahun saat ini
        fetchChartData(year, month);
    });

    document.addEventListener('DOMContentLoaded', function () {
        const now = new Date();
        const currentMonth = (now.getMonth() + 1).toString(); // 1–12

        // Set dropdown bulan ke bulan saat ini
        document.getElementById('month-select').value = currentMonth;

        // Panggil fetchChartData dengan bulan dan tahun saat ini
        const year = now.getFullYear();
        fetchChartData(year, currentMonth);
    });

    let areaChart;

    function renderAreaChart(data) {
        const ctx = document.getElementById('areaChart').getContext('2d');

        if (areaChart) areaChart.destroy();

        const areaLabels = Object.keys(data);
        const closedData = areaLabels.map(label => data[label].closed_count);
        const nonClosedData = areaLabels.map(label => data[label].non_closed_count);

        areaChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: areaLabels,
                datasets: [
                    {
                        label: 'Closed',
                        data: closedData,
                        backgroundColor: 'rgba(0, 105, 170, 0.2)',
                        borderColor: 'rgba(0, 105, 170, 0.4)',
                        borderWidth: 1
                    },
                    {
                        label: 'Non-Closed',
                        data: nonClosedData,
                        backgroundColor: '#0069AA',
                        borderColor: '#0069AA',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                // indexAxis: 'y', // ❌ HAPUS untuk bar vertikal
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,   // Putar label agar muat
                            minRotation: 30,   // Minimum rotasi
                            font: {
                                size: 9       // Kecilkan ukuran font jika terlalu banyak
                            }
                        },
                        title: {
                            display: true,
                            text: 'Area'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Findings'
                        },
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            callback: function (value) {
                                return Number.isInteger(value) ? value : '';
                            }
                            
                        }
                    }
                }
            }
        });

    }

    function fetchAreaChartData(year, month) {
        const url = userRole === 'ehs' ? '/ehs/dashboard/area-chart-data' : '/dashboard/area-chart-data';

        $.ajax({
            url: url,
            data: { year, month },
            success: function (res) {
                renderAreaChart(res.areaStatusCounts);
            },
            error: function (xhr) {
                console.error('Error fetching area chart data:', xhr.responseText);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const now = new Date();
        const year = now.getFullYear(); // Tahun sekarang otomatis
        const currentMonth = (now.getMonth() + 1).toString().padStart(2, '0');

        const monthSelect = document.getElementById('month-select-area');
        if (monthSelect) {
            monthSelect.value = currentMonth;

            monthSelect.addEventListener('change', function () {
                const selectedMonth = this.value;
                fetchAreaChartData(year, selectedMonth);
            });
        }

        // Initial fetch dengan bulan dan tahun sekarang
        fetchAreaChartData(year, currentMonth);
    });


    let categoryChart;

    function renderCategoryChart(categoryStatusCounts, categoryAliases) {
        const ctx = document.getElementById('categoryChart').getContext('2d');

        if (categoryChart) categoryChart.destroy();

        const categoryLabels = Object.values(categoryAliases);

        const closedData = categoryLabels.map(label => {
            const originalLabel = Object.keys(categoryAliases).find(key => categoryAliases[key] === label);
            return categoryStatusCounts[originalLabel]?.closed_count || 0;
        });

        const nonClosedData = categoryLabels.map(label => {
            const originalLabel = Object.keys(categoryAliases).find(key => categoryAliases[key] === label);
            return categoryStatusCounts[originalLabel]?.non_closed_count || 0;
        });

        categoryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [
                    {
                        label: 'Non-Closed',
                        data: nonClosedData,
                        backgroundColor: '#0069AA',
                        borderRadius: 6
                    },
                    {
                        label: 'Closed',
                        data: closedData,
                        backgroundColor: 'rgba(0, 105, 170, 0.2)',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y', // ✅ Inilah bagian penting agar jadi horizontal
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Findings'
                        }
                    },
                    y: {
                        stacked: true,
                        ticks: {
                            autoSkip: false,
                            font: {
                                size: 10
                            }
                        },
                        title: {
                            display: true,
                            text: 'Kategori'
                        }
                    }
                }
            }
        });


    }

    function fetchCategoryChartData(year, month) {
        const url = userRole === 'ehs' ? '/ehs/dashboard/category-chart-data' : '/dashboard/category-chart-data';

        $.ajax({
            url: url,
            data: { year, month },
            success: function (res) {
                renderCategoryChart(res.categoryStatusCounts, res.categoryAliases);
            },
            error: function (xhr) {
                console.error('Error fetching category chart data:', xhr.responseText);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const now = new Date();
        const year = now.getFullYear();
        const currentMonth = (now.getMonth() + 1).toString().padStart(2, '0');

        const monthSelect = document.getElementById('month-select-category');
        if (monthSelect) {
            monthSelect.value = currentMonth;

            monthSelect.addEventListener('change', function () {
                const selectedMonth = this.value;
                fetchCategoryChartData(year, selectedMonth);
            });
        }

        // Initial fetch dengan bulan dan tahun sekarang
        fetchCategoryChartData(year, currentMonth);
    });


    
        let statusChart;

        function renderStatusChart(statusCounts) {
            const ctx = document.getElementById('statusChart').getContext('2d');

            if (statusChart) statusChart.destroy();

            const dataValues = [
                Number(statusCounts.open) || 0,
                Number(statusCounts.closed) || 0,
                Number(statusCounts.in_progress) || 0,
                Number(statusCounts.overdue) || 0
            ];

            const total = dataValues.reduce((a, b) => a + b, 0);

            statusChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Open', 'Closed', 'In Progress', 'Overdue'],
                    datasets: [{
                        data: dataValues,
                        backgroundColor: [
                            '#FFC107',   // Open - kuning lebih tegas
                            '#4CAF50',   // Closed - hijau tegas
                            '#2196F3',   // In Progress - biru tegas
                            '#0069AA'    // Overdue - biru utama solid
                        ],
                        borderWidth: 0 // tidak ada border
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        datalabels: {
                            formatter: (value, context) => {
                                return total ? (value / total * 100).toFixed(1) + '%' : '0%';
                            },
                            color: '#000',
                            font: { weight: 'bold' }
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }


        function fetchStatusChartData(year, month) {
            const url = userRole === 'ehs' ? '/ehs/dashboard/status-chart-data' : '/dashboard/status-chart-data';

            $.ajax({
                url: url,
                data: { year, month },
                success: function (res) {
                    renderStatusChart(res.statusCounts);
                },
                error: function (xhr) {
                    console.error('Error fetching status chart data:', xhr.responseText);
                }
            });
        }

        $('#month-select-status').change(function () {
            const now = new Date();
            const year = now.getFullYear(); // Tahun sekarang otomatis
            const month = $(this).val();
            fetchStatusChartData(year, month);
        });


        document.addEventListener('DOMContentLoaded', function () {
            const now = new Date();
            const year = now.getFullYear();
            const currentMonth = (now.getMonth() + 1).toString().padStart(2, '0');

            const monthSelect = document.getElementById('month-select-status');
            if (monthSelect) {
                monthSelect.value = currentMonth;
            }

            fetchStatusChartData(year, currentMonth);
        });

        let departmentChart;

        function renderDepartmentChart(data) {
            const ctx = document.getElementById('departmentChart').getContext('2d');

            if (departmentChart) departmentChart.destroy();

            const labels = data.map(d => d.label);
            const values = data.map(d => d.value);

            departmentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Jumlah Temuan per Departemen',
                        data: values,
                        backgroundColor: '#0069AA', // warna biru utama transparan
                        // borderColor: '#0069AA', // warna biru utama solid
                        // borderWidth: 2,
                        borderRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                font: {
                                    size: 10 // ukuran font label agar tidak bertumpuk
                                }
                            }
                        }
                    }
                }
            });
        }


        function fetchDepartmentChartData(month) {
            const year = new Date().getFullYear(); // selalu gunakan tahun sekarang
            const url = userRole === 'ehs' ? '/ehs/dashboard/department-chart-data' : '/dashboard/department-chart-data';

            $.ajax({
                url: url,
                data: { year, month },
                success: function (res) {
                    renderDepartmentChart(res.data);
                },
                error: function (xhr) {
                    console.error('Error fetching department chart data:', xhr.responseText);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const now = new Date();
            const currentMonth = String(now.getMonth() + 1).padStart(2, '0');

            const monthSelect = document.getElementById('month-department');
            if (monthSelect) {
                monthSelect.value = currentMonth;

                monthSelect.addEventListener('change', function () {
                    fetchDepartmentChartData(this.value);
                });

                // Load awal
                fetchDepartmentChartData(currentMonth);
            }
        });


    </script>
    
    
</x-app-layout>
