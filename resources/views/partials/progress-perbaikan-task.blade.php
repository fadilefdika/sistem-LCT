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
                <div class="w-24 px-4">Status</div>
                <div class="w-40 px-4">Notes</div>
            </div>

            <!-- Data Rows -->
            <div class="divide-y divide-gray-200">
                @foreach($laporan->tasks as $index => $task)
                    <div class="flex items-center py-4 hover:bg-gray-100 transition duration-200 rounded-lg">
                        <div class="w-10 text-center font-medium text-gray-900">{{ $index + 1 }}</div>
                        <div class="flex-1 px-4 font-medium text-gray-900">{{ $task->task_name }}</div>
                        <div class="w-40 px-4 text-gray-600">{{ $task->name_pic }}</div>
                        <div class="w-32 px-4 text-gray-600">{{ \Carbon\Carbon::parse($task->due_date)->format('F j, Y') }}</div>
                        <div class="w-24 px-4 text-gray-600">{{ $task->status }}</div>
                        <div class="w-40 px-4 text-gray-500 italic">{{ $task->notes ?? '-' }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Tombol Approve & Close -->
            @if(in_array($laporan->status_lct, ['approved_taskbudget', 'approved_permanent']))
                <div class="mt-6 flex gap-4">
                    <!-- Tombol Approve -->
                    @if($laporan->status_lct === 'approved_taskbudget')
                        <form action="{{ route('admin.progress-perbaikan.approve', $laporan->id_laporan_lct) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 text-white font-semibold rounded-lg bg-green-500 hover:bg-green-600 transition cursor-pointer"
                                {{ !$allTasksCompleted ? 'disabled' : '' }}>
                                ✅ Approve
                            </button>
                        </form>
                    @endif

                    <!-- Tombol Close -->
                    @if($laporan->status_lct === 'approved_permanent')
                        <form action="{{ route('admin.progress-perbaikan.close', $laporan->id_laporan_lct) }}" method="POST">
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
