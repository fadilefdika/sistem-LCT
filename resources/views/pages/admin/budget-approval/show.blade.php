<x-app-layout class="overflow-y-auto">
    <section class="p-6 relative">
    <div class="w-full bg-white shadow-lg rounded-xl overflow-hidden">
    <!-- Header Section with Status Highlight -->
    <div class="p-6 text-white">
        <!-- Header -->
        <div class="flex items-center gap-4 border-b pb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">LCT Activity Overview</h2>
                    <p class="text-sm text-gray-500">Submitted on {{ $taskBudget->created_at->format('F j, Y') }}</p>
                </div>
            </div>
    </div>

    <!-- Main Content Container -->
    <div class="p-6 space-y-8">
        <!-- Critical Information Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Hazard Level (Simplified) -->
            <div class="bg-white border border-gray-200 p-4 rounded-lg h-full">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Hazard Level</h3>
                <div class="mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium
                        {{ $taskBudget->tingkat_bahaya === 'High' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($taskBudget->tingkat_bahaya) }}
                    </span>
                </div>
            </div>


            <!-- Dates Section -->
            <div class="bg-white border border-gray-200 p-4 rounded-lg space-y-4 h-full">
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Finding Date</h3>
                    <p class="mt-1 text-base font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($taskBudget->tanggal_temuan)->format('F j, Y') }}
                    </p>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Due Date (Temporary)</h3>
                    <p class="mt-1 text-base font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($taskBudget->due_date_temp)->format('F j, Y') }}
                    </p>
                </div>
            </div>

            <!-- Location Section -->
            <div class="bg-white border border-gray-200 p-4 rounded-lg space-y-4 h-full">
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Area</h3>
                    <div class="flex items-center space-x-2 mt-1">
                        <p class="text-base font-medium text-gray-900">{{ $taskBudget->area->nama_area }}</p>
                        <span class="text-sm text-gray-600">- {{ $taskBudget->detail_area }}</span>
                    </div>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Due Date (Permanent)</h3>
                    <div class="mt-1 inline-block text-black text-base rounded-lg">
                        {{ \Carbon\Carbon::parse($taskBudget->due_date_perm)->format('F j, Y') }}
                        <span class="ml-2 italic text-xs">(Not yet resolved)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finding & Action Section -->
        <div class="space-y-6">
            <!-- Finding Report -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Finding Report
                </h3>
                <p class="text-gray-800">{{ $taskBudget->temuan_ketidaksesuaian }}</p>
            </div>

            <!-- Corrective Action History -->
            @php
                $taskBudgets = collect(json_decode($taskBudget->tindakan_perbaikan ?? '[]'))->sortByDesc('tanggal');
                $totalRevisions = $taskBudgets->count();
            @endphp

            @foreach ($taskBudgets as $index => $item)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Corrective Action (Revision {{ $totalRevisions - $loop->index }}) - 
                        <span class="ml-1 text-xs text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal)->timezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</span>
                    </h3>
                    <p class="text-gray-800 whitespace-pre-line">{{ $item->tindakan }}</p>
                </div>
            @endforeach
        </div>


        <!-- Evidence Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Finding Evidence -->
            <div x-data="{ showAll: false }">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Finding Photo</h3>
                <div class="grid grid-cols-3 gap-2">
                    @foreach ($bukti_temuan as $index => $image)
                        <template x-if="showAll || {{ $index }} < 5">
                            <a href="{{ $image }}" target="_blank" class="group">
                                <img src="{{ $image }}" alt="Finding Evidence"
                                    class="w-full h-24 object-cover rounded border border-gray-200 group-hover:border-blue-500 transition-colors" />
                            </a>
                        </template>
                    @endforeach
                </div>
                @if (count($bukti_temuan) > 3)
                    <button @click="showAll = !showAll" class="mt-2 text-blue-600 text-sm hover:underline focus:outline-none">
                        <span x-text="showAll ? 'Show Less' : 'Show More'"></span>
                    </button>
                @endif
            </div>

            <!-- Repair Evidence -->
            <div x-data="{ showAll: false }">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Corrective Action (Photo)</h3>
                <div class="grid grid-cols-3 gap-2">
                    @foreach ($taskBudgets as $item)
                        @if(!empty($item->bukti) && is_array($item->bukti))
                            @foreach ($item->bukti as $index => $image)
                                <template x-if="showAll || {{ $index }} < 5">
                                    <a href="{{ Storage::url($image) }}" target="_blank" class="group">
                                        <img src="{{ Storage::url($image) }}" alt="Repair Evidence"
                                            class="w-full h-24 object-cover rounded border border-gray-200 group-hover:border-blue-500 transition-colors" />
                                    </a>
                                </template>
                            @endforeach
                        @endif
                    @endforeach
                </div>
                @php
                    $totalImages = collect($taskBudgets)->reduce(fn($carry, $item) => $carry + (is_array($item->bukti) ? count($item->bukti) : 0), 0);
                @endphp
                @if ($totalImages > 3)
                    <button @click="showAll = !showAll" class="mt-2 text-blue-600 text-sm hover:underline focus:outline-none">
                        <span x-text="showAll ? 'Show Less' : 'Show More'"></span>
                    </button>
                @endif
            </div>

        </div>


            <div class="bg-white p-5 rounded-lg border border-gray-300 shadow-sm space-y-6">
                    <div class="space-y-6">
                        <div class="divide-y divide-gray-200 text-sm text-gray-800">
                            <!-- Section Title -->
                            <div class="pb-4">
                                <h2 class="text-lg font-semibold text-gray-900">
                                    Permanent Corrective Action for Your Approval
                                </h2>
                            </div>

                            <!-- Permanent Action -->
                            <div class="py-4">
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                    Permanent Action Description
                                </h3>
                                <div class="whitespace-pre-line leading-relaxed">
                                    {{ $taskBudget->action_permanent ?? '-' }}
                                </div>
                            </div>

                            <!-- Estimated Budget -->
                            <div class="py-4">
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                    Estimated Budget
                                </h3>
                                <p class="text-base font-medium">
                                    Rp {{ number_format($taskBudget->estimated_budget ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                <!-- Manager's Notes (Conditional) -->
                @if ($taskBudget->manager_notes)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                    <h3 class="text-sm font-semibold text-yellow-800 mb-2">Manager's Notes</h3>
                    <p class="text-yellow-700">{{ $taskBudget->manager_notes }}</p>
                </div>
                @endif

                <!-- Attachments Section -->
                <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Attachments
                    </h3>
                    
                    @php
                        $existingAttachments = json_decode($taskBudget->attachments ?? '[]', true);
                    @endphp
                    
                    @if (!empty($existingAttachments))
                        <div class="space-y-2">
                            @foreach ($existingAttachments as $index => $attachment)
                                <div class="flex items-center justify-between p-3 bg-white rounded border border-gray-200 hover:bg-gray-50">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ $attachment['original_name'] }}</span>
                                    </div>
                                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">No files uploaded yet.</p>
                    @endif
                </div>

                <!-- Task List Section -->
                <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Task List
                    </h3>
                    
                    @if ($taskBudget->tasks->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SVP</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($taskBudget->tasks as $index => $task)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $task->task_name ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ $task->pic->user->fullname ?? 'Unassigned' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($task->due_date)->translatedFormat('d F Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 italic">No tasks assigned yet.</p>
                    @endif
                </div>

                <!-- History Card -->
                <div class="bg-white rounded-lg shadow-md p-4 mt-4 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Corrective Action History</h2>
                            <p class="text-sm text-gray-500">View the detailed progress and corrective actions taken for this case.</p>
                        </div>
                        <a href="{{ route('admin.budget-approval.history', $taskBudget->id_laporan_lct) }}">
                            <button class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50 cursor-pointer">
                                <i class="fas fa-history mr-2"></i>View History
                            </button>
                        </a>
                    </div>
                </div> 

                <!-- Approval Actions -->
                @if($taskBudget->status_lct === 'waiting_approval_taskbudget')
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h3 class="text-sm font-semibold text-blue-800 mb-3">Approval Actions</h3>
                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <form method="POST" action="{{ route('admin.budget-approval.approve', $taskBudget->id_laporan_lct) }}" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-700 text-sm font-medium flex items-center justify-center cursor-pointer">
                                Approve
                            </button>
                        </form>
                        
                        <button id="rejectBtn" class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium flex items-center justify-center cursor-pointer">
                            Revise
                        </button>
                    </div>

                    <!-- Reject Form (Hidden Initially) -->
                    <form method="POST" id="rejectForm" class="hidden mt-4 space-y-3">
                        @csrf
                        <label for="alasan_reject" class="block text-sm font-medium text-gray-700">Revise Reason</label>
                        <textarea name="alasan_reject" id="alasan_reject" rows="3" class="w-full p-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Please specify the reason for Revise..." required></textarea>
                        <div class="flex justify-end">
                            <button type="submit" formaction="{{ route('admin.budget-approval.reject', $taskBudget->id_laporan_lct) }}" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium cursor-pointer">
                                Submit Revise
                            </button>
                        </div>
                    </form>
                </div>
                @endif
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
