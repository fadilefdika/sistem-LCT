<div class="bg-white p-6 shadow-sm rounded-xl">

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-sm font-semibold text-gray-600">
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
                    <tr class="hover:bg-gray-100 transition duration-200 ease-in-out border-b bg-white">
                        <td class="px-4 py-4 text-sm text-gray-800">{{ $index + 1 }}</td>
                        <td class="px-4 py-4 text-sm text-gray-800">{{ $budget->picUser->fullname ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-800">{{$budget->tingkat_bahaya}}</td>
                        <td class="px-4 py-4 text-gray-900 font-medium">
                            Rp {{ number_format($budget->estimated_budget, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($budget->tasks->isNotEmpty())
                                {{ \Carbon\Carbon::parse($budget->tasks->first()->created_at)->locale('en')->translatedFormat('F j, Y') }}
                            @else
                                -
                            @endif
                        </td>                      
                        @php
                            $statusMapping = [
                                'waiting_approval_taskbudget' => 'Waiting for Activity Approval',
                                'taskbudget_revision' => 'The Task and Budget Require Revision by PIC.',
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

                        <td class="px-4 py-4 text-sm">
                            <p class="truncate block max-w-xs font-medium px-2 py-1 rounded {{ $bgClass }}">
                                {{ $statusLabel }}
                            </p>
                        </td>


                        <td class="px-4 py-4 text-center">
                            <a href="{{ route('admin.budget-approval.show', $budget->id_laporan_lct) }}" 
                                class="text-blue-500 hover:text-blue-700 font-medium hover:underline ">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6"></path>
                                </svg>
                                <p class="text-sm">No budget request data available.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
    </div>

    <div class="mt-6 flex justify-between items-center border-t px-5 py-3">
        <span class="text-sm text-gray-600">
            Showing {{ $taskBudget->firstItem() }} to {{ $taskBudget->lastItem() }} of {{ $taskBudget->total() }} entries
        </span>
        <div>
            {{ $taskBudget->links('pagination::tailwind') }}
        </div>
    </div>
</div>
