<x-app-layout class="overflow-y-auto">
    <section class="p-5 relative">
        {{-- <!-- Back Button -->
        <div class="mb-4 absolute top-5 left-5">
            <a href="{{ route('admin.budget-approval')}}" class="flex items-center text-gray-800 hover:underline text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
        </div> --}}

        <div class=" mx-auto bg-white shadow-md rounded-xl p-6 space-y-6">
            <!-- Header -->
            <div class="flex items-center space-x-4 border-b pb-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-lg font-semibold">
                        {{ substr($taskBudget->picUser->fullname, 0, 1) }}
                    </div>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Activity Approval Detail</h2>
                    <p class="text-sm text-gray-600">
                        Submitted on {{ $taskBudget->created_at->format('F j, Y') }}
                    </p>
                </div>
            </div>
        
            <!-- Budget Details -->
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Nonconformity Findings</h3>
                    <p class="text-gray-600">{{ $taskBudget->temuan_ketidaksesuaian }}</p>
                </div>
        
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Risk Level</h3>
                        <span class="inline-block px-3 py-1 text-sm font-medium text-white rounded-lg
                            {{ $taskBudget->tingkat_bahaya == 'High' ? 'bg-red-500' : 'bg-yellow-500' }}">
                            {{ ucfirst($taskBudget->tingkat_bahaya) }}
                        </span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Submitted by</h3>
                        <p class="text-gray-600">{{ $taskBudget->picUser->fullname }}</p>
                    </div>
                </div>
        
                <div class="grid grid-cols-2 gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Budget Amount</h3>
                    <p class="text-gray-900 font-medium">Rp {{ number_format($taskBudget->estimated_budget, 0, ',', '.') }}</p>
                </div>
        
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Approval Status</h3>
                    <span class="inline-block px-4 py-2 text-sm font-medium text-white rounded-lg
                        {{ $taskBudget->status_lct == 'approved_taskbudget' ? 'bg-green-500' : ($taskBudget->status_lct == 'taskbudget_revision' ? 'bg-red-500' : 'bg-gray-500') }}">
                        {{ ucfirst(str_replace('_', ' ', $taskBudget->status_lct)) }}
                    </span>
        
                    @if ($taskBudget->manager_notes)
                        <div class="mt-4">
                            <h3 class="text-lg font-semibold text-gray-800">Manager Notes</h3>
                            <p class="text-gray-600">{{ $taskBudget->manager_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
            </div>
        
            <!-- Task List -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Task List</h2>
        
                @if ($taskBudget && $taskBudget->tasks->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-sm">
                            <thead class="bg-gray-200 text-gray-700">
                                <tr>
                                    {{-- <th class="px-4 py-3 text-left">#</th> --}}
                                    <th class="px-4 py-3 text-left">Task Name</th>
                                    <th class="px-4 py-3 text-left">PIC Name</th>
                                    <th class="px-4 py-3 text-left">Due Date</th>
                                    <th class="px-4 py-3 text-left">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @foreach ($taskBudget->tasks as $index => $task)
                                    <tr class="border-t">
                                        <td class="px-4 py-3">{{ $task->task_name ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $task->name_pic ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $task->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>                            
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">No tasks available.</p>
                @endif
            </div>
        
            <!-- Approval Buttons -->
            @if($taskBudget->status_budget !== 'approved')
                <div class="flex justify-end space-x-4">
                    <form method="POST">
                        @csrf
                        <button type="submit" formaction="{{ route('admin.budget-approval.approve', $taskBudget->id) }}" 
                            class="cursor-pointer px-4 py-2 bg-green-500 text-white rounded-lg text-sm hover:bg-green-600 transition">
                            Approve
                        </button>
                    </form>
        
                    <button type="button" id="rejectBtn" 
                        class="cursor-pointer px-4 py-2 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600 transition">
                        Reject
                    </button>
                </div>
        
                <!-- Reject Reason Form (Hidden by Default) -->
                <form method="POST" id="rejectForm" class="hidden mt-4 space-y-2">
                    @csrf
                    <input type="hidden" name="budget_id" value="{{ $taskBudget->id }}">
                    <textarea name="alasan_reject" rows="3" 
                        class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                        placeholder="Masukkan alasan penolakan..." required></textarea>
        
                    <button type="submit" formaction="{{ route('admin.budget-approval.reject', $taskBudget->id_laporan_lct) }}" 
                        class="px-4 py-2 bg-red-500 text-white rounded-lg text-sm hover:bg-red-600 transition cursor-pointer">
                        Submit Reject
                    </button>
                </form>
            @endif
        
            <!-- Reject History -->
            <div class="bg-white shadow-md rounded-lg p-6 mt-5">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Reject History</h3>
                <div class="space-y-4">
                    @foreach ($taskBudget->rejects as $reject)
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm border-l-4 border-red-500">
                            <p class="text-sm text-gray-600">
                                <strong class="text-gray-800">Revision on:</strong> 
                                <span class="font-medium">{{ $reject->created_at->format('F j, Y') }} at {{ $reject->created_at->format('h:i A') }}</span>
                            </p>
                            <p class="text-sm text-gray-600">
                                <strong class="text-gray-800">Reason:</strong> 
                                <span class="font-medium">{{ $reject->alasan_reject }}</span>
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
    </section>

    <script>
        document.getElementById('rejectBtn').addEventListener('click', function() {
            document.getElementById('rejectForm').classList.toggle('hidden');
        });
    </script>
</x-app-layout>