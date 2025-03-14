<form action="{{ route('admin.manajemen-lct.submitTaskBudget',['id_laporan_lct' => $laporan->id_laporan_lct] )}}" method="POST">
    @csrf
    <div class="bg-white px-6 pt-6 pb-6 rounded-lg shadow-lg relative h-full mb-4 overflow-x-auto"
        x-data="{ 
            tasks: JSON.parse(JSON.stringify({{ Illuminate\Support\Js::from($tasks) }})),
            estimatedBudget: '{{ number_format($laporan->estimated_budget, 0, ',', '.') }}',
            formatCurrency(value) {
                let cleaned = value.replace(/\D/g, '');
                return new Intl.NumberFormat('id-ID').format(cleaned);
            },
            canSubmit() {
                return this.tasks.some(task => task.taskName.trim() !== '');
            }
        }"
        x-init="console.log('Loaded tasks:', tasks)"
    >

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
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(task, index) in tasks" :key="index">
                        <tr>
                            <td class="border border-gray-300 px-3 py-2 text-center w-12" x-text="index + 1"></td>
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
        <div class="flex justify-end">
            <button type="submit"
                class="text-white w-32 bg-blue-700 cursor-pointer hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 mt-4"
                :disabled="!canSubmit()">
                Submit Report
            </button>
        </div>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        Alpine.start();
    });

    document.body.addEventListener('click', function (event) {
        if (event.target.matches('input[type="date"]')) {
            event.target.showPicker();
        }
    });

    function formatCurrency(value) {
        value = value.replace(/\D/g, ""); // Hapus semua karakter non-digit
        return new Intl.NumberFormat("id-ID").format(value);
    }

    document.addEventListener("alpine:init", () => {
        Alpine.data("taskManager", () => ({
            estimatedBudget: '{{ number_format($laporan->estimatedBudget, 0, ',', '.') }}',
        }));
    });
</script>
