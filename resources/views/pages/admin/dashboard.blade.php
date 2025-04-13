<x-app-layout>
    
    <div class="p-3 sm:p-5">
        <div class="mx-auto max-w-screen-2xl">
            <div class="container mx-auto">

                <!-- Main Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    
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

            
                <!-- Tabel Reports Section (Medium & High Risk Reports and Overdue Reports) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-4">
                    
                    <!-- Medium & High Risk Reports Table -->
                    <div class="p-6 bg-white rounded-lg shadow-md flex flex-col">
                        @include('partials.dashboard-tabel-medium-high',[
                            'title' => 'Medium & High Risk Reports',
                            'laporans' => $laporanMediumHigh
                        ])
                    </div>
            
                    <!-- Overdue Reports Table -->
                    <div class="p-6 bg-white rounded-lg shadow-md flex flex-col">
                        @include('partials.dashboard-tabel', [
                            'title' => 'Overdue Reports',
                            'laporans' => $laporanOverdue
                        ])
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
