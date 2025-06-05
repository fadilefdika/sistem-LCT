<div class="bg-white p-5 shadow-sm rounded-lg border border-gray-100">

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">PIC</th> 
                    <th class="px-4 py-3">Hazard Level</th> 
                    <th class="px-4 py-3">Total Amount</th> 
                    <th class="px-4 py-3">Submission Date</th> 
                    <th class="px-4 py-3">Budget Status</th> 
                    <th class="px-4 py-3 text-center">Actions</th> 
                </tr>                
            </thead>            
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($taskBudget as $index => $budget)
                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                        <td class="px-4 py-3 text-[11px] text-gray-600">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 text-[11px] text-gray-700 font-medium">{{ $budget->picUser->fullname ?? '-' }}</td>
                        <td class="px-4 py-3 text-[11px] text-gray-600">{{$budget->tingkat_bahaya}}</td>
                        <td class="px-4 py-3 text-[11px] text-gray-700 font-medium">
                            Rp {{ number_format($budget->estimated_budget, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-[11px] text-gray-600">
                            @if($budget->tasks->isNotEmpty())
                                {{ \Carbon\Carbon::parse($budget->tasks->first()->created_at)->locale('en')->translatedFormat('j M Y') }}
                            @else
                                -
                            @endif
                        </td>                      
                        @php
                            $statusMapping = [
                                'waiting_approval_taskbudget' => 'Waiting Approval',
                                'taskbudget_revision' => 'Requires Revision',
                                'approved_taskbudget' => 'Approved',
                                'work_permanent' => 'Approved',
                                'waiting_approval_permanent' => 'Approved',
                                'permanent_revision' => 'Approved',
                                'approved_permanent' => 'Approved',
                            ];

                            $statusLabel = $statusMapping[$budget->status_lct] ?? ucfirst(str_replace('_', ' ', $budget->status_lct));

                            $bgClass = match ($budget->status_lct) {
                                'waiting_approval_taskbudget' => 'bg-red-50 text-red-600',
                                'taskbudget_revision' => 'bg-yellow-50 text-yellow-600',
                                'approved_taskbudget', 'work_permanent', 'waiting_approval_permanent', 'permanent_revision', 'approved_permanent' => 'bg-green-50 text-green-600',
                                default => 'bg-gray-50 text-gray-600',
                            };
                        @endphp

                        <td class="px-4 py-3 text-[11px]">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium {{ $bgClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.budget-approval.show', $budget->id_laporan_lct) }}" 
                                class="text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6"></path>
                                </svg>
                                <p class="text-xs">No budget request data available</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-between items-center border-t border-gray-100 px-4 py-3">
        <span class="text-xs text-gray-500">
            Showing {{ $taskBudget->firstItem() }} to {{ $taskBudget->lastItem() }} of {{ $taskBudget->total() }} entries
        </span>
        <div class="text-xs">
            {{ $taskBudget->links('pagination::tailwind') }}
        </div>
    </div>
</div>