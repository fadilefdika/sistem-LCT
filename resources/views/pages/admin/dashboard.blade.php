<x-app-layout>
    
    <div class="p-3 sm:p-5">
        <div class="mx-auto max-w-screen-2xl">
            <div class="container mx-auto px-4">
    
                <!-- Grafik dalam 2 kolom -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Grafik Garis: LCT Per Bulan -->
                    <div class="p-4 bg-white rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-4">Incident By Month</h2>
                        <canvas id="monthlyChart"></canvas>
                    </div>
    
                    <!-- Grafik Batang Horizontal: Berdasarkan Area -->
                    <div class="p-4 bg-white rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-4">Most Findings Areas</h2>
                        <canvas id="areaChart"></canvas>
                    </div>
    
                    <!-- Grafik Batang Vertikal: Berdasarkan Kategori -->
                    <div class="p-4 bg-white rounded-lg shadow">
                        <h2 class="text-xl font-bold mb-4">Types of Findings (This Month)</h2>
                        <canvas id="categoryChart"></canvas>
                    </div>
    
                    <!-- Grafik Pie: Open vs Closed -->
                    <div class="p-4 bg-white rounded-lg shadow flex flex-col">
                        <h2 class="text-xl font-bold mb-4">Status of Finding</h2>
                        <canvas id="statusChart" class="self-center" style="max-width: 300px; max-height: 300px;"></canvas>
                    </div>                                     
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Konversi nama bulan ke format bahasa Inggris
        const monthNames = {
            "Januari": "January", "Februari": "February", "Maret": "March", "April": "April",
            "Mei": "May", "Juni": "June", "Juli": "July", "Agustus": "August",
            "September": "September", "Oktober": "October", "November": "November", "Desember": "December"
        };

        // Ubah key bulan ke bahasa Inggris
        const monthlyLabels = @json(array_keys($monthlyReports->toArray())).map(month => monthNames[month] || month);
        const monthlyData = @json(array_values($monthlyReports->toArray()));

        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Jumlah Temuan',
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
                            stepSize: 1, // Pastikan angka pada sumbu Y adalah bilangan bulat
                            callback: function(value) {
                                return Number.isInteger(value) ? value : null;
                            }
                        },
                        beginAtZero: true
                    }
                }
            }
        });

    
        // Data LCT per area (Batang Horizontal)
        new Chart(document.getElementById('areaChart'), {
            type: 'bar',
            data: {
                labels: @json(array_keys($areaCounts->toArray())),
                datasets: [{
                    label: 'Jumlah Temuan',
                    data: @json(array_values($areaCounts->toArray())),
                    backgroundColor: @json(array_values($areaCounts->toArray())).map((_, i) => {
                        const colors = [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)'
                        ];
                        return colors[i % colors.length]; // Pilih warna secara berulang
                    }),
                    borderColor: @json(array_values($areaCounts->toArray())).map((_, i) => {
                        const borderColors = [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ];
                        return borderColors[i % borderColors.length]; // Pilih border warna yang sesuai
                    }),
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y', // Batang horizontal
                responsive: true,
                scales: {
                    x: {
                        ticks: {
                            stepSize: 1, // Pastikan nilai naik per 1
                            precision: 0, // Hanya bilangan bulat
                            callback: function(value) {
                                return Number.isInteger(value) ? value : '';
                            }
                        },
                        beginAtZero: true
                    }
                }
            }
        });


        // Data LCT per kategori (Batang Vertikal)
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: @json($categoryCounts->keys()->toArray()), // Pakai alias
                datasets: [{
                    label: 'Jumlah Temuan',
                    data: @json($categoryCounts->values()->toArray()),
                    backgroundColor: @json($categoryCounts->values()->toArray()).map((_, i) => {
                        const colors = [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)'
                        ];
                        return colors[i % colors.length]; // Pilih warna secara berulang
                    }),
                    borderColor: @json($categoryCounts->values()->toArray()).map((_, i) => {
                        const borderColors = [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ];
                        return borderColors[i % borderColors.length]; // Pilih border warna yang sesuai
                    }),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: { // Tampilkan nama lengkap saat hover
                        callbacks: {
                            title: function(tooltipItems) {
                                let fullNames = {
                                    "Unsafe Condition": "Kondisi Tidak Aman (Unsafe Condition)",
                                    "Unsafe Act": "Tindakan Tidak Aman (Unsafe Act)",
                                    "5S": "5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)",
                                    "Near Miss": "Near miss"
                                };
                                return fullNames[tooltipItems[0].label] || tooltipItems[0].label;
                            }
                        }
                    }
                },
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


    
        // Data Open & Close (Pie)
        new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: ['Open', 'Closed'],
                datasets: [{
                    data: @json(array_values($statusCounts)),
                    backgroundColor: ['rgba(54, 162, 235, 0.6)', 'rgba(75, 192, 75, 0.6)'],
                    borderColor: ['rgba(54, 162, 235, 1)', 'rgba(75, 192, 75, 1)'],
                    borderWidth: 1
                }]
            },
            options: { responsive: true }
        });
    </script>
    
</x-app-layout>
