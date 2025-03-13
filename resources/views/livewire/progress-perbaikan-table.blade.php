<div class="bg-white p-6 relative shadow-md rounded-xl overflow-x-auto">
    
    <div class="flex flex-row flex-wrap align-items-center gap-3 p-3 border rounded shadow-sm bg-white mb-4">
        
        <!-- Filter Tingkat Bahaya -->
        <div class="flex flex-col" style="min-width: 180px;">
            <label class="form-label fw-bold text-muted mb-1">Risk Level</label>
            <select wire:model="riskLevel" wire:change="applyFilter" class="form-select">
                @foreach (['' => 'All Risk Level', 'Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'] as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    
        <!-- Filter Status LCT -->
        <div class="flex flex-col" style="min-width: 220px;">
            <label class="form-label fw-bold text-muted mb-1">LCT Status</label>
            <select wire:model="statusLct" wire:change="applyFilter" class="form-select">
                <option value="">All statuses</option>
                <option value="in_progress">In Progress</option>
                <option value="progress_work">Progress Work</option>
                <option value="waiting_approval">Waiting Approval</option>
                <option value="approved">Approved</option>
                <option value="revision">Revision</option>
                <option value="waiting_approval_temporary">Waiting Approval Temporary</option>
                <option value="approved_temporary">Approved Temporary</option>
                <option value="temporary_revision">Temporary Revision</option>
                <option value="work_permanent">Work Permanent</option>
                <option value="waiting_approval_permanent">Waiting Approval Permanent</option>
                <option value="approved_permanent">Approved Permanent</option>
                <option value="permanent_revision">Permanent Revision</option>
            </select>
        </div>
    
        <!-- Tombol Reset -->
            <div class="flex flex-col">
                <button wire:click="resetFilters" class="btn btn-outline-secondary px-3 cursor-pointer shadow mt-[23px] bg-black text-white">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>
            
            <!-- Loading Indicator -->
            <div wire:loading wire:target="riskLevel, statusLct, resetFilters" class="text-sm text-muted mt-8">
                <i class="spinner-border spinner-border-sm"></i> Loading...
            </div>
    </div>

    
    
    
    
    
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-300 shadow-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr class="text-left text-sm font-semibold text-gray-600">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Non-Conformity Findings</th>
                    <th class="px-4 py-3">PIC Name</th>
                    <th class="px-4 py-3">Risk Level</th>
                    <th class="px-4 py-3">Progress Status</th>
                    <th class="px-4 py-3">Due Date</th>
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
                            {{ $laporan->temuan_ketidaksesuaian }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-800 w-40">{{ $laporan->picUser->fullname ?? '-' }}</td>
    
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
                            // Default status color
                            $statusColors = [
                                'open' => 'bg-gray-500',
                                'review' => 'bg-purple-500',
                                'in_progress' => 'bg-gray-500',
                                'progress_work' => 'bg-yellow-500',
                                'waiting_approval' => 'bg-blue-500',
                                'approved' => 'bg-green-500',
                                'closed' => 'bg-green-700',
                                'revision' => 'bg-red-500'
                            ];

                            // Tambahan status untuk Medium & High
                            if ($laporan->tingkat_bahaya === 'Medium' || $laporan->tingkat_bahaya === 'High') {
                                $statusColors = array_merge($statusColors, [
                                    'waiting_approval_temporary' => 'bg-blue-500',
                                    'approved_temporary' => 'bg-green-500',
                                    'temporary_revision' => 'bg-red-500',
                                    'work_permanent' => 'bg-yellow-500',
                                    'waiting_approval_permanent' => 'bg-blue-500',
                                    'approved_permanent' => 'bg-green-500',
                                    'permanent_revision' => 'bg-red-500'
                                ]);
                            }
                        @endphp

                        <span class="inline-flex items-center justify-center px-3 py-1 text-[10px] font-semibold text-white rounded-full
                            {{ $statusColors[$laporan->status_lct] ?? 'bg-gray-400' }} whitespace-nowrap">
                            {{ ucwords(str_replace('_', ' ', $laporan->status_lct)) }}
                        </span>
                    </td>

                    <!-- Tenggat Waktu -->
                    <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->format('F d, Y') }}
                    </td>

                    <!-- Tanggal Selesai / Overdue -->
                    <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                        @if ($laporan->date_completion)
                            {{ \Carbon\Carbon::parse($laporan->date_completion)->format('F d, Y') }}
                        @else
                            @php
                                $dueDate = \Carbon\Carbon::parse($laporan->tenggat_waktu)->startOfDay();
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
                    <td class="px-4 py-3 flex items-center gap-2 w-28">
                        <a href="{{ route('admin.progress-perbaikan.show', $laporan->id_laporan_lct) }}"
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
