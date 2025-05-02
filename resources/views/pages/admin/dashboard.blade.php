<x-app-layout>
    
    <div class="p-3 sm:p-5">
        <div class="mx-auto max-w-screen-2xl">
            <div class="container mx-auto">

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
                        <h2 class="text-2xl font-semibold mb-1">Monthly Findings</h2>
                        <canvas id="monthlyChart" class="flex-grow"></canvas>
                    </div>

                    <!-- Grafik Pie: Open vs Closed (1/3 width) -->
                    <div class="p-6 bg-white rounded-lg shadow-md lg:col-span-1 flex flex-col justify-between">
                        <h2 class="text-2xl font-semibold mb-1">Findings by Status</h2>
                        <canvas id="statusChart" class="self-center" style="max-width: 300px; max-height: 300px;"></canvas>
                    </div>

                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-4">
                    <!-- Grafik Batang Horizontal: Berdasarkan Area -->
                    <div class="p-6 bg-white rounded-lg shadow-md flex flex-col">
                        <h2 class="text-2xl font-semibold mb-4">Most Findings Areas</h2>
                        <canvas id="areaChart" class="flex-grow"></canvas>
                    </div>

                    <!-- Grafik Batang Vertikal: Berdasarkan Kategori -->
                    <div class="p-6 bg-white rounded-lg shadow-md flex flex-col">
                        <h2 class="text-2xl font-semibold mb-4">Categories of Findings</h2>
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            
            </div>
        </div>
    </div>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const monthlyLabels = @json(array_keys($monthlyReports->toArray()));
        const monthlyData = @json(array_values($monthlyReports->toArray()));
    
        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Number of Findings per Month',
                    data: monthlyData,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : null;
                            }
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    
        const areaLabels = @json(array_keys($areaCounts->toArray()));
        const areaData = @json(array_values($areaCounts->toArray()));
    
        new Chart(document.getElementById('areaChart'), {
            type: 'bar',
            data: {
                labels: areaLabels,
                datasets: [{
                    label: 'Number of Findings per Area',
                    data: areaData,
                    backgroundColor: areaData.map((_, i) => {
                        const colors = [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)'
                        ];
                        return colors[i % colors.length];
                    }),
                    borderColor: areaData.map((_, i) => {
                        const borderColors = [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ];
                        return borderColors[i % borderColors.length];
                    }),
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                scales: {
                    x: {
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            callback: function(value) {
                                return Number.isInteger(value) ? value : '';
                            }
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    
        const categoryLabels = @json(array_values($categoryAliases));
        const categoryData = @json(array_map(function($key) use ($categoryCounts) {
            return $categoryCounts[$key] ?? 0;
        }, array_keys($categoryAliases)));

    
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Number of Findings per Category',
                    data: categoryData,
                    backgroundColor: categoryData.map((_, i) => {
                        const colors = [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)'
                        ];
                        return colors[i % colors.length];
                    }),
                    borderColor: categoryData.map((_, i) => {
                        const borderColors = [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ];
                        return borderColors[i % borderColors.length];
                    }),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                }
            }
        });
    
        const statusCounts = [
            {{ $statusCounts['open'] }},
            {{ $statusCounts['close'] }},
            {{ $statusCounts['in_progress'] }}
        ];
    
        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: ['Open', 'Closed', 'In Progress'],
                datasets: [{
                    data: statusCounts,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',   // Open
                        'rgba(75, 192, 75, 0.6)',    // Closed
                        'rgba(255, 206, 86, 0.6)'    // In Progress
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
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
    
    
</x-app-layout>
