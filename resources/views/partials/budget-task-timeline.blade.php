<div x-data="{ 
    isApproved: @json($laporan->status_lct === 'approved_taskbudget') 
}">

    @if($laporan->status_lct === 'taskbudget_revision')
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p class="font-bold">Revision Required</p>
            <p>There is a revision on the budget request. Please check the revision details.</p>
        </div>
    @elseif($laporan->status_lct === 'waiting_approval_taskbudget')
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
            <p class="font-bold">Awaiting Manager Approval</p>
            <p>Your revision has been submitted. Please wait for approval.</p>
        </div>
    @endif

    <!-- FORM TASK (DITAMPILKAN SAAT BELUM DI APPROVE) -->
    <div x-show="!isApproved">
        <form action="{{ route('admin.manajemen-lct.submitTaskBudget', ['id_laporan_lct' => $laporan->id_laporan_lct]) }}" method="POST" id="taskForm">
            @csrf
            <input type="hidden" name="deletedTasks" id="deletedTasksInput">

            <div class="bg-white px-6 pt-6 pb-6 rounded-lg shadow-lg relative h-full mb-4 overflow-x-auto">
                <h3 class="text-lg font-semibold mb-4">Task Management and Timeline</h3>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-center">No</th>
                                <th class="border px-3 py-2 text-left">Task Name</th>
                                <th class="border px-3 py-2 text-left">PIC Name</th>
                                <th class="border px-3 py-2 text-left">Due Date</th>
                                <th class="border px-3 py-2 text-left">Notes</th>
                                <th class="border px-3 py-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(task, index) in tasks" :key="index">
                                <tr>
                                    <td class="border px-3 py-2 text-center" x-text="index + 1"></td>
                                    <input type="hidden" x-model="task.id" :name="'tasks['+index+'][id]'">
                                    <td class="border px-3 py-2"><input type="text" x-model="task.taskName" class="w-full"></td>
                                    <td class="border px-3 py-2"><input type="text" x-model="task.namePic" class="w-full"></td>
                                    <td class="border px-3 py-2"><input type="date" x-model="task.dueDate" class="w-full"></td>
                                    <td class="border px-3 py-2"><input type="text" x-model="task.notes" class="w-full"></td>
                                    <td class="border px-3 py-2 text-center">
                                        <button type="button" @click="removeTask(index)" class="text-red-600">×</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Estimasi Budget -->
                <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                    <h3 class="text-lg font-semibold mb-2">Estimasi Budget</h3>
                    <div class="flex items-center">
                        <span class="font-medium mr-3">Total Budget (Rp):</span>
                        <input type="text" x-model="estimatedBudget" class="w-40 p-2 border border-gray-300 rounded-lg text-right">
                        <input type="hidden" name="estimatedBudget" :value="estimatedBudget ? estimatedBudget.replace(/\./g, '') : ''">
                    </div>
                </div>

                <!-- Submit button -->
                @if(in_array($laporan->status_lct ?? '', ['approved_temporary', 'waiting_approval_taskbudget']))
                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-blue-700 px-5 py-3 rounded-lg mt-4">
                        Submit Report
                    </button>
                </div>
                @endif
            </div>
        </form>
    </div>

<!-- VIEW APPROVED TASKS -->
<div x-show="isApproved" class="mt-6">
    <h3 class="text-xl font-bold text-gray-800 mb-4">✅ Approved Tasks List</h3>

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
            @foreach($tasks as $index => $task)
                @if(!empty($task['taskName']) && !empty($task['namePic']) && !empty($task['dueDate']))
                    <div class="grid grid-cols-6 gap-4 py-4 items-center hover:bg-gray-100 transition duration-200 rounded-lg">
                        <div class="font-medium text-gray-900">{{ $index + 1 }}</div>
                        <div class="text-gray-900 font-medium">{{ $task['taskName'] }}</div>
                        <div class="text-gray-600">{{ $task['namePic'] }}</div>
                        <div class="text-gray-600">{{ $task['dueDate'] }}</div>
                        <div>
                            <select class="status-dropdown border rounded-lg px-3 py-1 bg-gray-50 focus:ring focus:ring-blue-300 transition duration-200 text-gray-700 w-full appearance-none">
                                <option value="pending" {{ $task['status'] == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="in_progress" {{ $task['status'] == 'in_progress' ? 'selected' : '' }}>🚀 In Progress</option>
                                <option value="completed" {{ $task['status'] == 'completed' ? 'selected' : '' }}>✅ Completed</option>
                            </select>
                        </div>
                        <div class="text-gray-500 italic">{{ $task['notes'] ?? '-' }}</div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>


    <!-- Reject History -->
<div class="bg-white shadow-md rounded-lg p-6 mt-5">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Revision History</h3>
    <div class="space-y-4">
        @if ($laporan->rejectLaporan->isNotEmpty())
        @foreach ($laporan->rejectLaporan as $reject)
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm border-l-4 border-red-500">
                <p class="text-sm text-gray-600">
                    <strong class="text-gray-800">Revision on:</strong> 
                    <span class="font-medium">
                        {{ $reject->created_at->setTimezone('Asia/Jakarta')->format('F j, Y') }} at 
                        {{ $reject->created_at->setTimezone('Asia/Jakarta')->format('h:i A') }} WIB
                    </span>                                
                </p>
                <p class="text-sm text-gray-600">
                    <strong class="text-gray-800">Reason:</strong> 
                    <span class="font-medium">{{ $reject->alasan_reject }}</span>
                </p>
            </div>
        @endforeach
        @else
            <p class="text-sm text-gray-500 italic">No revision history available.</p>
        @endif
    </div>
</div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        Alpine.start();
    });

    function formatCurrency(value) {
        value = value.replace(/\D/g, ""); // Hapus semua karakter non-digit
        return new Intl.NumberFormat("id-ID").format(value);
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('taskData', () => ({
            tasks: @json($tasks ?? []), // Pastikan jika $tasks null, set default ke []
            estimatedBudget: '{{ number_format($laporan->estimated_budget ?? 0, 0, ',', '.') }}',
            removeTask(index) {
                this.tasks.splice(index, 1);
            },
            canSubmit() {
                return this.tasks.length > 0 && this.estimatedBudget !== '';
            }
        }));
    });
</script>


<script>
   document.querySelectorAll('.status-dropdown').forEach((dropdown) => {
    dropdown.addEventListener('change', async function () {
        const taskId = dropdown.getAttribute('data-task-id');
        const newStatus = dropdown.value;
        const updateUrl = `/manajemen-lct/${taskId}/updateStatus`;

        try {
            const response = await fetch(updateUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus }),
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
            console.log("Status updated:", data);

        } 
    });
});

</script>
