<x-app-layout class="overflow-y-auto">
    <section class="p-6 relative">
        <div class="max-w-7xl mx-auto bg-white shadow-md rounded-xl p-8 space-y-10">
            <!-- Header -->
            <div class="flex items-center gap-4 border-b pb-4">
                <div class="w-14 h-14 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 text-xl font-bold">
                    {{ substr($taskBudget->picUser->fullname, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">LCT Activity Overview</h2>
                    <p class="text-sm text-gray-500">Submitted on {{ $taskBudget->created_at->format('F j, Y') }}</p>
                </div>
            </div>
    
            <!-- Highlight Info -->
            <div class="grid md:grid-cols-3 sm:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm text-gray-500">Finding Date</h3>
                    <p class="text-lg text-gray-900 font-semibold">{{ $taskBudget->tanggal_temuan }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Area</h3>
                    <p class="text-lg text-gray-900 font-semibold">{{ $taskBudget->area }} - {{ $taskBudget->detail_area }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Due Date</h3>
                    <p class="text-lg text-gray-900 font-semibold">{{ $taskBudget->due_date }}</p>
                </div>
            </div>
    
            <!-- Finding Content -->
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm text-gray-500">Finding Report</h3>
                    <p class="text-base text-gray-800 font-medium">{{ $taskBudget->temuan_ketidaksesuaian }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Corrective Action</h3>
                    <p class="text-base text-gray-800 font-medium">{{ $taskBudget->tindakan_perbaikan }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Finding Evidence</h3>
                    <p class="text-base text-gray-800 font-medium">{{ $taskBudget->bukti_temuan }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Repair Evidence</h3>
                    <p class="text-base text-gray-800 font-medium">{{ $taskBudget->bukti_perbaikan }}</p>
                </div>
            </div>
    
            <!-- Miscellaneous Info -->
            <div class="grid md:grid-cols-3 sm:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm text-gray-500">Completion Date</h3>
                    <p class="text-base text-gray-800 font-medium">{{ $taskBudget->date_completion ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Hazard Level</h3>
                    <span class="inline-block mt-1 px-3 py-1 text-sm font-medium text-white rounded-full 
                        {{ $taskBudget->tingkat_bahaya === 'High' ? 'bg-red-600' : 'bg-yellow-500' }}">
                        {{ ucfirst($taskBudget->tingkat_bahaya) }}
                    </span>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Reported By</h3>
                    <p class="text-base text-gray-800 font-medium">{{ $taskBudget->picUser->fullname }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Estimated Budget</h3>
                    <p class="text-base font-semibold text-gray-800">Rp {{ number_format($taskBudget->estimated_budget, 0, ',', '.') }}</p>
                </div>
                <div>
                    <h3 class="text-sm text-gray-500">Approval Status</h3>
                    <span class="inline-block mt-1 px-4 py-1 text-sm font-medium text-white rounded-full
                        {{ $taskBudget->status_lct == 'approved_taskbudget' ? 'bg-green-600' : 
                            ($taskBudget->status_lct == 'taskbudget_revision' ? 'bg-red-500' : 'bg-gray-500') }}">
                        {{
                            match($taskBudget->status_lct) {
                                'approved_taskbudget' => 'Approved',
                                'taskbudget_revision' => 'Revision Required',
                                'waiting_approval_taskbudget' => 'Awaiting Approval',
                                default => ucfirst(str_replace('_', ' ', $taskBudget->status_lct)),
                            }
                        }}
                    </span>
                </div>
            </div>
    
            @if ($taskBudget->manager_notes)
                <div>
                    <h3 class="text-sm text-gray-500">Manager's Notes</h3>
                    <p class="text-base text-gray-800 font-medium mt-1">{{ $taskBudget->manager_notes }}</p>
                </div>
            @endif
    
            <!-- Task List -->
            <div class="bg-gray-50 rounded-lg p-6 shadow">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Task List</h3>
                @if ($taskBudget->tasks->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-gray-300 rounded-lg">
                            <thead class="bg-gray-200 text-gray-700">
                                <tr>
                                    <th class="text-left px-4 py-2">No</th>
                                    <th class="text-left px-4 py-2">Task Name</th>
                                    <th class="text-left px-4 py-2">SVP</th>
                                    <th class="text-left px-4 py-2">Due Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white text-gray-800">
                                @foreach ($taskBudget->tasks as $index => $task)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2">{{ $task->task_name ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $task->pic->user->fullname ?? 'Unassigned' }}</td>
                                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($task->due_date)->translatedFormat('d F Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 italic">No tasks assigned yet.</p>
                @endif
            </div>

            <div x-data="fileUpload" class="mt-6 p-4 border rounded-lg shadow-md bg-white">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Attachments</h3>
                
                <!-- Existing Attachments -->
                @php
                    $existingAttachments = json_decode($taskBudget->attachments ?? '[]', true);
                @endphp
                
                @if (!empty($existingAttachments))
                <div class="mb-6">
                    <p class="text-sm font-medium text-gray-700 mb-2">Submitted Documents</p>
                    <ul class="list-disc pl-5 text-sm text-gray-600 space-y-2">
                        @foreach ($existingAttachments as $index => $attachment)
                            <li class="flex items-center justify-between">
                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="text-blue-600 underline hover:text-blue-800">
                                    {{ $attachment['original_name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>                    
                @else
                    <p class="text-sm text-gray-500 mb-4">No files uploaded yet.</p>
                @endif
                
                <!-- Display selected file names -->
                <div x-show="selectedFiles.length > 0" class="mt-4">
                    <ul class="list-disc pl-5 text-sm text-gray-600 space-y-1">
                        <template x-for="(file, index) in selectedFiles" :key="index">
                            <li x-text="file.name"></li>
                        </template>
                    </ul>
                </div>
                
            </div>
    
            <!-- Approval Actions -->
            @if($taskBudget->status_lct === 'waiting_approval_taskbudget')
                <div class="flex flex-col sm:flex-row justify-end gap-4">
                    <form method="POST" action="{{ route('admin.budget-approval.approve', $taskBudget->id_laporan_lct) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm cursor-pointer">
                            Approve
                        </button>
                    </form>
                    <button id="rejectBtn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm cursor-pointer">
                        Revision
                    </button>
                </div>
    
                <!-- Reject Form -->
                <form method="POST" id="rejectForm" class="hidden mt-4 space-y-2">
                    @csrf
                    <textarea name="alasan_reject" rows="3" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500" placeholder="Reason for revision..." required></textarea>
                    <button type="submit" formaction="{{ route('admin.budget-approval.reject', $taskBudget->id_laporan_lct) }}" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                        Submit
                    </button>
                </form>
            @endif
    
            <a href="{{ route('admin.budget-approval.history', $taskBudget->id_laporan_lct) }}" class="inline-block">
                <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                    <i class="fas fa-history mr-2"></i>History
                </button>
            </a>
        </div>
    </section>
    
    
    <script>
        const rejectBtn = document.getElementById('rejectBtn');
        const rejectForm = document.getElementById('rejectForm');
        rejectBtn?.addEventListener('click', () => {
            rejectForm.classList.toggle('hidden');
        });
    </script>
</x-app-layout>
