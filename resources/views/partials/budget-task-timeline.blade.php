<div x-data="taskData()">
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
    @elseif($laporan->status_lct === 'approved_permanent')
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4" role="alert">
            <p class="font-bold">Permanent Approved</p>
            <p>Your task request has been permanently approved.</p>
        </div>
    @elseif($laporan->status_lct === 'approved_temporary')
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p class="font-bold">Temporary & Permanent Task</p>
            <p>Please complete this task within 2 days after the temporary approval.</p>
        </div>
    @endif

    <!-- FORM TASK (DITAMPILKAN SAAT BELUM DI APPROVE) -->
    @if(!in_array($laporan->status_lct, ['approved_permanent', 'closed']))
    <div x-show="!isApproved">
        <form action="{{ route('admin.manajemen-lct.submitTaskBudget', ['id_laporan_lct' => $laporan->id_laporan_lct]) }}" method="POST">
            @csrf
            <input type="hidden" name="deletedTasks" id="deletedTasksInput">

            <div class="bg-white px-6 pt-6 pb-6 rounded-lg shadow-lg mb-4 overflow-x-auto">
                <h3 class="text-lg font-semibold mb-4">Task Management and Timeline</h3>
                
                <table class="min-w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 text-center">No</th>
                            <th class="border px-3 py-2 text-left">Task Name</th>
                            <th class="border px-3 py-2 text-left">SVP Name</th>
                            <th class="border px-3 py-2 text-left">Due Date</th>
                            <th class="border px-3 py-2 text-left">Notes</th>
                            <th class="border px-3 py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody> 
                        <template x-for="(task, index) in tasks" :key="index">
                            <tr>
                                <td class="border px-3 py-2 text-center" x-text="index + 1"></td>
                                <input type="hidden" x-model="task.id" :name="'tasks['+index+'][id]'" placeholder="">
                            
                                <!-- Task Name -->
                                <td class="border">
                                    <input type="text" x-model="task.taskName"
                                        @click="addRow(index)"
                                        class="w-full border-gray-100"
                                        :name="'tasks['+index+'][taskName]'"
                                        placeholder="Create a New Task..."
                                        >
                                </td>
                        
                                <!-- PIC Selection -->
                                <td class="border">
                                    <select 
                                        class="w-full border-gray-100"
                                        x-model="task.picId" 
                                        :name="'tasks['+index+'][picId]'"
                                        @change="console.log('Task ID:', task.id, 'PIC Terpilih:', task.picId)"
                                    >
                                        <option value="">Pilih PIC</option>
                                        @foreach($picList as $pic)
                                            <option value="{{ $pic['id'] }}" x-bind:selected="task.picId == {{ $pic['id'] }}">
                                                {{ $pic['fullname'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                        
                                <td class="border">
                                    <input type="date" x-model="task.dueDate" class="w-full border-gray-100" :name="'tasks['+index+'][dueDate]'">
                                </td>
                                <td class="border">
                                    <input type="text" x-model="task.notes" class="w-full border-gray-100" :name="'tasks['+index+'][notes]'" placeholder="Notes (Optional)...">
                                </td>
                                <td class="border text-center">
                                    <button type="button" @click="removeTask(index)" class="text-red-600">×</button>
                                </td>
                            </tr>
                        </template>
                        
                    </tbody>
                </table>

                <div class="mt-4 p-4 bg-gray-100 rounded-lg" 
                x-data="{
                    estimatedBudget: '{{ intval($laporan->estimated_budget ?? 0) }}', // Pastikan jadi integer
                    formattedBudget: '',
                    showError: false,
                    formatCurrency() {
                        this.formattedBudget = this.estimatedBudget.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    }
                }"
                x-init="formatCurrency()"
            >
                <h3 class="text-lg font-semibold mb-2">Estimated Budget</h3>
                <div class="flex items-center">
                    <span class="font-medium mr-3">Estimated Budget (Rp):</span>
                    <input 
                        type="text" 
                        x-model="formattedBudget" 
                        class="w-40 p-2 border border-gray-300 rounded-lg text-right" 
                        @input="
                            estimatedBudget = $event.target.value.replace(/\D/g, '');
                            formatCurrency();
                            showError = false;
                        "
                        @blur="showError = estimatedBudget === ''"
                        placeholder="0"
                        required
                    >
                    
                    <!-- Hidden input untuk mengirimkan nilai ke controller -->
                    <input type="hidden" name="estimatedBudget" :value="estimatedBudget" required>
                </div>
            
                <!-- Pesan Error -->
                <p x-show="showError" class="text-red-500 text-sm mt-1">Estimasi budget wajib diisi!</p>
            </div>
            
                <!-- Submit button -->
                @if(in_array($laporan->status_lct ?? '', ['approved_temporary', 'taskbudget_revision']))
                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-blue-700 px-5 py-3 rounded-lg mt-4 cursor-pointer">
                        Submit Report
                    </button>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>
@endif

@if(in_array($laporan->status_lct, ['approved_taskbudget', 'approved_permanent', 'closed']))
    <div class="mt-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">✅ Approved Tasks List</h3>
        
        @if($laporan->status_lct === 'approved_permanent')
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p class="font-bold">Permanent Approval Granted</p>
                <p>Task has been permanently approved. You may now proceed to close.</p>
            </div>
        @elseif($laporan->status_lct === 'closed')
            <div class="bg-gray-200 border-l-4 border-gray-500 text-gray-700 p-4 mb-4" role="alert">
                <p class="font-bold">Case Closed</p>
                <p>This case has been closed. No further actions required.</p>
            </div>
        @endif

        <div class="bg-white px-6 py-6 rounded-xl shadow-lg">
            <!-- Wrapper with overflow handling -->
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <!-- Table Header -->
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold min-w-[50px]">No</th>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold min-w-[200px]">Nama Task</th>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold min-w-[150px]">Nama PIC</th>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold min-w-[150px]">Due Date</th>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold min-w-[150px]">Status</th>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold min-w-[250px]">Notes</th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        @foreach($tasks as $index => $task)
                            @if(!empty($task['taskName']) && !empty($task['picId']) && !empty($task['dueDate']))
                                <tr class="hover:bg-gray-100 transition duration-200">
                                    <!-- No -->
                                    <td class="px-4 py-2 text-gray-900 min-w-[50px]">{{ $index + 1 }}</td>
                                    <!-- Task Name -->
                                    <td class="px-4 py-2 text-gray-900 overflow-x-auto whitespace-normal min-w-[200px] max-w-[200px] break-words">
                                        {{ $task['taskName'] }}
                                    </td>
                                    <!-- PIC Name -->
                                    <td class="px-4 py-2 text-gray-600 min-w-[150px]">
                                        @php
                                            // Ambil nama PIC berdasarkan picId
                                            $pic = $picList->firstWhere('id', $task['picId']);
                                        @endphp
                                        {{ $pic ? $pic['fullname'] : 'No PIC Assigned' }}
                                    </td>
                                    <!-- Due Date -->
                                    <td class="px-4 py-2 text-gray-600 min-w-[150px]">{{ \Carbon\Carbon::parse($task['dueDate'])->format('F j, Y') }}</td>
                                    <!-- Status -->
                                    <td class="px-4 py-2 min-w-[150px]">
                                        <select class="status-dropdown border rounded-lg px-3 py-1 bg-gray-50 focus:ring focus:ring-blue-300 transition duration-200 text-gray-700 w-full appearance-none" 
                                                data-task-id="{{ $task['id'] }}">
                                            <option value="pending" {{ $task['status'] == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                            {{-- <option value="in_progress" {{ $task['status'] == 'in_progress' ? 'selected' : '' }}>🚀 In Progress</option> --}}
                                            <option value="completed" {{ $task['status'] == 'completed' ? 'selected' : '' }}>✅ Completed</option>
                                        </select>
                                    </td>
                                    <!-- Notes -->
                                    <td class="px-4 py-2 text-gray-500 italic overflow-x-auto whitespace-normal min-w-[250px] max-w-[250px] break-words">
                                        {{ $task['notes'] ?? '-' }} 
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>        
    </div>
@endif




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
        isApproved: @json($laporan->status_lct === 'approved_taskbudget'),
        tasks: @json($tasks ?? []), 
        estimatedBudget: '{{ $laporan->estimated_budget ?? 0 }}',

        get formattedBudget() {
            return new Intl.NumberFormat("id-ID").format(this.estimatedBudget);
        },
        set formattedBudget(value) {
            this.estimatedBudget = value.replace(/\D/g, '');
        },

        init() {
            if (this.tasks.length === 0) {
                this.tasks.push({ taskName: '', picId: '', dueDate: '', notes: '' });
            }
        },

        addRow(index) {
            if (index === this.tasks.length - 1) {
                this.tasks.push({ taskName: '', picId: '', dueDate: '', notes: '' });
            }
        },

        removeTask(index) {
            if (this.tasks.length > 1) {
                this.tasks.splice(index, 1);
            }
        },

        validatePicId(index) {
            console.log("Validasi PIC:", this.tasks[index].picId);
            if (!this.tasks[index].picId) {
                console.warn(`Task ${index + 1}: PIC belum dipilih!`);
            }
        }
    }));
});

</script>


<script>
   document.querySelectorAll('.status-dropdown').forEach((dropdown) => {
    dropdown.addEventListener('change', async function () {
        const taskId = this.getAttribute('data-task-id');
        const newStatus = this.value;
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

            // Tambahkan efek sukses (misalnya warna hijau)
            this.classList.add('bg-green-100');
            setTimeout(() => this.classList.remove('bg-green-100'), 2000);

        } catch (error) {
            console.error("Failed to update status:", error);
            alert("Gagal memperbarui status. Silakan coba lagi.");
        }
    });
});

</script>

