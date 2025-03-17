<x-app-layout>
    
    <div class="p-3 sm:p-5">
        <div class="mx-auto max-w-screen-2xl">
            <div class="container mx-auto px-6 py-4">

                <!-- Main Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                    
                    <!-- Grafik Garis: LCT Per Bulan (2/3 width) -->
                    <div class="p-6 bg-white rounded-lg shadow-md lg:col-span-2 flex flex-col">
                        <h2 class="text-2xl font-semibold mb-1">Incident By Month</h2>
                        <canvas id="monthlyChart" class="flex-grow"></canvas>
                    </div>

                    <!-- Grafik Pie: Open vs Closed (1/3 width) -->
                    <div class="p-6 bg-white rounded-lg shadow-md lg:col-span-1 flex flex-col justify-between">
                        <h2 class="text-2xl font-semibold mb-1">Status of Finding</h2>
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
                        <h2 class="text-2xl font-semibold mb-4">Types of Findings (This Month)</h2>
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            
            </div>
            
            
        </div>
    </div>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
       // Simulasi data dengan banyak bulan (lebih dari 12)
const simulatedMonthlyLabels = [];
const simulatedMonthlyData = [];
for (let i = 1; i <= 50; i++) {
    simulatedMonthlyLabels.push(`Month ${i}`);
    simulatedMonthlyData.push(Math.floor(Math.random() * 100)); // Angka acak antara 0 dan 100
}

// Gunakan data simulasi untuk chart
new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: {
        labels: simulatedMonthlyLabels,
        datasets: [{
            label: 'Number of findings',
            data: simulatedMonthlyData,
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


    
    // Simulasi data dengan banyak area
const simulatedAreaLabels = [];
const simulatedAreaData = [];
for (let i = 1; i <=12 ; i++) {
    simulatedAreaLabels.push(`Area ${i}`);
    simulatedAreaData.push(Math.floor(Math.random() * 100)); // Angka acak antara 0 dan 100
}

// Gunakan data simulasi untuk chart
new Chart(document.getElementById('areaChart'), {
    type: 'bar',
    data: {
        labels: simulatedAreaLabels,
        datasets: [{
            label: 'Number of findings',
            data: simulatedAreaData,
            backgroundColor: simulatedAreaData.map((_, i) => {
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
            borderColor: simulatedAreaData.map((_, i) => {
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


       // Simulasi data dengan banyak kategori
const simulatedCategoryLabels = [];
const simulatedCategoryData = [];
for (let i = 1; i <= 4; i++) {
    simulatedCategoryLabels.push(`Category ${i}`);
    simulatedCategoryData.push(Math.floor(Math.random() * 100)); // Angka acak antara 0 dan 100
}

// Gunakan data simulasi untuk chart
new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: {
        labels: simulatedCategoryLabels,
        datasets: [{
            label: 'Number of findings',
            data: simulatedCategoryData,
            backgroundColor: simulatedCategoryData.map((_, i) => {
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
            borderColor: simulatedCategoryData.map((_, i) => {
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


    
 // Simulasi data untuk status Open & Close
const simulatedStatusData = [Math.floor(Math.random() * 100), Math.floor(Math.random() * 100)];

new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: {
        labels: ['Open', 'Closed'],
        datasets: [{
            data: simulatedStatusData,
            backgroundColor: ['rgba(54, 162, 235, 0.6)', 'rgba(75, 192, 75, 0.6)'],
            borderColor: ['rgba(54, 162, 235, 1)', 'rgba(75, 192, 75, 1)'],
            borderWidth: 1
        }]
    },
    options: { responsive: true }
});

    </script>
    
</x-app-layout>
