<div>
    <h3 class="text-xl font-bold text-gray-800 mb-4">📌 Approved Tasks List</h3>

    <div class="bg-white px-6 py-6 rounded-xl shadow-lg border border-gray-200">
        <!-- Header -->
        <div class="grid grid-cols-6 gap-4 font-semibold text-gray-900 border-b pb-3">
            <div>No</div>
            <div>Nama Task</div>
            <div>Nama PIC</div>
            <div>Due Date</div>
            <div>Status</div>
            <div>Notes</div>
        </div>

        <!-- Data Rows -->
        <div class="divide-y divide-gray-200">
            @foreach($laporan->tasks as $index => $task)
                <div class="grid grid-cols-6 gap-4 py-4 items-center hover:bg-gray-100 transition duration-200 rounded-lg">
                    <div class="font-medium text-gray-900">{{ $index + 1 }}</div>
                    <div class="text-gray-900 font-medium">{{ $task->task_name }}</div>
                    <div class="text-gray-600">{{ $task->name_pic }}</div>
                    <div class="text-gray-600">{{ \Carbon\Carbon::parse($task->due_date)->format('F j, Y') }}</div>
                    <div class="text-gray-600">{{$task->status}}</div>
                    <div class="text-gray-500 italic">{{ $task->notes ?? '-' }}</div>
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
    </div>
</div>
