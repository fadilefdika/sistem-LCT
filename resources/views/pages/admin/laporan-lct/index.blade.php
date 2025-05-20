<x-app-layout class="overflow-y-auto">
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <section class="p-3 sm:p-5 flex flex-col">
        <div class="flex flex-row justify-between items-center align-center gap-4 p-4 border rounded-xl shadow-sm bg-white mb-6 mc-2">
            <!-- Filter Popover -->
            <div x-data="{ open: false }" class="relative z-10">
                <!-- Tombol Filter -->
                <button @click="open = !open"
                    class="inline-flex items-center gap-2 rounded-lg bg-black text-white text-sm px-4 py-2 shadow hover:bg-gray-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 14.414V20a1 1 0 01-1.447.894l-4-2A1 1 0 019 18v-3.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                    Filter
                </button>

                <!-- Popover -->
                <div x-show="open" x-cloak @click.outside="open = false" x-transition
                    class="absolute mt-2 w-[90vw] max-w-5xl bg-white border border-gray-300 shadow-xl rounded-xl p-6 space-y-4">

                    <!-- Header with Close -->
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Filter Options</h2>
                        <button @click="open = false" class="text-gray-500 hover:text-gray-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Form Filters -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">

                        <!-- Hazard Level -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hazard Level</label>
                            <select wire:model="riskLevel" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="">All Hazard Level</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>

                        <!-- LCT Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">LCT Status</label>
                            <select wire:model="statusLct" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="">All statuses</option>
                                <option value="test">Test</option>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                            <input type="date" wire:model="tanggalAwal" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>

                        <!-- Date To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                            <input type="date" wire:model="tanggalAkhir" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>

                        <!-- Department -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                            <select wire:model="departemenId" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="">All Departments</option>
                                <option value="department">Department</option>
                            </select>
                        </div>

                        <!-- Area -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Area</label>
                            <select wire:model="areaId" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="">All Areas</option>
                                <option value="area">Area</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center border-t pt-4">
                        <div class="flex gap-2">
                            <button wire:click="applyFilter"
                                class="bg-black text-white px-4 py-2 text-sm rounded-lg shadow hover:bg-gray-800">
                                Terapkan Filter
                            </button>
                            <button wire:click="resetFilters"
                                class="bg-gray-500 text-white px-4 py-2 text-sm rounded-lg shadow hover:bg-gray-600">
                                Reset
                            </button>
                        </div>
                        <div wire:loading wire:target="riskLevel, statusLct, resetFilters, tanggalAwal, tanggalAkhir, departemenId, areaId"
                            class="text-sm text-gray-500">Loading...</div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <!-- PPT Button with PowerPoint styling -->
                <div class="export-option ppt-option">
                    <span class="block text-xs font-semibold text-orange-600 mb-1">Presentation Format</span>
                    <button wire:click="exportToPPT"
                            class="flex cursor-pointer items-center px-4 py-2 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-md transition duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                        Download PPT Report
                    </button>
                </div>
                
                <!-- Excel Button with Excel styling -->
                <div class="export-option excel-option">
                    <span class="block text-xs font-semibold text-green-600 mb-1">Data Format</span>
                    <button wire:click="exportToExcel"
                            class="flex cursor-pointer items-center px-4 py-2 text-sm font-medium text-white bg-green-500 hover:bg-green-600 rounded-md transition duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        Export Excel Report
                    </button>
                </div>
            </div>
        </div>


        <div class="p-6">
            <h2 class="text-2xl font-bold mb-4">Advanced Report</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <!-- Finding -->
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="text-lg font-semibold mb-2">Total Findings</h3>
                    <canvas id="findingChart"></canvas>
                </div>

                <!-- Finding by Status -->
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="text-lg font-semibold mb-2">Findings by Status</h3>
                    <canvas id="statusChart"></canvas>
                </div>

                <!-- Findings by Area -->
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="text-lg font-semibold mb-2">Findings by Area</h3>
                    <canvas id="areaChart"></canvas>
                </div>

                <!-- Findings by Category -->
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="text-lg font-semibold mb-2">Findings by Category</h3>
                    <canvas id="categoryChart"></canvas>
                </div>

                <!-- Findings by Department -->
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="text-lg font-semibold mb-2">Findings by Department</h3>
                    <canvas id="departmentChart"></canvas>
                </div>

                <!-- Overdue -->
                <div class="bg-white p-4 rounded-xl shadow">
                    <h3 class="text-lg font-semibold mb-2">Overdue Findings</h3>
                    <canvas id="overdueChart"></canvas>
                </div>
            </div>
        </div>
    </section>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Dummy Data
        const colors = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'];

        // Finding
        new Chart(document.getElementById('findingChart'), {
            type: 'doughnut',
            data: {
                labels: ['Closed', 'Open'],
                datasets: [{
                    data: [100, 300],
                    backgroundColor: [colors[0], colors[1]],
                }]
            }
        });

        // Status
        new Chart(document.getElementById('statusChart'), {
            type: 'bar',
            data: {
                labels: ['Open', 'In Progress', 'Resolved', 'Closed'],
                datasets: [{
                    label: 'Findings',
                    data: [120, 90, 60, 130],
                    backgroundColor: colors,
                }]
            }
        });

        // Area
        new Chart(document.getElementById('areaChart'), {
            type: 'pie',
            data: {
                labels: ['Area A', 'Area B', 'Area C'],
                datasets: [{
                    data: [80, 150, 70],
                    backgroundColor: colors,
                }]
            }
        });

        // Category
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: ['Safety', 'Quality', 'Compliance'],
                datasets: [{
                    label: 'Findings',
                    data: [50, 120, 100],
                    backgroundColor: colors,
                }]
            }
        });

        // Department
        new Chart(document.getElementById('departmentChart'), {
            type: 'bar',
            data: {
                labels: ['HR', 'Finance', 'Production', 'Logistics'],
                datasets: [{
                    label: 'Findings',
                    data: [30, 60, 90, 40],
                    backgroundColor: colors,
                }]
            }
        });

        // Overdue
        new Chart(document.getElementById('overdueChart'), {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [{
                    label: 'Overdue Count',
                    data: [5, 8, 4, 9],
                    borderColor: colors[3],
                    backgroundColor: 'rgba(239,68,68,0.1)',
                    fill: true,
                    tension: 0.3,
                }]
            }
        });
    </script>
</x-app-layout>
