<div class="bg-white p-6 relative shadow-md rounded-xl overflow-x-auto">
    
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
            {{-- <svg class="animate-spin h-4 w-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg> --}}
            Loading...
        </div>
    </div>
    <div class="border-t pt-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">Export Reports</label>
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
    
    
    
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-300 shadow-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr class="text-left text-sm font-semibold text-gray-600">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Non-Conformity Findings</th>
                    <th class="px-4 py-3">PIC</th>
                    <th class="px-4 py-3">Hazard Level</th>
                    <th class="px-4 py-3">Progress Status</th>
                    <th class="px-4 py-3">Tracking Status</th>
                    <th class="px-4 py-3">Due Date</th>
                    <th class="px-4 py-3">Due Date Permanent</th>
                    <th class="px-4 py-3">Completion Date</th>
                    <th class="px-4 py-3">Action</th>
                </tr>                
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($laporans as $index => $laporan)
                <tr class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out">
                    <td class="px-4 py-3 text-center font-semibold text-gray-800 w-12">
                        {{ $laporans->firstItem() + $index }}
                    </td>
                    <td class="px-4 py-3 text-gray-800 max-w-xs truncate cursor-pointer relative group">
                        <span class="temuan-clamp" title="{{ $laporan->temuan_ketidaksesuaian }}">
                            {{ $laporan->temuan_ketidaksesuaian }} </span>
                    </td>
                    <td class="px-4 py-3 text-gray-800 w-40 whitespace-nowrap">{{ $laporan->picUser->fullname ?? '-' }}</td>
    
                    <!-- Tingkat Bahaya -->
                    <td class="px-4 py-3 text-gray-800 w-28">
                        @php
                            $bahayaColors = [
                                'High' => 'bg-red-500',
                                'Medium' => 'bg-yellow-500',
                                'Low' => 'bg-green-500'
                            ];
                        @endphp
                        <span class="px-3 py-1 text-xs font-semibold text-white rounded-full {{ $bahayaColors[$laporan->tingkat_bahaya] ?? 'bg-gray-400' }}">
                            {{ $laporan->tingkat_bahaya }}
                        </span>
                    </td>

                    <!-- Status Progress -->
                    <td class="px-4 py-3 text-gray-800 w-36">
                        @php
                            // List of status with labels, colors, and tracking descriptions
                            $statusMapping = [
                                'open' => ['label' => 'Open', 'color' => 'bg-gray-500', 'tracking' => 'Report has been created'],
                                'review' => ['label' => 'Review', 'color' => 'bg-purple-500', 'tracking' => 'Report is under review'],
                                'in_progress' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'Report has been sent, but PIC has not viewed it'],
                                'progress_work' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'PIC has viewed the report'],
                                'work_permanent' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'PIC is working on a permanent LCT'],
                                'waiting_approval' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for LCT Low approval from EHS'],
                                'waiting_approval_temporary' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for temporary LCT approval from EHS'],
                                'waiting_approval_permanent' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for permanent LCT approval from EHS'],
                                'waiting_approval_taskbudget' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for task and budget approval from the manager'],
                                'approved' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'LCT Low has been approved by EHS'],
                                'approved_temporary' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Temporary LCT has been approved by EHS'],
                                'approved_permanent' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Permanent LCT has been approved by EHS'],
                                'approved_taskbudget' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Task and budget for permanent LCT has been approved by the manager'],
                                'revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'LCT Low needs revision by PIC'],
                                'temporary_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'Temporary LCT needs revision by PIC'],
                                'permanent_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'Permanent LCT needs revision by PIC'],
                                'taskbudget_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'The LCT task and budget require revision by PIC'],
                                'closed' => ['label' => 'Closed', 'color' => 'bg-green-700', 'tracking' => 'Report has been closed by PIC'],
                            ];

                            // If danger level is Medium or High, adjust specific status colors
                            if (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                                foreach (['waiting_approval_temporary', 'approved_temporary', 'temporary_revision', 
                                        'work_permanent', 'waiting_approval_permanent', 'approved_permanent', 
                                        'permanent_revision'] as $key) {
                                    if (isset($statusMapping[$key])) {
                                        $statusMapping[$key]['color'] = match ($key) {
                                            'approved_temporary', 'approved_permanent' => 'bg-green-500',
                                            'temporary_revision', 'permanent_revision' => 'bg-red-500',
                                            'waiting_approval_temporary', 'waiting_approval_permanent' => 'bg-blue-500',
                                            'work_permanent' => 'bg-yellow-500',
                                            default => $statusMapping[$key]['color'],
                                        };
                                    }
                                }
                            }

                            // Get status from the report data
                            $status = $statusMapping[$laporan->status_lct] ?? [
                                'label' => 'Unknown',
                                'color' => 'bg-gray-400',
                                'tracking' => 'Status not found'
                            ];
                        @endphp

                        <!-- Status Column -->
                        <span class="inline-flex items-center justify-center px-3 py-1 text-[10px] font-semibold text-white rounded-full 
                            {{ $status['color'] }} whitespace-nowrap">
                            {{ $status['label'] }}
                        </span>
                    </td>

                    <!-- Tracking Status Column -->
                    <td>
                        <span class="inline-flex items-center justify-center px-3 py-1 text-black rounded-full whitespace-nowrap">
                            @if ($laporan->status_lct === 'closed' && !empty($laporan->catatan_ehs))
                                {{ $laporan->catatan_ehs }}
                            @else
                                {{ $status['tracking'] }}
                            @endif
                        </span>
                    </td>

                    
                    <!-- Tenggat Waktu -->
                    <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                        @if($laporan->status_lct == 'open')
                            <p>-</p>
                        @elseif($laporan->tingkat_bahaya !== 'Low')
                            {{ \Carbon\Carbon::parse($laporan->due_date)->format('F d, Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($laporan->due_date_temp)->format('F d, Y') }}
                        @endif
                    </td>

                    <!-- Tenggat Waktu -->
                    <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                        @if($laporan->status_lct == 'open')
                            <p>-</p>
                        @elseif($laporan->tingkat_bahaya !== 'Low')
                            {{ \Carbon\Carbon::parse($laporan->due_date_perm)->format('F d, Y') }}
                        @else
                            <p>-</p>
                        @endif
                    </td>

                    <!-- Tanggal Selesai / Overdue -->
                    <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
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


                    <!-- Tombol Aksi -->
                    @php
                        $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
                        $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');
                    @endphp

                    <td class="px-4 py-3 flex items-center gap-2 w-28">
                        <a href="{{ route($roleName === 'ehs' ? 'ehs.progress-perbaikan.show' : 'admin.progress-perbaikan.show', $laporan->id_laporan_lct) }}"
                            class="text-blue-600 hover:underline">
                            Detail
                        </a>
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
    
    

    <div class="mt-6 flex justify-between items-center border-t px-5 py-3">
        <span class="text-sm text-gray-600">
            Showing {{ $laporans->firstItem() }} to {{ $laporans->lastItem() }} of {{ $laporans->total() }} entries
        </span>
        <div>
            {{ $laporans->links('pagination::tailwind') }}
        </div>
    </div>
</div>
