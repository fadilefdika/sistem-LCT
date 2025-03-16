@if($laporan->status_lct === 'taskbudget_revision')
    <div id="revision-notification" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
        <p class="font-bold">Revision Required</p>
        <p>There is a revision on the budget request. Please scroll down to see the revision details.</p>
    </div>
    @elseif($laporan->status_lct === 'waiting_approval_taskbudget')
        <div id="approval-notification" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
            <p class="font-bold">Awaiting Manager Approval</p>
            <p>Your revision has been submitted. Please wait for the manager's approval before proceeding.</p>
        </div>
@endif


<form action="{{ route('admin.manajemen-lct.submitTaskBudget',['id_laporan_lct' => $laporan->id_laporan_lct] )}}" 
    method="POST" id="taskForm">
    @csrf
    <input type="hidden" name="deletedTasks" id="deletedTasksInput">

    <div class="bg-white px-6 pt-6 pb-6 rounded-lg shadow-lg relative h-full mb-4 overflow-x-auto"
        x-data="taskData()" x-init="console.log('Loaded tasks:', tasks)">

        <h3 class="text-lg font-semibold mb-4">Task Management and Timeline</h3>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-3 py-2 text-center w-12">No</th>
                        <th class="border border-gray-300 px-3 py-2 text-left w-3/6">Task Name</th>
                        <th class="border border-gray-300 px-3 py-2 text-left w-1/6">PIC Name</th>
                        <th class="border border-gray-300 px-3 py-2 text-left w-1/6">Due Date</th>
                        <th class="border border-gray-300 px-3 py-2 text-left w-2/6">Notes</th>
                        <th class="border border-gray-300 px-3 py-2 text-center w-12">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(task, index) in tasks" :key="index">
                        <tr>
                            <td class="border border-gray-300 px-3 py-2 text-center w-12" x-text="index + 1"></td>
                            <input type="hidden" x-model="task.id" :name="'tasks['+index+'][id]'">
                            
                            <td class="border border-gray-300 w-3/6">
                                <input type="text" x-model="task.taskName" :name="'tasks['+index+'][taskName]'"
                                    @input="if(index === tasks.length - 1 && task.taskName.trim() !== '') tasks.push({ taskName: '', dueDate: '', namePic: '', notes: '' })"
                                    class="w-full p-2 focus:ring-0 outline-none bg-transparent border border-gray-50" 
                                    placeholder="Enter task name">
                            </td>
        
                            <td class="border border-gray-300 w-1/6">
                                <input type="text" x-model="task.namePic" :name="'tasks['+index+'][namePic]'"
                                    class="w-full p-2 focus:ring-0 outline-none bg-transparent border border-gray-50" 
                                    placeholder="Enter PIC name">
                            </td>
        
                            <td class="border border-gray-300 w-1/6">
                                <input type="date" x-model="task.dueDate" :name="'tasks['+index+'][dueDate]'"
                                    class="w-full p-2 focus:ring-0 outline-none bg-transparent border border-gray-50">
                            </td>
        
                            <td class="border border-gray-300 w-2/6">
                                <input type="text" x-model="task.notes" :name="'tasks['+index+'][notes]'"
                                    class="w-full p-2 focus:ring-0 outline-none bg-transparent border border-gray-50" 
                                    placeholder="Enter notes">
                            </td>
        
                            <!-- Clear Button -->
                            <td class="border border-gray-300 px-3 py-2 text-center w-12">
                                <button type="button" @click="removeTask(index)" 
                                    class="text-red-600 font-bold text-lg">
                                    ×
                                </button>
                            </td>
                        </tr>
                    </template>                    
                </tbody>
            </table>
        </div>

        <!-- Estimasi Budget -->
        <div class="mt-4 p-4 bg-gray-100 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-2">Estimasi Budget</h3>
            <div class="flex items-center">
                <span class="font-medium mr-3">Total Estimated Budget (Rp):</span>
                <input type="text" x-model="estimatedBudget"
                    @input="estimatedBudget = formatCurrency($event.target.value)"
                    class="w-40 p-2 border border-gray-300 rounded-lg text-right text-lg font-bold outline-none focus:ring-2 focus:ring-blue-400"
                    placeholder="0" required>
                <input type="hidden" name="estimatedBudget" :value="estimatedBudget.replace(/\./g, '')">
            </div>
        </div>

        <!-- Submit button -->
    @if(in_array($laporan->status_lct, ['approved_temporary', 'waiting_approval_taskbudget']))
    <div class="flex justify-end">
        <button type="submit"
            class="text-white w-32 bg-blue-700 cursor-pointer hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 mt-4"
            :disabled="!canSubmit()">
            Submit Report
        </button>
    </div>
    @endif

    </div>
</form>

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
            tasks: @json($tasks), // Ambil data dari backend
            estimatedBudget: '{{ number_format($laporan->estimated_budget, 0, ',', '.') }}',
            deletedTasks: [], // Untuk menyimpan ID task yang dihapus

            removeTask(index) {
                if (this.tasks[index]?.id) {
                    this.deletedTasks.push(this.tasks[index].id);
                }
                this.tasks.splice(index, 1);
            },

            canSubmit() {
                return this.tasks.some(task => task.taskName.trim() !== '');
            }
        }));
    });
</script>
