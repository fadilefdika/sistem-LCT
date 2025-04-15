<x-app-layout class="overflow-y-auto">
    <section class="p-5 relative">

        <div class="mx-auto bg-white shadow-md rounded-xl p-6 space-y-6">
            <!-- Header -->
            <div class="flex items-center space-x-4 border-b pb-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-lg font-semibold">
                        {{ substr($taskBudget->picUser->fullname, 0, 1) }}
                    </div>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Activity Approval Detail History</h2>
                    <p class="text-sm text-gray-600 flex items-center">
                        Submitted on {{ $taskBudget->created_at->format('F j, Y') }}
                    </p>
                    
                </div>
            </div>
        <!-- Budget Details -->
        <div class="space-y-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Finding Report</h3>
                <p class="text-gray-600">{{ $taskBudget->temuan_ketidaksesuaian }}</p>
            </div>
    
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Hazard Level</h3>
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
                                <th class="px-4 py-3 text-left">No</th>
                                <th class="px-4 py-3 text-left">Task Name</th>
                                <th class="px-4 py-3 text-left">SVP Name</th>
                                <th class="px-4 py-3 text-left">Due Date</th>
                                <th class="px-4 py-3 text-left">Attachment</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            @foreach ($taskBudget->tasks as $index => $task)
                                <tr class="border-t">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td> 
                                    <td class="px-4 py-3">{{ $task->task_name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $task->name_pic ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($task->due_date)->locale('en')->translatedFormat('d F Y') ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if ($task->attachment_path)
                                            <a 
                                                href="{{ asset('storage/' . Str::after($task->attachment_path, 'public/')) }}" 
                                                target="_blank" 
                                                class="text-blue-500 hover:underline"
                                            >
                                                View Attachment
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>                            
                    </table>
                </div>
            @else
                <p class="text-gray-600">No tasks available.</p>
            @endif
        </div>
        <div class="mx-auto bg-white shadow-md rounded-xl p-6 mt-5 space-y-6">
            <!-- Reject History Card -->
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Reject History</h3>
                <div class="space-y-4">
                    @foreach ($taskBudget->rejectLaporan as $reject)
                        <div class="bg-gray-50 p-4 rounded-lg shadow-sm border-l-4 border-red-500">
                            <p class="text-sm text-gray-600">
                                <strong class="text-gray-800">Revision on:</strong> 
                                <span class="font-medium">{{ $reject->created_at->format('F j, Y') }} at {{ $taskBudget->rejectLaporan->first()->created_at->format('h:i A') }}</span>
                            </p>
                            <p class="text-sm text-gray-600">
                                <strong class="text-gray-800">Reason:</strong> 
                                <span class="font-medium">{{ $reject->alasan_reject }}</span>
                            </p>
                        </div>
                    @endforeach
            </div>
        </div>
    </section>

    <script>
        document.getElementById('rejectBtn').addEventListener('click', function() {
            document.getElementById('rejectForm').classList.toggle('hidden');
        });
    </script>
</x-app-layout>