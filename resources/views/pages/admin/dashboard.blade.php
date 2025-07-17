<x-app-layout>
    
    <div class="p-3 sm:p-5">
        <div class="mx-auto max-w-screen-2xl">
            <div class="container mx-auto">

                <!-- Top Section - Cards and Key Metrics -->
                @php
                    $startOfMonth = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
                    $endOfMonth = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');

                    if (Auth::guard('ehs')->check()) {
                        $user = Auth::guard('ehs')->user();
                        $roleName = 'ehs';
                    } else {
                        $user = Auth::guard('web')->user();
                        // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
                        $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
                    }

                
                    if ($roleName === 'ehs') {
                        $routeName = 'ehs.reporting.index';
                    } elseif ($roleName === 'pic') {
                        $routeName = 'admin.manajemen-lct.index';
                    } else {
                        $routeName = 'admin.reporting.index';
                    }
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    
                    <!-- Total Findings -->
                    <a href="{{ route($routeName, ['tanggalAwal' => $startOfMonth, 'tanggalAkhir' => $endOfMonth]) }}">
                        <div class="bg-blue-600 text-white p-4 rounded-lg shadow-lg hover:opacity-90 transition">
                            <h3 class="font-medium text-white/80">Total Findings</h3>
                            <p class="text-3xl font-bold">{{ $totalFindings }}</p>
                            <div class="text-sm mt-2 text-white/70">
                                {{ $totalFindingsChange >= 0 ? '↑' : '↓' }} {{ abs($totalFindingsChange) }}% from last month
                            </div>
                        </div>
                    </a>

                    <!-- Closed -->
                    <a href="{{ route($routeName, ['tanggalAwal' => $startOfMonth, 'tanggalAkhir' => $endOfMonth, 'statusLct' => 'closed']) }}">
                        <div class="bg-green-600 text-white p-4 rounded-lg shadow-lg hover:opacity-90 transition">
                            <h3 class="font-medium text-white/80">Closed</h3>
                            <p class="text-3xl font-bold">{{ $resolved }}</p>
                            <div class="text-sm mt-2 text-white/70">
                                {{ $resolvedChange >= 0 ? '↑' : '↓' }} {{ abs($resolvedChange) }}% from last month
                            </div>
                        </div>
                    </a>

                    <!-- Overdue -->
                    <a href="{{ route($routeName, ['overdue' => 'true']) }}">
                        <div class="bg-amber-500 text-white p-4 rounded-lg shadow-lg hover:opacity-90 transition">
                            <h3 class="font-medium text-white/80">Overdue</h3>
                            <p class="text-3xl font-bold">{{ $overdue }}</p>
                            <div class="text-sm mt-2 text-white/70">
                                {{ $overdueChange >= 0 ? '↑' : '↓' }} {{ abs($overdueChange) }}% from last month
                            </div>
                        </div>
                    </a>

                    <!-- High Risk -->
                    <a href="{{ route($routeName, ['tanggalAwal' => $startOfMonth, 'tanggalAkhir' => $endOfMonth, 'riskLevel' => 'high']) }}">
                        <div class="bg-red-600 text-white p-4 rounded-lg shadow-lg hover:opacity-90 transition">
                            <h3 class="font-medium text-white/80">High Risk</h3>
                            <p class="text-3xl font-bold">{{ $highRisk }}</p>
                            <div class="text-sm mt-2 text-white/70">
                                {{ $highRiskChange >= 0 ? '↑' : '↓' }} {{ abs($highRiskChange) }}% from last month
                            </div>
                        </div>
                    </a>

                </div>

                

            @php
                // Ambil role aktif dari session, fallback ke relasi jika tidak ada
                if (Auth::guard('ehs')->check()) {
                    $userRole = optional(Auth::guard('ehs')->user()->roles->first())->name;
                } else {
                    $userRole = session('active_role') ?? optional(auth()->user()->roleLct->first())->name;
                }
            
                $userRole = strtolower($userRole);
            
                $isEhs = $userRole === 'ehs';
                $isUser = $userRole === 'user' || $userRole === 'employee';
                $isPic = $userRole === 'pic';
                $isManajer = $userRole === 'manajer';
            
                // Hitung jumlah tabel
                $tableCount = 0;
                if ($isEhs) $tableCount += 2;
                if ($isManajer) $tableCount += 1;
                if ($isUser) $tableCount += 1;
                if ($isPic) $tableCount += 2;
            
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

                        @php
                            $taskOnlyTotal = $todos['taskOnly']['total'] ?? 0;

                            $totalTasks = $correctiveLowCount
                                + $revisionLowCount
                                + $temporaryInProgressCount
                                + $revisionTemporaryCount
                                + $revisionBudgetCount
                                + $permanentWorkCount
                                + $taskOnlyTotal;
                        @endphp

                    <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="mb-3">
                            <h2 class="text-sm font-semibold text-gray-800">Tasks Overview</h2>
                            @if ($totalTasks > 0)
                                <p class="text-xs text-gray-500 mt-1">
                                    You have <span class="font-semibold text-blue-600">{{ $totalTasks }}</span> tasks to complete
                                </p>
                            @endif
                        </div>

                        <ul class="space-y-1">
                            @php
                                $taskLinks = [
                                    ['count' => $correctiveLowCount, 'label' => 'Corrective Action (Low Risk)', 'params' => ['riskLevel' => 'low', 'statusLct' => 'in_progress,progress_work']],
                                    ['count' => $revisionLowCount, 'label' => 'Revision (Low Risk)', 'params' => ['riskLevel' => 'low', 'statusLct' => 'revision']],
                                    ['count' => $temporaryInProgressCount, 'label' => 'Temporary Action (Medium/High Risk)', 'params' => ['riskLevel' => 'medium,high', 'statusLct' => 'in_progress,progress_work']],
                                    ['count' => $revisionTemporaryCount, 'label' => 'Revision - Temporary', 'params' => ['statusLct' => 'temporary_revision']],
                                    ['count' => $revisionBudgetCount, 'label' => 'Revision - Budget', 'params' => ['statusLct' => 'taskbudget_revision']],
                                    ['count' => $permanentWorkCount, 'label' => 'Permanent Action (Working)', 'params' => ['statusLct' => 'work_permanent']],
                                    // Tambahkan task only sebagai salah satu item biasa
                                    ['count' => $taskOnlyTotal, 'label' => 'Permanent Action (Task Only)', 'params' => ['is_task_only' => 'true']],
                                ];
                            @endphp

                        
                            {{-- Bagian untuk laporan PIC utama --}}
                            @foreach ($taskLinks as $task)
                                @if ($task['count'] > 0)
                                    <li>
                                        <a href="{{ route('admin.manajemen-lct.index', $task['params']) }}"
                                            class="flex justify-between items-center px-3 mb-2 text-xs rounded-md transition duration-150">
                                            <div class="flex items-center">
                                                <div class="w-5 text-gray-600 text-xs flex items-center justify-center font-semibold">{{ $task['count'] }}</div>
                                                <span class="text-gray-600">{{ $task['label'] }}</span>
                                            </div>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        
                    </div>

                        

                        <!-- Overdue Reports Table -->
                    <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                        <h2 class="text-base font-semibold mb-2">Overdue Reports</h2>
                        <div class="overflow-auto max-h-[600px]"> <!-- ditingkatkan -->
                            @include('partials.dashboard-tabel', [
                                'laporans' => $laporanOverdue
                            ])
                        </div>
                    </div>
                    @endif

                </div>


                <!-- Tabel Reports Section (Overdue & Medium-High Risk) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-4 items-start">

                    <!-- Medium & High Hazard Reports Table -->
                    <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                        <h2 class="text-base font-semibold mb-2">Medium & High Hazard Reports</h2>
                        <div class="overflow-auto max-h-[600px]"> <!-- ditingkatkan -->
                            @include('partials.dashboard-tabel-medium-high',[
                                'laporans' => $laporanMediumHigh
                            ])
                        </div>
                    </div>

                    <!-- Overdue Reports Table -->
                    <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                        <h2 class="text-base font-semibold mb-2">Low Hazard Reports</h2>
                        <div class="overflow-auto max-h-[600px]"> <!-- ditingkatkan -->
                            @include('partials.dashboard-tabel-medium-high', [
                                'laporans' => $laporanLow
                            ])
                        </div>
                    </div>

                </div>

                @if(!$isUser)
                    <!-- Grid Utama untuk Chart Atas -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-3">

                        <!-- Chart Garis: Findings per Bulan -->
                        <div class="bg-white rounded-2xl shadow-md p-4 sm:p-6 lg:col-span-2 w-full">
                            <!-- Header & Filter -->
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-base font-semibold text-gray-800">Findings Per Month</h2>
                        
                                <div class="w-32">
                                    <select id="month-select"
                                        class="w-full px-2 py-1 rounded border border-gray-300 bg-white text-xs focus:ring focus:ring-blue-400">
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        
                            <!-- Chart Area (No overflow) -->
                            <div class="w-full h-[250px] sm:h-[300px] md:h-[350px]">
                                <canvas id="monthlyChart" class="!w-full !h-full"></canvas>
                            </div>
                        </div>
                        
                        
                        <!-- Chart Pie: Status Closed/Non-Closed -->
                        <div class="bg-white rounded-2xl shadow-md p-6 relative w-full">
                            <div class="flex justify-between items-start mb-4">
                                <h2 class="text-base font-semibold text-gray-800">Findings by Status</h2>
                                <div class="w-32">
                                    <select id="month-select-status"
                                        class="w-full px-2 py-1 rounded-md border border-gray-300 bg-white text-xs focus:ring-2 focus:ring-blue-400">
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">
                                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                                <!-- Chart Area -->
                                <div class="flex-1 flex justify-center items-center h-[250px]">
                                    <canvas id="statusChart" class="!w-full !h-full max-w-[300px] max-h-[300px]"></canvas>
                                </div>

                                <!-- Legend Manual (Optional Enhancement) -->
                                <div class="text-sm space-y-2 hidden lg:block">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-[#FFC107]"></span>
                                        <span>Open</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-[#4CAF50]"></span>
                                        <span>Closed</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-[#2196F3]"></span>
                                        <span>In Progress</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-[#0069AA]"></span>
                                        <span>Overdue</span>
                                    </div>
                                </div>
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
                    @if($isEhs)
                    <div class="grid grid-cols-1 mt-4">
                        <div class="bg-white rounded-2xl shadow-md p-6">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
                                <h2 class="text-base font-semibold text-gray-800">
                                    Findings by Department
                                </h2>
                                <div class="w-full sm:w-40">
                                    <select id="month-department" class="w-full px-2 py-1 rounded border border-gray-300 bg-white text-xs focus:ring focus:ring-blue-400">
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="h-[300px]">
                                <canvas id="departmentChart"></canvas>
                            </div>
                        </div>
                    </div>
                    @endif

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
        const canvas = document.getElementById('monthlyChart');
        if (!canvas) return; // Stop diam-diam jika elemen tidak ada

        const ctx = canvas.getContext('2d');

        if (typeof monthlyChart !== 'undefined' && monthlyChart) {
            monthlyChart.destroy();
        }

        monthlyChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Number of Findings',
                    data: data,
                    backgroundColor: 'rgba(0, 105, 170, 0.2)',
                    borderColor: '#0069AA',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 10 // Ukuran font legend
                            }
                        }
                    },
                    tooltip: {
                        titleFont: {
                            size: 11 // Ukuran judul tooltip
                        },
                        bodyFont: {
                            size: 10 // Ukuran isi tooltip
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: isMonthly ? 'Months' : 'Dates',
                            font: {
                                size: 9 // ukuran judul sumbu X
                            }
                        },
                        ticks: {
                            font: {
                                size: 9 // ukuran label-label di sumbu X
                            },
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
                            text: 'Number of Findings',
                            font: {
                                size: 9 // ukuran judul sumbu Y
                            }
                        },
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 9 // ukuran label angka di sumbu Y
                            }
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
        const canvas = document.getElementById('areaChart');
        if (!canvas) return; // Diam saja jika tidak ada elemen

        const ctx = canvas.getContext('2d');

        if (typeof areaChart !== 'undefined' && areaChart) {
            areaChart.destroy();
        }

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
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 30,
                            font: {
                                size: 9
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
        const canvas = document.getElementById('categoryChart');
        if (!canvas) return; // Stop diam-diam jika elemen tidak ada

        const ctx = canvas.getContext('2d');

        if (typeof categoryChart !== 'undefined' && categoryChart){ 
            categoryChart.destroy();
        }

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
        const canvas = document.getElementById('statusChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');

        // Destroy chart sebelumnya
        if (statusChart) {
            statusChart.destroy();
        }

        // Label status dan warnanya
        const labels = ['Open', 'Closed', 'In Progress', 'Overdue'];
        const colors = ['#FFC107', '#4CAF50', '#2196F3', '#0069AA'];

        // Ambil nilai-nilai dari statusCounts
        const dataValues = [
            Number(statusCounts.open) || 0,
            Number(statusCounts.closed) || 0,
            Number(statusCounts.in_progress) || 0,
            Number(statusCounts.overdue) || 0
        ];

        const total = dataValues.reduce((a, b) => a + b, 0);
        const percentages = dataValues.map(v => total ? ((v / total) * 100).toFixed(1) : '0.0');

        // Inject ke #statusDetails
        const detailsContainer = document.getElementById('statusDetails');
        if (detailsContainer) {
            detailsContainer.innerHTML = ''; // Kosongkan

            labels.forEach((label, i) => {
                detailsContainer.innerHTML += `
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background-color: ${colors[i]}"></span>
                            <span>${label}</span>
                        </div>
                        <div class="text-right font-medium">
                            ${dataValues[i]} / ${total} × 100 = ${percentages[i]}%
                        </div>
                    </div>
                `;
            });

            // Tambahkan total
            detailsContainer.innerHTML += `
                <div class="flex justify-between pt-1 mt-2 border-t text-xs text-gray-500">
                    <span>Total</span>
                    <span>${total}</span>
                </div>
            `;
        }

        // Render Chart.js
        statusChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    borderWidth: 4,
                    borderColor: '#fff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            boxWidth: 12,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    datalabels: {
                        formatter: (value) => {
                            return total ? (value / total * 100).toFixed(1) + '%' : '0%';
                        },
                        color: '#000',
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                return `${label}: ${value} (${((value / total) * 100).toFixed(1)}%)`;
                            }
                        }
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
    const year = now.getFullYear();
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
            const canvas = document.getElementById('departmentChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            if (typeof departmentChart !== 'undefined' && departmentChart){ 
                departmentChart.destroy();
            }

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
