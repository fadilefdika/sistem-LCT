<div class="max-w-full">
<section class="flex flex-col">
    <div class="flex flex-row justify-between items-center align-center gap-4 p-4 border rounded-xl shadow-xs bg-white mb-4">
        <!-- Filter Popover -->
        <div x-data="{ open: false }" class="relative z-10">
            <!-- Tombol Filter -->
            <button @click="open = !open"
                class="inline-flex items-center gap-2 rounded-lg bg-black text-white text-sm px-4 py-2 shadow text-xs hover:bg-gray-800 transition">
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

                <div class="flex flex-wrap gap-4 p-4 border rounded-xl shadow-sm bg-white mb-6">
                    <!-- Hazard Level -->
                    <div class="flex flex-col w-full sm:w-auto min-w-[180px] space-y-1">
                        <label class="text-sm font-medium text-gray-700">Hazard Level</label>
                        <select wire:model="riskLevel" class="border rounded-lg px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="">All Hazard Level</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                
                    <!-- LCT Status -->
                    <div class="flex flex-col w-full sm:w-auto min-w-[220px] space-y-1">
                        <label class="text-sm font-medium text-gray-700">LCT Status</label>
                        <select wire:model="statusLct" class="border rounded-lg px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="">All statuses</option>
                            @foreach ($statusGroups as $label => $statuses)
                                <option value="{{ implode(',', $statuses) }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                
                    <!-- Date From -->
                    <div class="flex flex-col w-full sm:w-auto min-w-[160px] space-y-1">
                        <label class="text-sm font-medium text-gray-700">Date From</label>
                        <input type="date" wire:model="tanggalAwal" class="border rounded-lg px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-black" />
                    </div>
                
                    <!-- Date To -->
                    <div class="flex flex-col w-full sm:w-auto min-w-[160px] space-y-1">
                        <label class="text-sm font-medium text-gray-700">Date To</label>
                        <input type="date" wire:model="tanggalAkhir" class="border rounded-lg px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-black" />
                    </div>
                
                    <!-- Department -->
                    <div class="flex flex-col w-full sm:w-auto min-w-[180px] space-y-1">
                        <label class="text-sm font-medium text-gray-700">Department</label>
                        <select wire:model="departemenId" class="border rounded-lg px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="">All Departments</option>
                            @foreach ($departments as $nama => $id)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                
                    <!-- Area -->
                    <div class="flex flex-col w-full sm:w-auto min-w-[180px] space-y-1">
                        <label class="text-sm font-medium text-gray-700">Area</label>
                        <select wire:model="areaId" class="border rounded-lg px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-black">
                            <option value="">All Areas</option>
                            @foreach ($areas as $nama => $id)
                                <option value="{{ $id }}">{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                
                    <!-- Tombol Filter -->
                    <div class="flex flex-col justify-end">
                        <button wire:click="applyFilter"
                                class="inline-flex items-center gap-2 rounded-lg bg-black text-white text-sm px-4 py-2 shadow hover:bg-gray-800 transition">
                            Filter
                        </button>
                    </div>
            
                    <!-- Tombol Reset -->
                    <div class="flex flex-col justify-end">
                        <button wire:click="resetFilters"
                                class="inline-flex items-center gap-2 rounded-lg bg-black text-white text-sm px-4 py-2 shadow hover:bg-gray-800 transition">
                            Reset
                        </button>
                    </div>
                    
                    <!-- Loading Indicator -->
                    <div wire:loading wire:target="riskLevel, statusLct, resetFilters, tanggalAwal, tanggalAkhir, departemenId, area, search"
                        class="flex items-center text-sm text-gray-500 mt-2">
                        Loading...
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
                            class="bg-black text-white px-4 py-2 text-sm rounded-lg shadow hover:bg-gray-600">
                            Reset
                        </button>
                    </div>
                    <div wire:loading wire:target="riskLevel, statusLct, resetFilters, tanggalAwal, tanggalAkhir, departemenId, areaId"
                        class="text-sm text-gray-500">Loading...</div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3"> 
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
            <div>
                <label for="rangeType">Pilih Rentang Waktu</label>
                <select wire:model="rangeType" id="rangeType" class="form-select">
                    <option value="daily">Harian</option>
                    <option value="weekly">Mingguan</option>
                    <option value="monthly">Bulanan</option>
                    <option value="semester">Semester</option>
                    <option value="yearly">Tahunan</option>
                    <option value="custom">Kustom</option>
                </select>
            </div>
        </div>
    </div>


    @php
        $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
        $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');          
    @endphp

    @if($roleName === 'ehs')
        <div class="mb-3">
            <h2 class="text-base font-bold mb-4">Advanced Report</h2>

            <div class="space-y-4">
                <!-- Baris 1: Total Findings dan Findings by Status -->
                <div class="flex flex-col xl:flex-row gap-4">
                    <!-- Total Findings -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] xl:w-2/3 w-full">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold">Total Findings</h3>
                            <div class="flex gap-2">
                                <select id="findingYear" class="text-sm border-gray-300 rounded-md">
                                    @for ($year = now()->year; $year >= 2020; $year--)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                                <select id="findingMonth" class="text-sm border-gray-300 rounded-md">
                                    <option value="">All Months</option>
                                    @foreach ([
                                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May',
                                        6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct',
                                        11 => 'Nov', 12 => 'Dec'
                                    ] as $num => $month)
                                        <option value="{{ $num }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="h-[200px]">
                            <canvas id="findingChart"></canvas>
                        </div>
                    </div>
            
                    <!-- Findings by Status -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] xl:w-1/3 w-full">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold">Findings by Status</h3>
                            <div class="flex gap-2">
                                <select id="statusYear" class="text-sm border-gray-300 rounded-md">
                                    @for ($year = now()->year; $year >= 2020; $year--)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                                <select id="statusMonth" class="text-sm border-gray-300 rounded-md">
                                    <option value="">All Months</option>
                                    @foreach ([1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May',
                                            6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct',
                                            11 => 'Nov', 12 => 'Dec'] as $num => $month)
                                        <option value="{{ $num }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="h-[200px]">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>

                </div>
            
                <!-- Baris 2: Findings by Category dan Area -->
                <div class="flex flex-col xl:flex-row gap-4">
                    <!-- Findings by Category -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] xl:w-1/3 w-full">
                        <h3 class="text-sm font-semibold mb-2">Findings by Category</h3>
                    
                        <!-- Filter Year & Month -->
                        <div class="flex gap-2 mb-3">
                            <select id="categoryYear" class="text-sm border-gray-300 rounded-md">
                                @for ($year = now()->year; $year >= 2020; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                    
                            <select id="categoryMonth" class="text-sm border-gray-300 rounded-md">
                                <option value="">All Months</option>
                                @foreach ([
                                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May',
                                    6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct',
                                    11 => 'Nov', 12 => 'Dec'
                                ] as $num => $month)
                                    <option value="{{ $num }}">{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                    
                        <div class="h-[200px]">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                    
            
                    <!-- Findings by Area -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] overflow-x-auto xl:w-2/3 w-full">
                        <h3 class="text-sm font-semibold mb-2">Findings by Area</h3>
                    
                        <!-- Filter Year & Month -->
                        <div class="flex gap-2 mb-3">
                            <select id="areaYear" class="text-sm border-gray-300 rounded-md">
                                @for ($year = now()->year; $year >= 2020; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                    
                            <select id="areaMonth" class="text-sm border-gray-300 rounded-md">
                                <option value="">All Months</option>
                                @foreach ([
                                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May',
                                    6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct',
                                    11 => 'Nov', 12 => 'Dec'
                                ] as $num => $month)
                                    <option value="{{ $num }}">{{ $month }}</option>
                                @endforeach
                            </select>
                        </div>
                    
                        <div class="h-[200px]"><!-- beri min-width agar scroll muncul jika area banyak -->
                            <canvas id="areaChart"></canvas>
                        </div>
                    </div>                    
                </div>
            
                <!-- Baris 3: Department dan Overdue -->
                <div class="flex flex-col xl:flex-row gap-4">
                    <!-- Department -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] overflow-x-auto xl:w-2/3 w-full">
                        <h3 class="text-sm font-semibold mb-2">Findings by Department</h3>
                        <div class="flex gap-2 mb-3">
                            <select id="departmentYear" class="text-sm border-gray-300 rounded-md">
                            @for ($year = now()->year; $year >= 2020; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                            </select>
                            <select id="departmentMonth" class="text-sm border-gray-300 rounded-md">
                            <option value="">All Months</option>
                            @foreach ([
                                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May',
                                6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct',
                                11 => 'Nov', 12 => 'Dec'
                            ] as $num => $month)
                                <option value="{{ $num }}">{{ $month }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="h-[200px]">
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>

                    <!-- Overdue -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] overflow-x-auto xl:w-2/3 w-full">
                        <h3 class="text-sm font-semibold mb-2">Overdue Findings</h3>
                        <div class="flex gap-2 mb-3">
                            <select id="overdueYear" class="text-sm border-gray-300 rounded-md">
                              @for ($year = now()->year; $year >= 2020; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                              @endfor
                            </select>
                            <select id="overdueMonth" class="text-sm border-gray-300 rounded-md">
                              <option value="">All Months</option>
                              @foreach ([
                                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May',
                                6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct',
                                11 => 'Nov', 12 => 'Dec'
                              ] as $num => $month)
                                <option value="{{ $num }}">{{ $month }}</option>
                              @endforeach
                            </select>
                          </div>
                        <div class="h-[200px]">
                            <canvas id="overdueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            
            
        </div>
    @endif
</section>

<div class="bg-white p-6 relative shadow-md rounded-xl overflow-x-auto">
     
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div x-data="{ openRow: null }">
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-300 shadow-sm border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr class="text-left text-sm font-semibold text-gray-600">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Finding Date</th>
                        <th class="px-4 py-3">Due Date</th>
                        <th class="px-4 py-3">Due Date Permanent</th>
                        <th class="px-4 py-3">PIC</th>
                        <th class="px-4 py-3">Hazard Level</th>
                        <th class="px-4 py-3">Progress Status</th>
                        <th class="px-4 py-3">Completion Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($laporans as $index => $laporan)
    
                    @php
                        $laporanId = $laporan->id;
                        $tindakan_perbaikan = collect(json_decode($laporan->tindakan_perbaikan, true))->map(function ($entry) {
                            return [
                                'tanggal' => $entry['tanggal'],
                                'tindakan' => $entry['tindakan'],
                                'bukti' => collect($entry['bukti'])->map(fn($path) => asset('storage/' . $path)),
                            ];
                        });
    
                        // Status dan warna
                        $statusMapping = [
                                    'open' => ['label' => 'Open', 'color' => 'bg-gray-500', 'tracking' => 'Report has been created'],
                                    'review' => ['label' => 'Review', 'color' => 'bg-purple-500', 'tracking' => 'Report is under review'],
                                    'in_progress' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'Not yet viewed by PIC'],
                                    'progress_work' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'PIC has viewed the report'],
                                    'work_permanent' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'Permanent LCT in progress'],
                                    'waiting_approval' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Awaiting EHS approval'],
                                    'waiting_approval_temporary' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for temporary LCT approval from EHS'],
                                    'waiting_approval_permanent' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Awaiting EHS approval'],
                                    'waiting_approval_taskbudget' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting approval manager'],
                                    'approved' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Approved by EHS'],
                                    'approved_temporary' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Temporary approved by EHS'],
                                    'approved_permanent' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Permanent approved by EHS'],
                                    'approved_taskbudget' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Manager approved task & budget'],
                                    'revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'PIC must revise LCT Low'],
                                    'temporary_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'Temporary LCT needs revision by PIC'],
                                    'permanent_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'Permanent LCT needs revision by PIC'],
                                    'taskbudget_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'PIC must revise task & budget'],
                                    'closed' => ['label' => 'Closed', 'color' => 'bg-green-700', 'tracking' => 'EHS closed the report'],
                                ]; // Potong agar pendek — gunakan definisi Anda sebelumnya
                        $status = $statusMapping[$laporan->status_lct] ?? [
                            'label' => 'Unknown',
                            'color' => 'bg-gray-400',
                            'tracking' => 'Status not found'
                        ];
                        $bahayaColors = [
                            'High' => 'bg-red-500',
                            'Medium' => 'bg-yellow-500',
                            'Low' => 'bg-green-500'
                        ];
                    @endphp
    
                    <!-- Baris utama -->
                    <tr @click="openRow === {{ $laporanId }} ? openRow = null : openRow = {{ $laporanId }}"
                        class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out cursor-pointer">
                        <td class="px-4 py-3 text-xs text-center font-semibold text-gray-800 w-12">{{ $laporans->firstItem() + $index }}</td>
                        <td class="px-4 py-3 text-xs text-gray-800 w-32 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('F d, Y') }}
                           
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-800 w-32 whitespace-nowrap">
                            @if($laporan->status_lct == 'open')
                                <p>-</p>
                            @elseif($laporan->tingkat_bahaya !== 'Low')
                                {{ \Carbon\Carbon::parse($laporan->due_date)->format('F d, Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($laporan->due_date_temp)->format('F d, Y') }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-800 w-32 whitespace-nowrap">
                            @if($laporan->status_lct == 'open')
                                <p>-</p>
                            @elseif($laporan->tingkat_bahaya !== 'Low')
                                {{ \Carbon\Carbon::parse($laporan->due_date_perm)->format('F d, Y') }}
                            @else
                                <p>-</p>
                            @endif
                        </td>
                        @php
                            $fullname = $laporan->picUser->fullname ?? '-';
                            if ($fullname !== '-') {
                                $parts = explode(' ', $fullname);
                                $result = '';

                                foreach ($parts as $index => $part) {
                                    if ($index < 2) {
                                        // tampilkan dua nama pertama secara lengkap
                                        $result .= $part . ' ';
                                    } else {
                                        // untuk nama ketiga dan seterusnya, ambil inisial saja
                                        $result .= strtoupper(substr($part, 0, 1)) . ' ';
                                    }
                                }

                                $result = trim($result);
                            } else {
                                $result = '-';
                            }
                        @endphp

                        <td class="px-4 py-3 text-xs text-gray-800 w-40 whitespace-nowrap">{{ $result }}</td>

                        <td class="px-4 py-3 text-xs text-gray-800 w-28">
                            <span class="px-3 py-1 text-xs text-gray-800 rounded-full">
                                {{ $laporan->tingkat_bahaya }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-800 w-36">
                            <span class="inline-flex items-center justify-center px-3 py-1 text-xs text-gray-800 rounded-full">
                                {{ $status['label'] }}
                            </span>
                        </td>
                        
                        <td class="px-4 py-3 text-xs text-gray-800 w-32 whitespace-nowrap">
                            @if ($laporan->date_completion)
                                {{ \Carbon\Carbon::parse($laporan->date_completion)->format('F d, Y') }}
                            @else
                                @php
                                    $dueDate = \Carbon\Carbon::parse($laporan->due_date)->startOfDay();
                                    $today = \Carbon\Carbon::now()->startOfDay();
                                    $overdueDays = $dueDate->diffInDays($today, false);
                                @endphp
    
                                @if ($overdueDays > 0)
                                    <span class="bg-red-100 text-red-600 text-sm font-semibold px-2 py-1 rounded">
                                        Overdue {{ $overdueDays }} days
                                    </span>
                                @elseif ($overdueDays === 0)
                                    <span class="bg-yellow-100 text-yellow-600 text-sm font-semibold px-2 py-1 rounded">
                                        Due Today
                                    </span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            @endif
                        </td>
                    </tr>
    
                    <!-- Detail Row -->
                    @php
                        $raw = $laporan->bukti_temuan;

                        // Kalau array, langsung gunakan. Kalau string, decode dulu.
                        $buktiArray = is_array($raw) ? $raw : json_decode($raw, true);

                        // Jika null karena gagal decode, fallback ke array kosong
                        $buktiArray = $buktiArray ?? [];

                        $bukti_temuan = collect($buktiArray)->map(fn($path) => asset('storage/' . $path));

                        $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
                        $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');        
                                        if ($roleName === 'ehs') {
                                        $routeName = $laporan->status_lct === 'open'
                                                ? 'ehs.reporting.show.new'
                                                : 'ehs.reporting.show';
                                        } elseif ($roleName === 'pic') {
                                            $routeName = 'admin.manajemen-lct.show';
                                        } else {
                                            $routeName = 'admin.reporting.show';
                                        }

                    @endphp
    
                    <tr x-show="openRow === {{ $laporanId }}"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-y-90"
                        x-transition:enter-end="opacity-100 scale-y-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-y-100"
                        x-transition:leave-end="opacity-0 scale-y-90"
                        style="transform-origin: top">
                        <td colspan="8" class="w-full bg-gray-50 px-6 py-6">
                            <div class="w-full bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
                                <!-- Header -->
                                <div class="border-b pb-4 mb-4">
                                    <div class="flex justify-between items-center flex-wrap gap-4">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800 mb-1">📝 Finding Details</h3>
                                            <p class="text-xs text-gray-500">Report ID: <span class="font-medium text-gray-700">{{ $laporan->id_laporan_lct }}</span></p>
                                        </div>
                                        <div class="flex gap-4">
                                            <div>
                                                <p class="text-[10px] text-gray-500 uppercase font-semibold mb-1">Status</p>
                                                <span class="inline-flex px-3 py-1 text-[10px] font-semibold text-white rounded-full {{ $status['color'] }}">
                                                    {{ $status['label'] }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-gray-500 uppercase font-semibold mb-1">Hazard Level</p>
                                                <span class="inline-flex px-3 py-1 text-[10px] font-semibold text-white rounded-full {{ $bahayaColors[$laporan->tingkat_bahaya] ?? 'bg-gray-400' }}">
                                                    {{ $laporan->tingkat_bahaya }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                                <div x-data="{ activeTab: 'finder' }">
                                    <!-- Tabs -->
                                    <div class="flex border-b mb-4">
                                        <button @click="activeTab = 'finder'" 
                                                :class="activeTab === 'finder' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'" 
                                                class="px-4 py-2 text-sm font-semibold cursor-pointer">
                                            Finder Report
                                        </button>

                                        @if(!in_array($laporan->status_lct, ['open', 'in_progress', 'progress_work']))
                                            <button @click="activeTab = 'pic'" 
                                                    :class="activeTab === 'pic' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'" 
                                                    class="px-4 py-2 text-sm font-semibold cursor-pointer">
                                                PIC Improvement
                                            </button>
                                        @endif
                                    </div>

                                
                                    <!-- Finder Section -->
                                    <div x-show="activeTab === 'finder'">
                                        <div>
                                            <!-- Gambar dan Detail -->
                                            <div class="flex flex-col lg:flex-row gap-6 w-full">
                                                <div class="lg:w-1/3 w-full" x-data="{
                                                    images: @js($bukti_temuan),
                                                    current: 0,
                                                    interval: null,
                                                    startSlider() {
                                                        if (this.images.length > 1) {
                                                            this.interval = setInterval(() => {
                                                                this.next()
                                                            }, 3000)
                                                        }
                                                    },
                                                    stopSlider() {
                                                        clearInterval(this.interval)
                                                    },
                                                    next() {
                                                        this.current = (this.current + 1) % this.images.length
                                                    },
                                                    prev() {
                                                        this.current = (this.current - 1 + this.images.length) % this.images.length
                                                    }
                                                }" x-init="startSlider" @mouseenter="stopSlider" @mouseleave="startSlider">
                                                    <div class="relative aspect-video bg-gray-100 rounded-md overflow-hidden border border-gray-200">
                                                        <template x-for="(img, index) in images" :key="index">
                                                            <img
                                                                :src="img"
                                                                alt="Evidence"
                                                                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500"
                                                                :class="{ 'opacity-100': index === current, 'opacity-0': index !== current }"
                                                            >
                                                        </template>
                                                
                                                        <!-- Panah Navigasi (hanya jika gambar > 1) -->
                                                        <template x-if="images.length > 1">
                                                            <button @click="prev"
                                                                class="absolute cursor-pointer left-0 top-1/2 -translate-y-1/2 bg-white/60 hover:bg-white text-gray-800 px-2 py-1 rounded-r-md">
                                                                ‹
                                                            </button>
                                                        </template>
                                                
                                                        <template x-if="images.length > 1">
                                                            <button @click="next"
                                                                class="absolute cursor-pointer right-0 top-1/2 -translate-y-1/2 bg-white/60 hover:bg-white text-gray-800 px-2 py-1 rounded-l-md">
                                                                ›
                                                            </button>
                                                        </template>
                                                    </div>
                                                
                                                    <div class="mt-2 text-[10px] text-gray-500 text-center">
                                                        <span x-text="'This report contains ' + images.length + ' image' + (images.length > 1 ? 's' : '') + '.'"></span>
                                                    </div>
                                                </div>
                                                
                                    
                                                <div class="lg:w-2/3 w-full grid grid-cols-2 gap-4">
                                                    <div>
                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Finding Date</p>
                                                        <p class="text-xs text-gray-800">{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('d M Y') ?? '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Finder</p>
                                                        <p class="text-xs text-gray-800">{{ $laporan->user->fullname ?? '-' }}</p>
                                                    </div>
                                                    <div class="space-y-1">
                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Category</p>
                                                        <p class="text-xs text-gray-800">
                                                            {{ $laporan->kategori->nama_kategori === '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)' ? '5S' : $laporan->kategori->nama_kategori }}
                                                        </p>
                                                    </div>
                                                    <div class="space-y-1">
                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Area</p>
                                                        <p class="text-xs text-gray-800">
                                                            {{ optional($laporan->area)->nama_area ?? '-' }} - {{ $laporan->detail_area ?? '-' }}
                                                        </p>                                            
                                                    </div>
                                                    <div class="space-y-1 col-span-2">
                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Finding</p>
                                                        <p class="text-xs text-gray-800">{{ $laporan->temuan_ketidaksesuaian ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                    
                                            <!-- Safety Recommendation -->
                                            <div class="bg-blue-50/50 p-4 rounded-md border border-blue-100 w-full">
                                                <div class="flex items-start space-x-2">
                                                    <div class="mt-0.5">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-xs font-medium text-blue-800 mb-1">Safety Recommendation</h4>
                                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed">{{ $laporan->rekomendasi_safety }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                    
                                            <!-- Action Button -->
                                            <div class="flex justify-end pt-2">
                                                <a href="{{ route($routeName, $laporan->id_laporan_lct) }}"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                                    Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <!-- PIC Improvement Section -->
                                    @if(!in_array($laporan->status_lct, ['open', 'in_progress', 'progress_work']))
                                        <div x-show="activeTab === 'pic'" class="space-y-8">
                                            @php
                                                $tindakanPerbaikan = collect(json_decode($laporan->tindakan_perbaikan, true))->map(function ($entry) {
                                                    return [
                                                        'tanggal' => $entry['tanggal'],
                                                        'tindakan' => $entry['tindakan'],
                                                        'bukti' => collect($entry['bukti'])->map(fn($path) => asset('storage/' . $path)),
                                                    ];
                                                })->toArray(); // pastikan kembali ke array agar bisa digunakan di @foreach
                                            @endphp


                                            @if(is_array($tindakanPerbaikan) && count($tindakanPerbaikan))
                                                <div x-data="{
                                                    slides: @js($tindakanPerbaikan),
                                                    current: 0,
                                                    prev() {
                                                        this.current = (this.current - 1 + this.slides.length) % this.slides.length;
                                                    },
                                                    next() {
                                                        this.current = (this.current + 1) % this.slides.length;
                                                    }
                                                    }" class="relative overflow-hidden">

                                                    <!-- Wrapper untuk semua slide -->
                                                    <div class="flex transition-transform duration-500 ease-in-out"
                                                        :style="`transform: translateX(-${current * 100}%)`">

                                                        <template x-for="(tp, index) in slides" :key="index">
                                                            <div class="min-w-full p-4 space-y-4 border rounded-md shadow-md">
                                                                {{-- Konten sama seperti sebelumnya --}}
                                                                <div class="flex flex-col lg:flex-row gap-6">
                                                                    <!-- Image Slider -->
                                                                    <div class="lg:w-1/3 w-full" x-data="{
                                                                        images: tp.bukti,
                                                                        currentImg: 0,
                                                                        interval: null,
                                                                        startSlider() {
                                                                            if (this.images.length > 1) {
                                                                                this.interval = setInterval(() => this.next(), 3000);
                                                                            }
                                                                        },
                                                                        stopSlider() {
                                                                            clearInterval(this.interval);
                                                                        },
                                                                        next() {
                                                                            this.currentImg = (this.currentImg + 1) % this.images.length;
                                                                        },
                                                                        prev() {
                                                                            this.currentImg = (this.currentImg - 1 + this.images.length) % this.images.length;
                                                                        }
                                                                    }" x-init="startSlider" @mouseenter="stopSlider" @mouseleave="startSlider">
                                                                    <p class="text-center text-[10px] text-gray-500 mb-2" x-text="index === 0 ? '' : `Revision #${index}`"></p>

                                                                        <div class="relative aspect-video rounded-md overflow-hidden border border-gray-200 mb-2">
                                                                            <template x-for="(img, idx) in images" :key="idx">
                                                                                <img :src="img" alt="Evidence"
                                                                                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500"
                                                                                    :class="{ 'opacity-100': idx === currentImg, 'opacity-0': idx !== currentImg }">
                                                                            </template>

                                                                            <template x-if="images.length > 1">
                                                                                <button @click="prev"
                                                                                    class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white text-gray-800 px-2 py-1 rounded-r-md">
                                                                                    ‹
                                                                                </button>
                                                                            </template>
                                                                            <template x-if="images.length > 1">
                                                                                <button @click="next"
                                                                                    class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white text-gray-800 px-2 py-1 rounded-l-md">
                                                                                    ›
                                                                                </button>
                                                                            </template>
                                                                        </div>

                                                                        <p class="text-center text-[10px] text-gray-500 mt-2" x-text="'This correction contains ' + images.length + ' image' + (images.length > 1 ? 's' : '') + '.'"></p>
                                                                    </div>

                                                                    <!-- Detail Tindakan -->
                                                                    <div class="lg:w-2/3 w-full grid grid-cols-2 gap-6 text-sm bg-white p-4 rounded-md shadow-sm">
                                                                        <div class="border-b border-gray-200 pb-2">
                                                                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Completion Date</p>
                                                                            <p class="text-sm text-gray-900" x-text="new Date(tp.tanggal).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) ?? '-'"></p>
                                                                        </div>
                                                                        
                                                                        <div class="border-b border-gray-200 pb-2">
                                                                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">
                                                                                {{ strtolower($laporan->tingkat_bahaya) === 'low' ? 'Due Date' : 'Temporary Due Date' }}
                                                                            </p>
                                                                            <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($laporan->due_date)->format('d M Y') }}</p>
                                                                        </div>
                                                                        
                                                                        @if(strtolower($laporan->tingkat_bahaya) !== 'low')
                                                                        <div class="border-b border-gray-200 pb-2">
                                                                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Permanent Due Date</p>
                                                                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($laporan->due_date)->format('d M Y') }}</p>
                                                                        </div>
                                                                        @endif
                                                                    
                                                                        <div class="border-b border-gray-200 pb-2">
                                                                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">PIC</p>
                                                                        <p class="text-sm text-gray-900">{{ $laporan->picUser->fullname ?? '-' }}</p>
                                                                        </div>
                                                                    </div>                                                                              
                                                                </div>

                                                                <!-- Safety Recommendation -->
                                                                <div class="bg-blue-50/50 p-4 rounded-md border border-blue-100">
                                                                    <div class="flex items-start space-x-2">
                                                                        <div class="mt-0.5">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                                                                            </svg>
                                                                        </div>
                                                                        <div>
                                                                            <h4 class="text-xs font-medium text-blue-800 mb-1">Corrective Action</h4>
                                                                            <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed" x-text="tp.tindakan"></p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Action Button -->
                                                                <div class="flex justify-end">
                                                                    <a href="{{ route($routeName, $laporan->id_laporan_lct) }}"
                                                                        class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                                                        Details
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                                        </svg>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    <!-- Navigation: tampilkan hanya jika slide > 1 -->
                                                    @if(count($tindakanPerbaikan) > 1)
                                                        <div class="flex justify-between mt-4">
                                                            <button @click="prev()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 cursor-pointer">
                                                                ‹ Prev
                                                            </button>
                                                            <button @click="next()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 cursor-pointer">
                                                                Next ›
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-500 italic">No corrective action provided.</p>
                                            @endif
                                        </div>
                                    @endif


                                </div>
                            </div>
                        </td>                        
                    </tr>
    
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-6 text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6">
                                    </path>
                                </svg>
                                <p class="text-sm">No reports are available.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    
    <div class="mt-6 flex flex-col sm:flex-row sm:justify-between sm:items-center border-t px-5 py-3 gap-3">
        <span class="text-sm text-gray-600 text-center sm:text-left">
            Showing {{ $laporans->firstItem() }} to {{ $laporans->lastItem() }} of {{ $laporans->total() }} entries
        </span>
        <div class="flex justify-center sm:justify-end">
            {{ $laporans->links('pagination::tailwind') }}
        </div>
    </div>
    
</div>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        const colors = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'];

        let findingChart;
        function renderFindingChart(labels, data) {
            const ctx = document.getElementById('findingChart').getContext('2d');
            if (findingChart) findingChart.destroy();

            findingChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Findings',
                        data: data,
                        borderColor: '#0069AA',
                        backgroundColor: 'rgba(0, 105, 170, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3,
                        pointBackgroundColor: '#0069AA'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { title: { display: true, text: 'Date' } },
                        y: { beginAtZero: true, title: { display: true, text: 'Total Findings' }, ticks: { precision: 0 } }
                    }
                }
            });
        }

        function loadFindingData(year, month) {
            const dummyLabels = month ? Array.from({ length: 28 }, (_, i) => `${i + 1}`) : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const dummyData = dummyLabels.map(() => Math.floor(Math.random() * 10) + 1);
            renderFindingChart(dummyLabels, dummyData);
        }

        const yearSelect = document.getElementById('findingYear');
        const monthSelect = document.getElementById('findingMonth');
        yearSelect.addEventListener('change', () => loadFindingData(yearSelect.value, monthSelect.value));
        monthSelect.addEventListener('change', () => loadFindingData(yearSelect.value, monthSelect.value));
        loadFindingData(yearSelect.value, monthSelect.value);

    </script>

    <script>
        const ctxStatus = document.getElementById('statusChart').getContext('2d');

        const statusChart = new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Open', 'Closed', 'In Progress', 'Overdue'],
                datasets: [{
                    data: [50, 120, 40, 30],
                    backgroundColor: ['#F59E0B', '#10B981', '#3B82F6', '#EF4444'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    datalabels: {
                        color: '#fff',          // Warna putih
                        font: {
                            weight: 'bold',
                            size: 9            // Ukuran font yang cukup besar agar terlihat
                        },
                        formatter: (value, ctx) => {
                            let data = ctx.chart.data.datasets[0].data;
                            let total = data.reduce((a, b) => a + b, 0);
                            if (total === 0) return '0%';
                            let percentage = (value / total * 100).toFixed(1);
                            return percentage + '%';
                        },
                        // Tempatkan label di tengah slice (default sudah oke)
                        anchor: 'center',
                        align: 'center',
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

        function generateDummyStatusData(year, month) {
            // Untuk variasi nilai per kombinasi filter
            const seed = parseInt(year + (month || '0'));
            const random = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;

            // Bisa juga pakai seedable random lib untuk hasil konsisten
            const open = random(10, 50);
            const closed = random(20, 70);
            const inProgress = random(5, 40);
            const overdue = random(0, 25);

            return [open, closed, inProgress, overdue];
        }

        function updateStatusChartWithDummy() {
            const year = document.getElementById('statusYear').value;
            const month = document.getElementById('statusMonth').value;

            const dummyData = generateDummyStatusData(year, month);
            statusChart.data.datasets[0].data = dummyData;
            statusChart.update();
        }

        // Jalankan saat pertama kali
        updateStatusChartWithDummy();

        // Listener untuk filter dropdown
        document.getElementById('statusYear').addEventListener('change', updateStatusChartWithDummy);
        document.getElementById('statusMonth').addEventListener('change', updateStatusChartWithDummy);
    </script>

    <script>  const ctxCategory = document.getElementById('categoryChart').getContext('2d');
        const categoryLabels = ['Unsafe Act', '5S', 'Unsafe Condition', 'Nearmiss'];
        const categoryColors = ['#F87171', '#60A5FA', '#34D399', '#FBBF24']; // contoh warna per kategori

        // Inisialisasi chart dengan data kosong dulu
        const categoryChart = new Chart(ctxCategory, {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Findings',
                    data: [0, 0, 0, 0], // awalnya kosong
                    backgroundColor: categoryColors,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: { x: { beginAtZero: true } }
            }
        });

        // Fungsi buat generate dummy data acak untuk kategori
        function generateDummyCategoryData(year, month) {
            // Misal: data berbeda jika bulan dipilih, atau default jika tidak
            // Disini cuma random saja sebagai contoh
            return categoryLabels.map(() => Math.floor(Math.random() * 100) + 1);
        }

        // Fungsi update chart berdasarkan filter
        function updateCategoryChart() {
            const year = document.getElementById('categoryYear').value;
            const month = document.getElementById('categoryMonth').value;

            // Generate dummy data sesuai year & month
            const dummyData = generateDummyCategoryData(year, month);

            categoryChart.data.datasets[0].data = dummyData;
            categoryChart.update();
        }

        // Event listener untuk filter
        document.getElementById('categoryYear').addEventListener('change', updateCategoryChart);
        document.getElementById('categoryMonth').addEventListener('change', updateCategoryChart);

        // Initial render
        updateCategoryChart();
    </script>

    <script>
        const ctxArea = document.getElementById('areaChart').getContext('2d');

        // Generate label area sesuai jumlah
        function generateAreaLabels(count = 20) {
            return Array.from({ length: count }, (_, i) => `Area ${i + 1}`);
        }

        // Generate data dummy
        function generateDummyAreaData(count = 20) {
            return Array.from({ length: count }, () => Math.floor(Math.random() * 100));
        }

        const initialCount = 20;
        const areaLabels = generateAreaLabels(initialCount);
        const areaData = generateDummyAreaData(initialCount);

        const areaChart = new Chart(ctxArea, {
            type: 'bar',
            data: {
                labels: areaLabels,
                datasets: [{
                    label: 'Findings by Area',
                    data: areaData,
                    backgroundColor: '#0069AA',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        ticks: {
                            font: { size: 9 },
                            maxRotation: 45,
                            minRotation: 45,
                            autoSkip: false
                        },
                        maxBarThickness: 5,   // maksimal ketebalan bar (px)
                        barPercentage: 0.15,    // coba perkecil lagi jadi 15% dari slot
                        categoryPercentage: 0.4 // perkecil ruang kategori untuk jarak antar bar
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        // Update chart data berdasarkan filter
        function updateAreaChart() {
            const year = document.getElementById('areaYear').value;
            const month = document.getElementById('areaMonth').value;

            // Contoh: jika month dipilih area 15, jika tidak area 20
            const count = month ? 15 : 20;

            const newLabels = generateAreaLabels(count);
            const newData = generateDummyAreaData(count);

            areaChart.data.labels = newLabels;
            areaChart.data.datasets[0].data = newData;
            areaChart.update();
        }

        // Event listener
        document.getElementById('areaYear').addEventListener('change', updateAreaChart);
        document.getElementById('areaMonth').addEventListener('change', updateAreaChart);

        // Render awal
        updateAreaChart();
    </script>

    <script>
        const ctx = document.getElementById('departmentChart').getContext('2d');

        // Fungsi generate dummy data berdasarkan year dan month
        function generateDummyData(year, month) {
            // Dummy labels dept
            const labels = Array.from({ length: 12 }, (_, i) => `Dept ${i + 1}`);

            // Data random, bisa ditambah logika jika mau beda berdasarkan filter
            const data = labels.map(() => Math.floor(Math.random() * 80) + 1);

            return { labels, data };
        }

        // Render chart awal
        let currentYear = document.getElementById('departmentYear').value;
        let currentMonth = document.getElementById('departmentMonth').value;

        let { labels, data } = generateDummyData(currentYear, currentMonth);

        let departmentChart = new Chart(ctx, {
            type: 'bar',
            data: {
            labels: labels,
            datasets: [{
                label: 'Findings',
                data: data,
                backgroundColor: '#0069AA',
            }]
            },
            options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
            }
        });

        // Update chart saat filter berubah
        document.getElementById('departmentYear').addEventListener('change', function() {
            currentYear = this.value;
            updateChart();
        });

        document.getElementById('departmentMonth').addEventListener('change', function() {
            currentMonth = this.value;
            updateChart();
        });

        function updateChart() {
            const newData = generateDummyData(currentYear, currentMonth);
            departmentChart.data.labels = newData.labels;
            departmentChart.data.datasets[0].data = newData.data;
            departmentChart.update();
        }
    </script>

    <script>
        const ctxOverdue = document.getElementById('overdueChart').getContext('2d');

        // Dummy data generator untuk 4 minggu
        function generateOverdueData(year, month) {
            const labelsOverdue = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            const dataOverdue = labelsOverdue.map(() => Math.floor(Math.random() * 16));
            return { labelsOverdue, dataOverdue };
        }

        let currentYearOverdue = document.getElementById('overdueYear').value;
        let currentMonthOverdue = document.getElementById('overdueMonth').value;

        let { labelsOverdue, dataOverdue } = generateOverdueData(currentYearOverdue, currentMonthOverdue);

        let overdueChart = new Chart(ctxOverdue, {
            type: 'line',
            data: {
                labels: labelsOverdue,
                datasets: [{
                    label: 'Overdue Count',
                    data: dataOverdue,
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239,68,68,0.1)',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });

        // Event listener untuk update chart jika filter berubah
        document.getElementById('overdueYear').addEventListener('change', function () {
            currentYearOverdue = this.value;
            updateChart();
        });

        document.getElementById('overdueMonth').addEventListener('change', function () {
            currentMonthOverdue = this.value;
            updateChart();
        });

        function updateChart() {
            const newData = generateOverdueData(currentYearOverdue, currentMonthOverdue);
            overdueChart.data.labels = newData.labelsOverdue;
            overdueChart.data.datasets[0].data = newData.dataOverdue;
            overdueChart.update();
        }

    </script>
    
</div>
