<x-app-layout>
    
    <div class="p-3 sm:p-5">
        <div class="mx-auto max-w-screen-2xl">
            <div class="container mx-auto">

                 <!-- Top Section - Cards and Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Summary Cards -->
                <div class="bg-blue-600 text-white p-4 rounded-lg shadow-lg">
                    <h3 class="font-medium text-white/80">Total Findings</h3>
                    <p class="text-3xl font-bold">1,248</p>
                    <div class="text-sm mt-2">↑ 12% from last month</div>
                </div>
                
                <div class="bg-green-600 text-white p-4 rounded-lg shadow-lg">
                    <h3 class="font-medium text-white/80">Resolved</h3>
                    <p class="text-3xl font-bold">892</p>
                    <div class="text-sm mt-2">↑ 8% from last month</div>
                </div>
                
                <div class="bg-amber-500 text-white p-4 rounded-lg shadow-lg">
                    <h3 class="font-medium text-white/80">Overdue</h3>
                    <p class="text-3xl font-bold">156</p>
                    <div class="text-sm mt-2">↓ 3% from last month</div>
                </div>
                
                <div class="bg-red-600 text-white p-4 rounded-lg shadow-lg">
                    <h3 class="font-medium text-white/80">High Risk</h3>
                    <p class="text-3xl font-bold">42</p>
                    <div class="text-sm mt-2">↑ 5% from last month</div>
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
                            <h2 class="text-xl font-semibold mb-2">New Finding</h2>
                            <div class="overflow-auto h-[435px]"> <!-- Height diseragamkan -->
                                @include('partials.dashboard-tabel-new', [
                                    'laporans' => $laporanNew
                                ])
                            </div>
                        </div>

                        <!-- Report Awaiting Approval -->
                        <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                            <h2 class="text-xl font-semibold mb-2">Report Awaiting Approval</h2>
                            <div class="overflow-auto h-[435px]"> <!-- Height diseragamkan -->
                                @include('partials.dashboard-tabel', [
                                    'laporans' => $laporanNeedApproval
                                ])
                            </div>
                        </div>
                    @endif

                    {{-- Manager --}}
                    @if($isManajer)
                        <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                            <h2 class="text-xl font-semibold mb-2">Report Awaiting Approval</h2>
                            <div class="overflow-auto h-auto"> <!-- Height diseragamkan -->
                                @include('partials.dashboard-tabel-approval-taskbudget', [
                                    'laporans' => $laporanNeedApprovalBudget
                                ])
                            </div>
                        </div>
                    @endif

                    @if($isUser)
                        <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                            <h2 class="text-xl font-semibold mb-2">Activity Progress</h2>
                            <div class="overflow-auto h-auto"> <!-- Height diseragamkan -->
                                @include('partials.dashboard-tabel', [
                                    'laporans' => $laporanUser
                                ])
                            </div>
                        </div>
                    @endif

                    @if($isPic)
                        <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                            <h2 class="text-xl font-semibold mb-2">Unread Reports</h2>
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
                        <h2 class="text-xl font-semibold mb-2">Overdue Reports</h2>
                        <div class="overflow-auto max-h-[600px]"> <!-- ditingkatkan -->
                            @include('partials.dashboard-tabel', [
                                'laporans' => $laporanOverdue
                            ])
                        </div>
                    </div>
                    
                    <!-- Medium & High Risk Reports Table -->
                    <div class="p-4 bg-white rounded-lg shadow-md flex flex-col">
                        <h2 class="text-xl font-semibold mb-2">Medium & High Risk Reports</h2>
                        <div class="overflow-auto max-h-[600px]"> <!-- ditingkatkan -->
                            @include('partials.dashboard-tabel-medium-high',[
                                'laporans' => $laporanMediumHigh
                            ])
                        </div>
                    </div>

                </div>

                <!-- Main Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mt-4">
                    
                    <!-- Grafik Garis: LCT Per Bulan (2/3 width) -->
                    <div class="p-6 bg-white rounded-lg shadow-md lg:col-span-2 flex flex-col">
                        <h2 class="text-2xl font-semibold mb-1">Findings</h2>
                        <div class="flex gap-4 mb-4">
                            <div class="flex space-x-4">
                                <!-- Year Select -->
                                <div class="relative w-40">
                                    <select id="year-select" class="w-full px-4 py-2 pr-8 rounded border border-gray-300 bg-white text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="">Select Year</option>
                                        @foreach ($findings as $year)
                                            <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            
                                <!-- Month Select -->
                                <div class="relative w-48">
                                    <select id="month-select" class="w-full px-4 py-2 pr-8 rounded border border-gray-300 bg-white text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="">All Months</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>                           
                        </div>
                        
                        <canvas id="monthlyChart" class="flex-grow"></canvas>
                    </div>

                    <!-- Grafik Pie: Open vs Closed (1/3 width) -->
                    <div class="p-6 bg-white rounded-lg shadow-md lg:col-span-1 flex flex-col justify-between">
                        <h2 class="text-2xl font-semibold mb-1">Findings by Status</h2>
                        <div class="flex gap-4 mb-4">
                            <div class="flex space-x-4">
                                <!-- Year Select -->
                                <div class="relative w-40">
                                    <select id="year-select-status" class="w-full px-4 py-2 pr-8 rounded border border-gray-300 bg-white text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="">Select Year</option>
                                        @foreach ($findings as $year)
                                            <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            
                                <!-- Month Select -->
                                <div class="relative w-48">
                                    <select id="month-select-status" class="w-full px-4 py-2 pr-8 rounded border border-gray-300 bg-white text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="">All Months</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>                           
                        </div>
                        <canvas id="statusChart" class="self-center" style="max-width: 300px; max-height: 300px;"></canvas>
                    </div>

                </div>
                <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mt-6">
                    <!-- Grafik Batang Horizontal: Berdasarkan Area -->
                    <div class="p-6 bg-white rounded-lg shadow-md lg:col-span-3 flex flex-col">
                        <h2 class="text-2xl font-semibold mb-4">Findings by Area</h2>
                        <div class="flex gap-4 mb-4">
                            <div class="flex space-x-4">
                                <!-- Year Select -->
                                <div class="relative w-40">
                                    <select id="year-select-area" class="w-full px-4 py-2 pr-8 rounded border border-gray-300 bg-white text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="">Select Year</option>
                                        @foreach ($findings as $year)
                                            <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            
                                <!-- Month Select -->
                                <div class="relative w-48">
                                    <select id="month-select-area" class="w-full px-4 py-2 pr-8 rounded border border-gray-300 bg-white text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="">All Months</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>                           
                        </div>
                        <div style="height: 390px;">
                            <canvas id="areaChart"></canvas>
                        </div>
                    </div>
                
                    <!-- Grafik Batang Vertikal: Berdasarkan Kategori -->
                    <div class="p-6 bg-white rounded-lg shadow-md lg:col-span-2 flex flex-col">
                        <h2 class="text-2xl font-semibold mb-4">Findings by Category</h2>
                        <div class="flex gap-4 mb-4">
                            <div class="flex space-x-4">
                                <!-- Year Select -->
                                <div class="relative w-40">
                                    <select id="year-select-category" class="w-full px-4 py-2 pr-8 rounded border border-gray-300 bg-white text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="">Select Year</option>
                                        @foreach ($findings as $year)
                                            <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            
                                <!-- Month Select -->
                                <div class="relative w-48">
                                    <select id="month-select-category" class="w-full px-4 py-2 pr-8 rounded border border-gray-300 bg-white text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="">All Months</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>                           
                        </div>
                        <canvas id="categoryChart" class="max-h-80"></canvas>
                    </div>
                </div>
                <div class="grid grid-cols-1 mt-6">
                <div class="p-6 bg-white rounded-lg shadow-md lg:col-span-3 flex flex-col">
                    <h2 class="text-2xl font-semibold mb-4">Findings by Department</h2>
                    <div class="flex gap-4 mb-4">
                        <div class="flex space-x-4">
                            <!-- Year Select -->
                            <div class="relative w-40">
                                <select id="year-department" class="w-full px-4 py-2 pr-8 rounded border border-gray-300 bg-white text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    <option value="">Select Year</option>
                                    @foreach ($findings as $year)
                                        <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        
                            <!-- Month Select -->
                            <div class="relative w-48">
                                <select id="month-department" class="w-full px-4 py-2 pr-8 rounded border border-gray-300 bg-white text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    <option value="">All Months</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>                           
                    </div>
                    <div style="height: 390px;">
                        <canvas id="departmentChart"></canvas>
                    </div>
                </div>
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
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
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
                            text: isMonthly ? 'Months' : 'Dates' // Gunakan flag, bukan length
                        },
                        ticks: {
                            callback: function(value, index, ticks) {
                                const label = this.getLabelForValue(value);
                                if (isMonthly) {
                                    return label; // langsung tampilkan nama bulan
                                } else {
                                    const date = new Date(label);
                                    return date.getDate().toString().padStart(2, '0'); // hanya tanggal
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

    // Event dropdown
    $('#year-select').change(function () {
        const year = $(this).val();
        $('#month-select').prop('disabled', !year).val('');
        if (year) {
            fetchChartData(year);
        }
    });

    $('#month-select').change(function () {
        const year = $('#year-select').val();
        const month = $(this).val();
        if (year) {
            fetchChartData(year, month); // bulan kosong => all month
        }
    });

    // Initial load (optional)
    @if(count($findings))
        fetchChartData({{ $findings->max() }});
    @endif
    

        let areaChart;
        // Fungsi untuk menggambar chart area
        function renderAreaChart(data) {
            const ctx = document.getElementById('areaChart').getContext('2d');

            // Hancurkan chart lama jika ada
            if (areaChart) {
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
                            backgroundColor: 'rgba(75, 192, 75, 0.6)',
                            borderColor: 'rgba(75, 192, 75, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Non-Closed',
                            data: nonClosedData,
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false, 
                    responsive: true,
                    scales: {
                        x: {
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


        // Fungsi untuk mengambil data berdasarkan tahun dan bulan
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

        // Event listener untuk dropdown tahun
        $('#year-select-area').change(function () {
            const year = $(this).val();
            $('#month-select-area').prop('disabled', !year).val('');
            if (year) {
                fetchAreaChartData(year);
            }
        });

        // Event listener untuk dropdown bulan
        $('#month-select-area').change(function () {
            const year = $('#year-select-area').val();
            const month = $(this).val();
            if (year) {
                fetchAreaChartData(year, month); // Filter berdasarkan tahun dan bulan
            }
        });

            // Initial load (optional)
        @if(count($findings))
            fetchAreaChartData({{ $findings->max() }});
        @endif

    
        let categoryChart; // Simpan chart agar bisa di-destroy


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
                            label: 'Closed',
                            data: closedData,
                            backgroundColor: 'rgba(75, 192, 75, 0.6)',
                            borderColor: 'rgba(75, 192, 75, 1)',
                            borderWidth: 1,
                            barThickness: 40,
                        },
                        {
                            label: 'Non-Closed',
                            data: nonClosedData,
                            backgroundColor: 'rgba(255, 99, 132, 0.6)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            barThickness: 40,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    aspectRatio: 0.1,
                    scales: {
                        x: {
                            stacked: true,
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            beginAtZero: true,
                            stacked: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0
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
                    // Gunakan renderCategoryChart untuk memperbarui grafik kategori
                    renderCategoryChart(res.categoryStatusCounts, res.categoryAliases);
                },
                error: function (xhr) {
                    console.error('Error fetching category chart data:', xhr.responseText);
                }
            });
        }


        // Event listener untuk dropdown tahun
        $('#year-select-category').change(function () {
            const year = $(this).val();
            $('#month-select-category').prop('disabled', !year).val('');
            if (year) {
                fetchCategoryChartData(year);
            }
        });

        // Event listener untuk dropdown bulan
        $('#month-select-category').change(function () {
            const year = $('#year-select-category').val();
            const month = $(this).val();
            if (year) {
                fetchCategoryChartData(year, month); // Filter berdasarkan tahun dan bulan
            }
        });
        
        // Initial load (optional)
        @if(count($findings))
            fetchCategoryChartData({{ $findings->max() }});
        @endif

    
        let statusChart;

        function renderStatusChart(statusCounts) {
            const ctx = document.getElementById('statusChart').getContext('2d');

            if (statusChart) statusChart.destroy();

            const dataValues = [
                Number(statusCounts.open) || 0,
                Number(statusCounts.closed) || 0,
                Number(statusCounts.in_progress) || 0
            ];

            const total = dataValues.reduce((a, b) => a + b, 0);

            statusChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Open', 'Closed', 'In Progress'],
                    datasets: [{
                        data: dataValues,
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(75, 192, 75, 0.6)',
                            'rgba(255, 206, 86, 0.6)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 75, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
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


        $('#year-select-status').change(function () {
            const year = $(this).val();
            $('#month-select-status').prop('disabled', !year).val('');
            if (year) fetchStatusChartData(year);
        });

        $('#month-select-status').change(function () {
            const year = $('#year-select-status').val();
            const month = $(this).val();
            if (year) fetchStatusChartData(year, month);
        });

        // Initial load (optional)
        @if(count($findings))
        fetchStatusChartData({{ $findings->max() }});
        @endif

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
                        backgroundColor: 'rgba(153, 102, 255, 0.6)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1,
                        barThickness: 40
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
                                autoSkip: false
                            }
                        }
                    }
                }
            });
        }

        function fetchDepartmentChartData(year, month) {
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

        $('#year-department').change(function () {
            const year = $(this).val();
            $('#month-department').prop('disabled', !year).val('');
            if (year) fetchDepartmentChartData(year);
        });

        $('#month-department').change(function () {
            const year = $('#year-department').val();
            const month = $(this).val();
            if (year) fetchDepartmentChartData(year, month);
        });


        @if(count($findings))
            fetchDepartmentChartData({{ $findings->max() }});
        @endif

    </script>
    
    
</x-app-layout>
