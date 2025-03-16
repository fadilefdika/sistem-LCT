<div class="mx-auto">
    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">📌 Approved Tasks List</h3>

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
        <div class="mt-6 flex gap-4">
            <form action="{{-- route('admin.lct.approve', $laporan->id_laporan_lct) --}}" method="POST">
                @csrf
                <button type="submit"
                    class="px-4 py-2 text-white font-semibold rounded-lg bg-green-500 hover:bg-green-600 transition"
                    {{ !$allTasksCompleted ? 'disabled' : '' }}>
                    ✅ Approve
                </button>
            </form>
            
            <form action="{{-- route('admin.lct.close', $laporan->id_laporan_lct) --}}" method="POST">
                @csrf
                <button type="submit"
                    class="px-4 py-2 text-white font-semibold rounded-lg bg-red-500 hover:bg-red-600 transition"
                    {{ !$allTasksCompleted ? 'disabled' : '' }}>
                    ❌ Close Case
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
