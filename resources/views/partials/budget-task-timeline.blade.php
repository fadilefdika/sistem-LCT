<div class="bg-white px-6 pt-6 pb-6 rounded-lg shadow-lg relative h-full mb-4 overflow-x-auto" x-data="{ 
    tasks: [{ taskName: '', dueDate: '', budgetEstimation: 0, notes: '' }],
    totalBudget: 0,
    updateTotal() {
        this.totalBudget = this.tasks.reduce((sum, task) => sum + (parseFloat(task.budgetEstimation.toString().replace(/\D/g, '')) || 0), 0);
    },
    formatCurrency(index) {
        let num = this.tasks[index].budgetEstimation.toString().replace(/\D/g, '');
        this.tasks[index].budgetEstimation = num.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        this.updateTotal();
    },
}">
    <h3 class="text-lg font-semibold mb-4">Task Management and Timeline</h3>

    <div class="mt-4 overflow-x-auto">
        <table class="min-w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-3 py-2 text-center w-12">No</th>
                    <th class="border border-gray-300 px-3 py-2 text-left w-3/6">Task Name</th>
                    <th class="border border-gray-300 px-3 py-2 text-left w-1/6">Due Date</th>
                    <th class="border border-gray-300 px-3 py-2 text-left w-1/6">Budget (Rp)</th>
                    <th class="border border-gray-300 px-3 py-2 text-left w-2/6">Notes</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(task, index) in tasks" :key="index">
                    <tr>
                        <!-- Row Number -->
                        <td class="border border-gray-300 px-3 py-2 text-center w-12" x-text="index + 1"></td>
    
                        <!-- Task Name (Lebih Panjang) -->
                        <td class="border border-gray-300 px-3 py-2 w-3/6">
                            <input type="text" x-model="task.taskName" 
                                @input="if(index === tasks.length - 1 && task.taskName.trim() !== '') tasks.push({ taskName: '', dueDate: '', budgetEstimation: 0, notes: '' })"
                                class="w-full p-1 focus:ring-0 outline-none bg-transparent" 
                                placeholder="Enter task name" required>
                        </td>
    
                        <!-- Due Date (FIXED) -->
                        <td class="border border-gray-300 px-3 py-2 w-1/6">
                            <input type="date" x-model="task.dueDate"
                                class="w-full p-1 focus:ring-0 outline-none bg-transparent text-center" required>
                        </td>
    
                        <!-- Budget Estimation -->
                        <td class="border border-gray-300 px-3 py-2 w-1/6">
                            <input type="text" x-model="task.budgetEstimation" 
                                @input="formatCurrency(index)" 
                                class="w-full p-1 focus:ring-0 outline-none bg-transparent text-right" 
                                placeholder="0" required>
                        </td>
    
                        <!-- Notes -->
                        <td class="border border-gray-300 px-3 py-2 w-2/6">
                            <input type="text" x-model="task.notes" 
                                class="w-full p-1 focus:ring-0 outline-none bg-transparent" 
                                placeholder="Enter notes">
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    

    <!-- Total Budget -->
    <div class="mt-4 p-4 bg-gray-100 rounded-lg shadow-md text-right">
        <h3 class="text-lg font-semibold mb-2">Total Budget</h3>
        <div class="flex justify-between">
            <span class="font-medium">Total Estimated Budget (Rp):</span>
            <span class="font-bold text-xl" x-text="totalBudget.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 })"></span>
        </div>
    </div>

    <!-- Submit button -->
    <div class="flex justify-end">
        <button type="submit" class=" text-white w-20 whitespace-nowrap flex justify-end bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 mt-4 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 cursor-pointer">
            Submit Report
        </button>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        Alpine.start();
    });

    document.body.addEventListener('click', function (event) {
        if (event.target.matches('input[type="date"]')) {
            event.target.showPicker();
        }
    });
</script>
