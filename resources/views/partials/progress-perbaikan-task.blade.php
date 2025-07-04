<div class="w-full max-h-[calc(100vh)] pb-32 overflow-y-auto 
                [&::-webkit-scrollbar]:w-2
                [&::-webkit-scrollbar-track]:rounded-full
                [&::-webkit-scrollbar-track]:bg-gray-100
                [&::-webkit-scrollbar-thumb]:rounded-full
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-track]:bg-slate-700
                dark:[&::-webkit-scrollbar-thumb]:bg-slate-500">
<div class="w-full px-4 sm:px-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-0 mb-6 pt-6">
        <!-- Judul dan Deskripsi -->
        <div>
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Approved Tasks List
            </h1>
            <p class="text-sm text-gray-500 mt-1">List of all approved tasks with current status</p>
        </div>
        @php
            $status = $laporan->status_lct;
            $statusMap = [
                'approved_taskbudget' => ['label' => 'Approved (Task Budget)', 'color' => 'bg-emerald-100 text-emerald-800'],
                'approved_permanent' => ['label' => 'Approved (Permanent)', 'color' => 'bg-emerald-100 text-emerald-800'],
                'waiting_approval_taskbudget' => ['label' => 'Waiting Approval (Task Budget)', 'color' => 'bg-amber-100 text-amber-800'],
                'waiting_approval_permanent' => ['label' => 'Waiting Approval (Permanent)', 'color' => 'bg-amber-100 text-amber-800'],
                'waiting_approval_temporary' => ['label' => 'Waiting Approval (Temporary)', 'color' => 'bg-amber-100 text-amber-800'],
                'work_permanent' => ['label' => 'In Progress (Permanent)', 'color' => 'bg-amber-100 text-amber-800'],
                'taskbudget_revision' => ['label' => 'Revision (Task Budget)', 'color' => 'bg-rose-100 text-rose-800'],
                'temporary_revision' => ['label' => 'Revision (Temporary)', 'color' => 'bg-rose-100 text-rose-800'],
                'permanent_revision' => ['label' => 'Revision (Permanent)', 'color' => 'bg-rose-100 text-rose-800'],
                'closed' => ['label' => 'Closed', 'color' => 'bg-gray-200 text-gray-800'],
            ];

            $label = $statusMap[$status]['label'] ?? ucfirst(str_replace('_', ' ', $status));
            $color = $statusMap[$status]['color'] ?? 'bg-gray-100 text-gray-800';
        @endphp
        <!-- Badge Status -->
        <div class="md:mt-0">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $color }}">
                {{ $label }}
            </span>
        </div>
    </div>


    <!-- Main Card -->
    <div class="bg-white p-3 rounded-lg shadow-lg w-full mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if($laporan->tasks->isEmpty())
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-700">No tasks created yet</h3>
                    <p class="mt-1 text-sm text-gray-500 max-w-md mx-auto">The SVP has not created any tasks for this report.</p>
                </div>
            @else
                <div class="overflow-x-auto w-full">
                    <table class="min-w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-50 text-[11px] text-gray-500 uppercase">
                            <tr>
                                <th class="px-4 py-3 text-center">#</th>
                                <th class="px-4 py-3">Task Name</th>
                                <th class="px-4 py-3">PIC</th>
                                <th class="px-4 py-3">Due Date</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($laporan->tasks as $index => $task)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $task->task_name }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs">{{ $task->pic->user->fullname }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-[10px]">
                                        <div>{{ \Carbon\Carbon::parse($task->due_date)->locale('en')->isoFormat('MMM D, YYYY') }}</div>
                                        <div class="{{ $task->due_date < now() && $task->status != 'completed' ? 'text-rose-500' : 'text-gray-500' }}">
                                            @if($task->due_date < now() && $task->status != 'completed')
                                                Overdue
                                            @else
                                                {{ \Carbon\Carbon::parse($task->due_date)->locale('en')->diffForHumans() }}
                                            @endif
                                        </div>
                                    </td>                                    
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $task->status == 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $task->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $task->status == 'completed' ? 'bg-emerald-100 text-emerald-800' : '' }}">
                                            {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 truncate" title="{{ $task->notes ?? 'No notes' }}">
                                        {{ $task->notes ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
        
                {{-- Action Buttons --}}
                @if(Auth::guard('ehs')->check())
                    @if(in_array($laporan->status_lct, ['approved_taskbudget','waiting_approval_permanent', 'approved_permanent']))
                        <div class="bg-gray-50 px-4 py-4 border-t border-gray-100 flex justify-end space-x-3">
                            @if($laporan->status_lct === 'waiting_approval_permanent' && $allTasksCompleted)
                                <form action="{{ route('ehs.reporting.close', $laporan->id_laporan_lct) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Approve All Tasks
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>


    <!-- Status Notification -->
    @if(in_array($laporan->status_lct, ['approved_taskbudget', 'approved_permanent', 'closed']))
        <div class="mt-6">
            @if($laporan->status_lct === 'approved_taskbudget')
                <div class="rounded-md bg-amber-50 p-4 border-l-4 border-amber-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800">Approval in Progress</h3>
                            <div class="mt-2 text-sm text-amber-700">
                                <p>The tasks are still in the budget approval stage. Final approval pending review.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($laporan->status_lct === 'approved_permanent')
                <div class="rounded-md bg-emerald-50 p-4 border-l-4 border-emerald-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-emerald-800">Permanently Approved</h3>
                            <div class="mt-2 text-sm text-emerald-700">
                                <p>All tasks have been permanently approved. Ready for closure when appropriate.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($laporan->status_lct === 'closed')
                <div class="rounded-md bg-gray-50 p-4 border-l-4 border-gray-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-800">Case Closed</h3>
                            <div class="mt-2 text-sm text-gray-700">
                                <p>This report has been officially closed. No further actions required.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
</div>