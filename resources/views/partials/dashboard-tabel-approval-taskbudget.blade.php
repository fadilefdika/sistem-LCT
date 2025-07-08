<div class="overflow-x-auto rounded-lg border border-gray-300 shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-xs">
        <thead class="bg-gray-100 text-gray-700 uppercase tracking-wide">
            <tr>
                <th class="px-4 py-2 text-left">No</th>
                <th class="px-4 py-2 text-left">Report Number</th>
                <th class="px-4 py-2 text-left">Area</th>
                <th class="px-4 py-2 text-left">Hazard Level</th>
                <th class="px-4 py-2 text-left">Submission Date</th>
                <th class="px-4 py-2 text-left">Budget Status</th>
                <th class="px-4 py-2 text-center">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
            @forelse($laporans as $index => $budget)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-2 text-gray-800">{{ $index + 1 }}</td>
                    <td class="px-4 py-2 text-gray-800">{{ $budget->id_laporan_lct }}</td>
                    <td class="px-4 py-2 text-gray-900 font-semibold">
                        {{ $budget->area->nama_area ?? '-' }}
                    </td>
                    <td class="px-4 py-2 text-gray-800">{{ $budget->tingkat_bahaya }}</td>
                    <td class="px-4 py-2 text-gray-800">
                        @if($budget->tasks->isNotEmpty())
                            {{ \Carbon\Carbon::parse($budget->tasks->first()->created_at)->locale('en')->translatedFormat('M d, Y') }}
                        @else
                            -
                        @endif
                    </td>
                    @php
                        $statusMapping = [
                            'waiting_approval_taskbudget' => 'Waiting Approval',
                            'taskbudget_revision' => 'Revision Needed',
                            'approved_taskbudget' => 'Budget Approved',
                            'work_permanent' => 'Budget Approved',
                            'waiting_approval_permanent' => 'Budget Approved',
                            'permanent_revision' => 'Budget Approved',
                            'approved_permanent' => 'Budget Approved',
                        ];

                        $statusLabel = $statusMapping[$budget->status_lct] ?? ucfirst(str_replace('_', ' ', $budget->status_lct));
                        $bgClass = match ($budget->status_lct) {
                            'waiting_approval_taskbudget' => 'bg-red-100 text-red-700',
                            'taskbudget_revision' => 'bg-yellow-100 text-yellow-800',
                            'approved_taskbudget', 'work_permanent', 'waiting_approval_permanent', 'permanent_revision', 'approved_permanent' => 'bg-green-100 text-green-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <td class="px-4 py-2">
                        <span class="inline-block px-2 py-1 rounded text-[11px] font-medium {{ $bgClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-center">
                        <a href="{{ route('admin.budget-approval.show', $budget->id_laporan_lct) }}"
                           class="text-blue-600 hover:underline font-medium">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6"></path>
                            </svg>
                            <p class="text-xs">No budget request need to approve.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
