<x-app-layout class="overflow-y-auto">
    @php
            $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
            $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');
                  
            if ($roleName === 'ehs') {
                $routeName = 'ehs.reporting.index';
            } elseif ($roleName === 'pic') {
                $routeName = 'admin.manajemen-lct.index';
            } else {
                $routeName = 'admin.budget-approval.index';
            }
        @endphp
    <section class="p-6 relative">
        <div class="flex justify-end mb-4">
            <a href="{{ route($routeName) }}"
                class="inline-flex items-center px-3 py-1 sm:px-4 sm:py-1.5 text-xs sm:text-sm bg-blue-500 border border-blue-500 rounded-md font-medium text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                Back
            </a>
        </div>
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
            <div class="px-6 pb-2 space-y-8">

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

                <div class="bg-white p-4 sm:p-5 rounded-lg border border-gray-200 shadow-sm space-y-4">
                    <div class="space-y-4">
                        <div class="divide-y divide-gray-200 text-sm text-gray-800">
                            <!-- Section Title -->
                            <div class="pb-3">
                                <h2 class="text-sm font-semibold text-gray-900">
                                    Permanent Corrective Action for Your Approval
                                </h2>
                            </div>
                
                            <!-- Permanent Action -->
                            <div class="py-3">
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Permanent Action Description
                                </h3>
                                <div class="whitespace-pre-line leading-relaxed mt-1">
                                    {{ $taskBudget->action_permanent ?? '-' }}
                                </div>
                            </div>
                
                            <!-- Estimated Budget -->
                            <div class="py-3">
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Estimated Budget
                                </h3>
                                <p class="text-sm font-medium mt-1">
                                    Rp {{ number_format($taskBudget->estimated_budget ?? 0, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                
                    <!-- Manager's Notes (Conditional) -->
                    @if ($taskBudget->manager_notes)
                        <div class="bg-amber-50 border-l-4 border-amber-400 p-3 rounded-r-lg">
                            <h3 class="text-xs font-semibold text-amber-800 mb-1">Manager's Notes</h3>
                            <p class="text-amber-700 text-sm">{{ $taskBudget->manager_notes }}</p>
                        </div>
                    @endif
                
                    <!-- Attachments Section -->
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Attachments
                        </h3>
                    
                        @php
                            $existingAttachments = json_decode($taskBudget->attachments ?? '[]', true);
                        @endphp
                    
                        @if (!empty($existingAttachments))
                            <div class="space-y-1">
                                @foreach ($existingAttachments as $attachment)
                                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank"
                                       class="flex items-center gap-2 p-2 rounded hover:bg-gray-100 transition text-sm text-blue-600">
                                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="truncate">{{ $attachment['original_name'] }}</span>
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-500 italic">No files uploaded yet.</p>
                        @endif
                    </div>
                    
                    <!-- Task List Section -->
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Task List
                        </h3>
                    
                        @if ($taskBudget->tasks->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 table-auto">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">#</th>
                                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task Name</th>
                                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">PIC</th>
                                            <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Due Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($taskBudget->tasks as $index => $task)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-2 py-2 text-xs text-gray-500">{{ $index + 1 }}</td>
                                                <td class="px-2 py-2 text-xs text-gray-900 break-words w-full">{{ $task->task_name ?? '-' }}</td>
                                                <td class="px-2 py-2 text-xs text-gray-500 whitespace-nowrap">
                                                    {{ $task->pic->user->fullname ?? 'Unassigned' }}
                                                </td>                                                                                                                                           
                                                <td class="px-2 py-2 text-xs text-gray-500 whitespace-nowrap text-right">
                                                    {{ \Carbon\Carbon::parse($task->due_date)->locale('en')->isoFormat('D MMM YYYY') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-xs text-gray-500 italic">No tasks assigned yet.</p>
                        @endif
                    </div>
                
                    <!-- Approval Actions -->
                    @if($taskBudget->status_lct === 'waiting_approval_taskbudget')
                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                            <h3 class="text-sm font-semibold text-blue-800 mb-2">Approval Actions</h3>
                            <div class="flex flex-col sm:flex-row justify-end gap-2">
                            <form method="POST" action="{{ route('admin.budget-approval.approve', $taskBudget->id_laporan_lct) }}" class="w-full sm:w-auto">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium flex items-center justify-center cursor-pointer">
                                        Approve
                                    </button>
                                </form>
                                
                                <button id="rejectBtn" class="w-full sm:w-auto px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium flex items-center justify-center cursor-pointer">
                                    Revise
                                </button>
                            </div>
                
                            <!-- Reject Form (Hidden Initially) -->
                            <form method="POST" id="rejectForm" class="hidden mt-3 space-y-2">
                                @csrf
                                <label for="alasan_reject" class="block text-xs font-medium text-gray-700">Revise Reason</label>
                                <textarea name="alasan_reject" id="alasan_reject" rows="3" class="w-full p-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Please specify the reason for Revise..." required></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" formaction="{{ route('admin.budget-approval.reject', $taskBudget->id_laporan_lct) }}" class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium cursor-pointer">
                                        Submit Revise
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                
                    @if ($taskBudget->status_lct === 'taskbudget_revision' && $budgetApprovalRejects->isNotEmpty()|| $taskBudget->tindakan_perbaikan && $budgetApprovalRejects->isNotEmpty())
                        @if ($combined->isNotEmpty())
                            <div 
                                x-data="{ openIndex: null }" 
                                class="bg-white p-6 rounded-xl border border-gray-200 mt-6 shadow-lg space-y-4"
                            >
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-info-circle text-red-500 text-lg"></i>
                                    <p class="text-red-500 font-semibold text-sm">Revision Details</p>
                                </div>

                                <table class="w-full text-sm text-left table-fixed border border-gray-200 rounded-lg overflow-hidden">
                                    <thead class="bg-gray-100 text-gray-700 uppercase text-[11px] tracking-wider">
                                        <tr>
                                            <th class="py-3 px-3 w-40">Date</th>
                                            <th class="py-3 px-3">Revision Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($combined as $item)
                                            @php
                                                $hasPicResponse = !empty($item['pic_message']);
                                                $index = $loop->index;
                                            @endphp

                                            <tr 
                                                @if($hasPicResponse)
                                                    @click="openIndex === {{ $index }} ? openIndex = null : openIndex = {{ $index }}"
                                                    class="cursor-pointer hover:bg-gray-50 transition"
                                                @else
                                                    class="cursor-not-allowed"
                                                @endif
                                            >
                                                <td class="py-2 px-3 text-gray-500 text-xs whitespace-nowrap align-top">
                                                    {{ $item['rev']->updated_at->format('d M Y H:i') }}
                                                </td>
                                                <td class="py-2 px-3 align-top">
                                                    <div class="flex justify-between items-center">
                                                        <span class="text-gray-800 text-xs leading-snug break-words w-full">
                                                            {{ $item['rev']->alasan_reject }}
                                                        </span>
                                                        @if($hasPicResponse)
                                                            <svg 
                                                                :class="{ 'rotate-180': openIndex === {{ $index }} }"
                                                                class="w-4 h-4 text-gray-400 transition-transform"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                            >
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                            </svg>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>

                                            @if($hasPicResponse)
                                            <tr x-show="openIndex === {{ $index }}" x-transition>
                                                <td colspan="2" class="bg-gray-50 px-6 py-4">
                                                    <p class="text-gray-700 text-xs font-semibold mb-1">PIC Response</p>
                                                    <p class="text-gray-800 text-[11px] leading-snug break-words text-justify">
                                                        {{ $item['pic_message'] }}
                                                    </p>
                                                </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif       
                </div>

                <!-- History Card -->
                <div class="bg-white rounded-lg shadow-sm p-3 border border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="mb-2 sm:mb-0">
                            <h2 class="text-sm font-semibold text-gray-800">Corrective Action History</h2>
                            <p class="text-xs text-gray-500">View the detailed progress and corrective actions</p>
                        </div>
                        <a href="{{ route('admin.budget-approval.history', $taskBudget->id_laporan_lct) }}" class="inline-block">
                            <button class="w-full sm:w-auto px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-xs font-medium flex items-center justify-center cursor-pointer">
                                History
                            </button>
                        </a>
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
