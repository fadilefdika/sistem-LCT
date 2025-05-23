<div class="w-full bg-[#F3F4F6] max-h-[calc(100vh)] pb-32 overflow-y-auto 
                    [&::-webkit-scrollbar]:w-1
                    [&::-webkit-scrollbar-track]:rounded-full
                    [&::-webkit-scrollbar-track]:bg-gray-100
                    [&::-webkit-scrollbar-thumb]:rounded-full
                    [&::-webkit-scrollbar-thumb]:bg-gray-300
                    dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                    dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
<div class="mx-auto">
    <h3 class="text-2xl font-bold text-gray-800 mb-4 mt-3 flex items-center">📌 Approved Tasks List</h3>

    <div class="bg-white px-6 py-6 rounded-xl shadow-lg border border-gray-200">
        @if($laporan->tasks->isEmpty())
            <div class="text-gray-500 italic text-center py-10">
                No tasks have been created by SVP yet.
            </div>
        @else
            <!-- Header -->
            <div class="flex items-center font-semibold text-gray-900 border-b pb-3">
                <div class="w-10 text-center">No</div>
                <div class="flex-1 px-4">Task Name</div>
                <div class="w-40 px-4">PIC</div>
                <div class="w-32 px-4">Due Date</div>
                <div class="w-32 px-4">Status</div>
                <div class="w-40 px-4">Attachment</div>
            </div>
    
            <!-- Data Rows -->
            <div class="divide-y divide-gray-200">
                @foreach($laporan->tasks as $index => $task)
                    <div class="flex items-center py-4 hover:bg-gray-100 transition duration-200 rounded-lg">
                        <div class="w-10 text-center font-medium text-gray-900">{{ $index + 1 }}</div>
                        <div class="flex-1 px-4 font-medium text-gray-900">{{ $task->task_name }}</div>
                        <div class="w-40 px-4 text-gray-800">{{ $task->pic->user->fullname }}</div>
                        <div class="w-32 px-4 text-gray-800">{{ \Carbon\Carbon::parse($task->due_date)->format('F j, Y') }}</div>
                        
                        <!-- Status with better visibility -->
                        <div class="w-32 px-4 text-center">
                            <span class="px-3 py-1 text-sm rounded-full 
                                        {{ $task->status == 'pending' ? 'bg-yellow-200 text-yellow-700' : '' }}
                                        {{ $task->status == 'in_progress' ? 'bg-blue-200 text-blue-700' : '' }}
                                        {{ $task->status == 'completed' ? 'bg-green-200 text-green-700' : '' }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
    
                        <div class="w-40 px-4 text-gray-600 italic">{{ $task->notes ?? '-' }}</div>
                    </div>
                @endforeach
            </div>

            @php
                if (Auth::guard('ehs')->check()) {
                    // Jika pengguna adalah EHS, ambil role dari relasi 'roles' pada model EhsUser
                    $user = Auth::guard('ehs')->user();
                    $roleName = optional($user->roles->first())->name;
                } else {
                    // Jika pengguna adalah User biasa, ambil role dari relasi 'roleLct' pada model User
                    $user = Auth::user();
                    $roleName = optional($user->roleLct->first())->name;
                }
            @endphp
            
            <!-- Tombol Approve & Close, hanya untuk EHS -->
            @if(Auth::guard('ehs')->check())
                @if(in_array($laporan->status_lct, ['approved_taskbudget','waiting_approval_permanent', 'approved_permanent']))
                    <div class="mt-6 flex justify-end gap-4">
                        <!-- Tombol Approve -->
                        @if($laporan->status_lct === 'waiting_approval_permanent' && $allTasksCompleted)
                            <form action="{{ route('ehs.reporting.approve', $laporan->id_laporan_lct) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-4 py-2 text-white font-semibold rounded-lg bg-green-500 hover:bg-green-600 transition cursor-pointer">
                                    ✅ Approve All
                                </button>
                            </form>
                        @endif
            
                        <!-- Tombol Close -->
                        @if($laporan->status_lct === 'approved_permanent')
                            <form action="{{ route('ehs.reporting.close', $laporan->id_laporan_lct) }}" method="POST">
                                @csrf 
                                <button type="submit"
                                        class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg shadow-md hover:bg-gray-800 transition cursor-pointer">
                                    🔒 Close
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            @endif
        @endif
    </div>
    

    <!-- Notifikasi Status di Bagian Bawah -->
    @if(in_array($laporan->status_lct, ['approved_taskbudget', 'approved_permanent', 'closed']))
        <div class="mt-6 bg-white px-6 py-4 rounded-xl shadow-lg border border-gray-200">
            @if($laporan->status_lct === 'approved_taskbudget')
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                    <p class="font-bold">📝 Approval in Progress</p>
                    <p>The tasks are still in the budget approval stage. Please review before final approval.</p>
                </div>
            @elseif($laporan->status_lct === 'approved_permanent')
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p class="font-bold">✅ Permanently Approved</p>
                    <p>All tasks have been permanently approved. You may proceed with closure.</p>
                </div>
            @elseif($laporan->status_lct === 'closed')
                <div class="bg-gray-100 border-l-4 border-gray-500 text-gray-700 p-4" role="alert">
                    <p class="font-bold">🔒 Case Closed</p>
                    <p>This case has been closed. No further actions are required.</p>
                </div>
            @endif
        </div>
    @endif
</div>
</div>