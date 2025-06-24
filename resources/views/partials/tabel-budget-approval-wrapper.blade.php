<div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <label for="perPageSelect" class="sr-only">Items per page</label>
                    <select id="perPageSelect" class="appearance-none bg-white border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="10" @if(request('perPage') == 10) selected @endif>10 per page</option>
                        <option value="15" @if(request('perPage') == 15) selected @endif>15 per page</option>
                        <option value="25" @if(request('perPage') == 25) selected @endif>25 per page</option>
                        <option value="50" @if(request('perPage') == 50) selected @endif>50 per page</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">PIC</th> 
                        <th class="px-4 py-3">Hazard Level</th> 
                        <th class="px-4 py-3">Total Amount</th> 
                        <th class="px-4 py-3">Submission Date</th> 
                        <th class="px-4 py-3">Status</th> 
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium">
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
    </div>

    <div class="flex items-center justify-between mt-6 px-2">
        <!-- Left side: Showing info -->
        <div class="text-sm text-gray-600">
            Showing
            <span class="font-semibold">{{ $taskBudget->firstItem() }}</span>
            to
            <span class="font-semibold">{{ $taskBudget->lastItem() }}</span>
            of
            <span class="font-semibold">{{ $taskBudget->total() }}</span>
            results
        </div>

        <!-- Right side: Pagination -->
        <div id="pagination-links" class="flex space-x-2">
            {{ $taskBudget->withQueryString()->links() }}
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const perPageSelect = document.getElementById('perPageSelect');
        const wrapper = document.getElementById('budget-table-container');
    
        perPageSelect.addEventListener('change', () => {
            const perPage = perPageSelect.value;
            fetch(`{{ route('admin.budget-approval.index') }}?perPage=${perPage}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                wrapper.innerHTML = html;
            });
        });
        
        $(document).on('click', '#pagination-links a', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            fetchData(url);
        });

    });
</script>