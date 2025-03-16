<div class="overflow-x-auto bg-white p-6 shadow-sm rounded-xl">
    
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-300 shadow-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr class="text-left text-sm font-semibold text-gray-700">
                    <th scope="col" class="px-4 py-3">No</th>
                    <th scope="col" class="px-4 py-3">Non-Conformity Findings</th>
                    <th scope="col" class="px-4 py-3">Area Details</th>
                    <th scope="col" class="px-4 py-3">Risk Level</th>
                    <th scope="col" class="px-4 py-3">Due Date</th>
                    <th scope="col" class="px-4 py-3">Progress Status</th>
                    <th scope="col" class="px-4 py-3">Tracking Status</th>
                    <th scope="col" class="px-4 py-3">Completion Date</th>
                    <th scope="col" class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($laporans as $index => $laporan)
                    <tr class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out">
                        <td class="px-6 py-4 text-gray-800">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $laporan->temuan_ketidaksesuaian }}</td>
                        <td class="px-6 py-4 text-gray-800 whitespace-nowrap">{{ $laporan->area }} - {{ $laporan->detail_area }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-white text-xs font-semibold
                                {{ $laporan->tingkat_bahaya === 'High' ? 'bg-red-600' : ($laporan->tingkat_bahaya === 'Medium' ? 'bg-yellow-500' : 'bg-green-500') }}">
                                {{ $laporan->tingkat_bahaya }}
                            </span>
                        </td>
                        <!-- Tenggat Waktu -->
                        <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($laporan->due_date)->format('F d, Y') }}
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
                                'approved' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'LCT Low has been approved by PIC'],
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
                            {{ $status['tracking'] }}
                        </span>
                    </td>
                        
                        <!-- Completion Date -->
                        <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                            {{ $laporan->date_completion ? \Carbon\Carbon::parse($laporan->date_completion)->format('F d, Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.manajemen-lct.show', $laporan->id_laporan_lct) }}" class="text-blue-600 hover:underline font-medium">
                                View Details
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
