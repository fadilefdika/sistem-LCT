<x-app-layout class="overflow-y-auto">
    <section class="p-6 relative">
        <div class="w-full bg-white shadow-lg rounded-xl overflow-hidden">
            <!-- Header Section with Status Highlight -->
            <div class="p-6 relative">
                <!-- Hazard Level Badge - Positioned Top Right -->
                <div class="absolute top-4 right-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        {{ $taskBudget->tingkat_bahaya === 'High' ? 'bg-red-100 text-red-700' :
                        ($taskBudget->tingkat_bahaya === 'Medium' ? 'bg-yellow-100 text-yellow-800' :
                        'bg-green-100 text-green-700') }}">
                        {{ ucfirst($taskBudget->tingkat_bahaya) }}
                    </span>
                </div>

                <!-- Header -->
                <div class="flex items-center justify-between border-b pb-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">LCT Activity Overview</h2>
                        <p class="text-xs text-gray-500">Submitted on {{ $taskBudget->created_at->format('F j, Y') }}</p>
                    </div>
                </div>
            </div>


            <!-- Main Content Container -->
            <div class="px-6 pt-2 pb-2 space-y-8">

                 <!-- Corrective Action History -->
                @php
                    $taskBudgets = collect(json_decode($taskBudget->tindakan_perbaikan ?? '[]'))->sortByDesc('tanggal');
                    $totalRevisions = $taskBudgets->count();
                @endphp

                

                <!-- TEMPORARY ACTION SECTION -->
                <div class="bg-white border border-gray-300 rounded-lg shadow-sm p-6 space-y-6">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-800 border-b pb-2">Temporary Action</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- LEFT: Information Section -->
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 tracking-wider">Finding Date</h4>
                                <p class="text-xs font-medium text-gray-800">{{ \Carbon\Carbon::parse($taskBudget->tanggal_temuan)->format('F j, Y') }}</p>
                            </div>
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 tracking-wider">Due Date (Temporary)</h4>
                                <p class="text-xs font-medium text-gray-800">{{ \Carbon\Carbon::parse($taskBudget->due_date_temp)->format('F j, Y') }}</p>
                                @if($taskBudget->approved_temporary_by_ehs =='pending' && in_array($taskBudget->status_lct, ['waiting_approval_temporary', 'waiting_approval_taskbudget','taskbudget_revision','approved_taskbudget','temporary_revision','work_permanent']))
                                    <span class="italic text-xs text-gray-500">(Awaiting approval from EHS)</span>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 tracking-wider">Area</h4>
                                <p class="text-xs font-medium text-gray-800">{{ $taskBudget->area->nama_area }} - <span class="text-gray-600">{{ $taskBudget->detail_area }}</span></p>
                            </div>
                        </div>

                        <!-- RIGHT: Photo Section -->
                        <div class="space-y-4">
                            <!-- Finding Photo -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-600 mb-3">Finding Photos</h4>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach ($bukti_temuan as $image)
                                        <a href="{{ $image }}" target="_blank" class="block">
                                            <img src="{{ $image }}" alt="Finding" loading="lazy"
                                                class="w-full h-16 object-cover rounded-lg border hover:shadow-md transition-shadow" />
                                        </a>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Corrective Photo -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-600 mb-3">Corrective Action Photos</h4>
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach ($taskBudgets as $item)
                                        @if (!empty($item->bukti) && is_array($item->bukti))
                                            @foreach ($item->bukti as $image)
                                                <a href="{{ Storage::url($image) }}" target="_blank" class="block">
                                                    <img src="{{ Storage::url($image) }}" alt="Corrective" loading="lazy"
                                                        class="w-full h-16 object-cover rounded-lg border hover:shadow-md transition-shadow" />
                                                </a>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Finding Report -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <h3 class="text-xs font-semibold text-red-600 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Finding Report
                        </h3>
                        <p class="text-gray-800 text-xs">{{ $taskBudget->temuan_ketidaksesuaian }}</p>
                    </div>

                    <!-- Corrective Action History -->
                    @php
                        $latest = collect($taskBudgets)->last();
                    @endphp

                    @if($latest)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h3 class="text-xs font-semibold text-green-600 mb-2 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Latest Corrective Action - 
                                <span class="ml-1 text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($latest->tanggal)->setTimezone('Asia/Jakarta')->format('F j, Y H:i') }} WIB
                                </span>                                
                            </h3>
                            <p class="text-gray-800 whitespace-pre-line text-xs">{{ $latest->tindakan }}</p>
                        </div>
                    @endif
                </div>



                    <div class="bg-white p-5 rounded-lg border border-gray-300 shadow-sm space-y-6">
                        <div class="space-y-5">
                            <div class="divide-y divide-gray-200 text-sm text-gray-800">
                                <!-- Section Title -->
                                <div class="pb-4">
                                    <h2 class="text-xs font-semibold text-gray-900">
                                        Permanent Corrective Action for Your Approval
                                    </h2>
                                </div>

                                <!-- Permanent Action -->
                                <div class="py-4">
                                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
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
                                    <p class="text-sm font-medium">
                                        Rp {{ number_format($taskBudget->estimated_budget ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Manager's Notes (Conditional) -->
                        @if ($taskBudget->manager_notes)
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                                <h3 class="text-xs font-semibold text-yellow-800 mb-2">Manager's Notes</h3>
                                <p class="text-yellow-700">{{ $taskBudget->manager_notes }}</p>
                            </div>
                        @endif

                        <!-- Attachments Section -->
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center">
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
                                                <span class="text-xs text-gray-700">{{ $attachment['original_name'] }}</span>
                                            </div>
                                            <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                                View
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-500 italic">No files uploaded yet.</p>
                            @endif
                        </div>

                        <!-- Task List Section -->
                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Task List
                            </h3>
                            
                            @if ($taskBudget->tasks->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 table-auto">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10">#</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">Task Name</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider max-w-[180px]">SVP</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider max-w-[160px]">Due Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($taskBudget->tasks as $index => $task)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">{{ $index + 1 }}</td>
                                                    <td class="px-4 py-3 text-xs text-gray-900 break-words">{{ $task->task_name ?? '-' }}</td>
                                                    <td class="px-4 py-3 text-xs text-gray-500 truncate">{{ $task->pic->user->fullname ?? 'Unassigned' }}</td>
                                                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                                                        {{ \Carbon\Carbon::parse($task->due_date)->locale('en')->isoFormat('D MMMM YYYY') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-500 italic">No tasks assigned yet.</p>
                            @endif
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

                        {{-- Revision history --}}
                        @if($revise->isNotEmpty())
                            <div class="mb-4 p-3 border-l-4 rounded-r-md">
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 mt-0.5 mr-2 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/>
                                    </svg>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-yellow-800 mb-1">Revision Required</h4>
                                        <div x-data="{ open: false }" class="text-sm">
                                            <button @click="open = !open" class="text-yellow-700 hover:text-yellow-900 underline">
                                                <span x-text="open ? 'Hide details' : 'View details'"></span>
                                                ({{ $revise->count() }} revision{{ $revise->count() > 1 ? 's' : '' }})
                                            </button>
                                            
                                            <div x-show="open" x-collapse class="mt-2 space-y-2">
                                                @foreach($revise->reverse()->values() as $i => $revision)
                                                    <div class="bg-white p-2 rounded border border-yellow-200">
                                                        <div class="flex justify-between items-center mb-1">
                                                            <span class="text-xs font-medium text-gray-600">#{{ $i + 1 }}</span>
                                                            <span class="text-xs text-gray-500">{{ $revision->updated_at->format('d M Y') }}</span>
                                                        </div>
                                                        <p class="text-xs text-gray-700 leading-relaxed">{{ $revision->alasan_reject }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

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

                        
                    </div>
            </div>
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
