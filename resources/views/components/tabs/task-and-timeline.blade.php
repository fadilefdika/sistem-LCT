<div class="w-full mx-auto bg-[#F3F4F6] overflow-hidden max-h-[calc(100vh)] pb-36 pt-3">
    @if($budget->status_budget === 'pending')
    <div class="bg-white p-6 rounded-lg shadow-lg text-center">
        <h2 class="text-xl font-semibold text-gray-800 mb-2">Awaiting Approval</h2>
        <p class="text-gray-600">Your submission is currently under review. Please wait for further updates.</p>
    </div>                    
    @else
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold mb-4">Budget Submission for LCT Repairs</h2>
            
            <form action="{{ route('admin.manajemen-lct.submitBudget', ['id_laporan_lct' => $laporan->id_laporan_lct]) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-6" x-data="{
                    formattedAmount: '',
                    formatAmount(value) {
                        value = value.replace(/\D/g, '');
                        this.formattedAmount = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    }
                }">
                    <label for="budget_amount" class="block text-sm font-medium text-gray-700">Budget Amount <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        name="budget_amount" 
                        id="budget_amount" 
                        x-model="formattedAmount"
                        x-on:input="formatAmount($event.target.value)" 
                        required 
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md" 
                        placeholder="Enter amount"
                    >
                    <p class="text-xs text-gray-500">Enter the amount in Indonesian Rupiah without symbols (e.g., 1.500.000)</p>
                </div>

                <div class="mb-6">
                    <label for="budget_description" class="block text-sm font-medium text-gray-700">Budget Description <span class="text-red-500">*</span></label>
                    <textarea name="budget_description" id="budget_description" rows="4" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md"></textarea>
                </div>

                <div class="mb-6">
                    <label for="payment_proof" class="block text-sm font-medium text-gray-700">Payment Proof Attachment <span class="text-red-500">*</span></label>
                    <input 
                        type="file" 
                        name="payment_proof" 
                        id="payment_proof" 
                        accept="image/*,application/pdf" 
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                    >
                    <p class="text-sm text-gray-500 mt-2">Upload an image or PDF file as proof of payment.</p>
                </div>

                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200 cursor-pointer">
                    Submit Budget Request
                </button>
            </form>

            @if($laporan->budget_approval == 'approved')
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-4">Tasks and Timeline</h3>

                    <form action="{{-- route('submit-task') --}}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="task_name" class="block text-sm font-medium text-gray-700">Task Name</label>
                            <input type="text" name="task_name" id="task_name" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        </div>

                        <div class="mb-4">
                            <label for="task_description" class="block text-sm font-medium text-gray-700">Task Description</label>
                            <textarea name="task_description" id="task_description" rows="3" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md"></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                            <input type="date" name="due_date" id="due_date" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            Create Task
                        </button>
                    </form>

                    <div class="mt-6">
                        <h4 class="text-lg font-semibold mb-4">Task Progress</h4>
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 border-b">Task Name</th>
                                    <th class="px-4 py-2 border-b">Status</th>
                                    <th class="px-4 py-2 border-b">Due Date</th>
                                    <th class="px-4 py-2 border-b">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                <tr>
                                    <td class="px-4 py-2 border-b">{{ $task->task_name }}</td>
                                    <td class="px-4 py-2 border-b">
                                        <span class="{{ 
                                            $task->status === 'completed' ? 'text-green-500' :
                                            ($task->status === 'in_progress' ? 'text-yellow-500' : 'text-red-500')
                                        }}">
                                            {{ $task->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border-b">{{ $task->due_date }}</td>
                                    <td class="px-4 py-2 border-b">
                                        <form action="{{ route('update-task-status', $task->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" onchange="this.form.submit()" class="px-2 py-1 border border-gray-300 rounded-md">
                                                <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>