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
    @if(!in_array($laporan->status_lct, ['approved_permanent', 'waiting_approval_permanent', 'closed']))
    <div x-show="!isApproved">
        <form action="{{ route('admin.manajemen-lct.submitTaskBudget', ['id_laporan_lct' => $laporan->id_laporan_lct]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="deletedTasks" id="deletedTasksInput">

            <div class="bg-white px-6 pt-6 pb-6 rounded-lg shadow-lg mb-4">
                <h3 class="text-lg font-semibold mb-4">Task Management and Timeline</h3>
                
                <div class="overflow-x-auto"> <!-- Wrapping the table in a div for horizontal scrolling -->
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-center">No</th>
                                <th class="border px-3 py-2 text-left">Task Name</th>
                                <th class="border px-3 py-2 text-left">PIC</th>
                                <th class="border px-3 py-2 text-left">Due Date</th>
                                <th class="border px-3 py-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody> 
                            <template x-for="(task, index) in tasks" :key="index">
                                <tr>
                                    <td class="border px-3 py-2 text-center" x-text="index + 1"></td>
                                    <input type="hidden" x-model="task.id" :name="'tasks['+index+'][id]'">
                                
                                    <!-- Task Name -->
                                    <td class="border">
                                        <input type="text" x-model="task.taskName"
                                            @click="addRow(index)"
                                            class="w-full border-gray-100"
                                            :name="'tasks['+index+'][taskName]'"
                                            placeholder="Create a New Task...">
                                    </td>
                            
                                    <!-- PIC Selection -->
                                    <td class="border">
                                        <select 
                                            class="w-full border-gray-100"
                                            x-model="task.picId" 
                                            :name="'tasks['+index+'][picId]'">
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
            
                                    <td class="border text-center">
                                        <button type="button" @click="removeTask(index)" class="text-red-600">×</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div> <!-- End of overflow-x-auto -->
                
                <div class="mt-4 p-4 bg-gray-100 rounded-lg" 
                    x-data="{
                        estimatedBudget: '{{ intval($laporan->estimated_budget ?? 0) }}', // Pastikan jadi integer
                        formattedBudget: '',
                        showError: false,
                        formatCurrency() {
                            this.formattedBudget = this.estimatedBudget.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.' );
                        }
                    }"
                    x-init="formatCurrency()">
                    <h3 class="text-lg font-semibold mb-2">Estimated Budget</h3>
                    <div class="flex items-center">
                        <span class="font-medium mr-3">Estimated Budget (Rp):</span>
                        <input 
                            type="text" 
                            x-model="formattedBudget" 
                            class="w-40 p-2 border border-gray-300 rounded-lg text-right" 
                            @input="
                                estimatedBudget = $event.target.value.replace(/\D/g, '' );
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
            
                <div x-data="fileUpload" class="mt-6 p-4 border rounded-lg shadow-md bg-white">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">Attachments</h3>
                    
                    <!-- Existing Attachments -->
                    @php
                        $existingAttachments = json_decode($laporan->attachments ?? '[]', true);
                    @endphp
                    
                    @if (!empty($existingAttachments))
                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-700 mb-2">Submitted Documents</p>
                        <ul class="list-disc pl-5 text-sm text-gray-600 space-y-2">
                            @foreach ($existingAttachments as $index => $attachment)
                                <li class="flex items-center justify-between">
                                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="text-blue-600 underline hover:text-blue-800">
                                        {{ $attachment['original_name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>                    
                    @endif
                    
                    <!-- Custom File Upload -->
                    <label for="file-upload" class="block mb-2 text-sm font-medium text-gray-700">Upload New Files</label>
                    
                    <!-- Upload Input -->
                    <div class="flex items-center space-x-4">
                        <input 
                            type="file" 
                            name="attachments[]"
                            id="file-upload"
                            multiple
                            accept="application/pdf, image/*"
                            class="hidden"
                            @change="handleFileChange"
                        />
                        <label for="file-upload" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg cursor-pointer hover:bg-blue-700 transition duration-300">
                            Choose Files
                        </label>
                    </div>
                    
                    <!-- Display selected file names -->
                    <div x-show="selectedFiles.length > 0" class="mt-4">
                        <ul class="list-disc pl-5 text-sm text-gray-600 space-y-1">
                            <template x-for="(file, index) in selectedFiles" :key="index">
                                <li x-text="file.name"></li>
                            </template>
                        </ul>
                    </div>
                    
                    @if($laporan->status_lct == 'approved_temporary')
                    <!-- Feedback Text -->
                    <p class="text-sm text-gray-500 mt-2">You can upload multiple files (PDF, Images).</p>
                    @endif
                </div>
                
                
                <!-- Submit button -->
                @if(in_array($laporan->status_lct ?? '', ['waiting_approval_temporary','approved_temporary', 'taskbudget_revision']))
                <div class="flex justify-end">
                    <button type="submit" class="text-white bg-blue-700 px-5 py-3 rounded-lg mt-4 cursor-pointer">
                        Send to Approver
                    </button>
                </div>
                @endif
            </div>
            
        </form>
    </div>
@endif


<!-- Approved Tasks Wrapper -->
@if(in_array($laporan->status_lct, ['approved_taskbudget','waiting_approval_permanent', 'approved_permanent', 'closed']))
    <div class="mt-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">✅ Approved Tasks List</h3>

        {{-- Status Info --}}
        @if($laporan->status_lct === 'waiting_approval_permanent')
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
            {{-- Estimated Budget --}}
            @if($laporan->estimated_budget)
                <div class="mb-6">
                    <div class="border border-gray-300 rounded-lg bg-gray-50 px-6 py-4 shadow-sm">
                        <h4 class="text-base font-bold text-gray-900 mb-1 uppercase tracking-wide">
                            Estimated Budget
                        </h4>
                        <p class="text-lg text-gray-800 font-semibold">
                            Rp {{ number_format($laporan->estimated_budget, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            @endif



            <!-- Task Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold min-w-[50px]">No</th>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold min-w-[200px]">Task Name</th>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold min-w-[150px]">PIC Name</th>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold min-w-[150px]">Due Date</th>
                            <th class="px-4 py-2 text-left text-gray-900 font-semibold min-w-[150px]">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $index => $task)
                            @if(!empty($task['taskName']) && !empty($task['picId']) && !empty($task['dueDate']))
                                <tr class="hover:bg-gray-100 transition duration-200">
                                    <td class="px-4 py-2 text-gray-900">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2 text-gray-900 break-words max-w-[200px]">{{ $task['taskName'] }}</td>
                                    <td class="px-4 py-2 text-gray-600">
                                        @php
                                            $pic = $picList->firstWhere('id', $task['picId']);
                                        @endphp
                                        {{ $pic ? $pic['fullname'] : 'No PIC Assigned' }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-600">
                                        {{ \Carbon\Carbon::parse($task['dueDate'])->format('F j, Y') }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input type="checkbox"
                                                data-task-id="{{ $task['id'] }}"
                                                class="status-checkbox form-checkbox h-5 w-5 text-green-600"
                                                {{ $task['status'] == 'completed' ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">
                                                {{ $task['status'] == 'completed' ? '✅ Completed' : '⏳ Pending' }}
                                            </span>
                                        </label>
                                    </td>                                    
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Submitted Documents -->
            <div x-data="fileUpload" class="mt-6 p-4 border rounded-lg shadow-md bg-white">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Submitted Documents</h3>

                @php
                    $existingAttachments = json_decode($laporan->attachments ?? '[]', true);
                @endphp

                @if (!empty($existingAttachments))
                    <div class="mb-6">
                        <ul class="list-disc pl-5 text-sm text-gray-600 space-y-2">
                            @foreach ($existingAttachments as $index => $attachment)
                                <li class="flex items-center justify-between">
                                    <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="text-blue-600 underline hover:text-blue-800">
                                        {{ $attachment['original_name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="text-sm text-gray-500 mb-4">There are no submitted documents associated with this task.</p>
                @endif
            </div>
        </div>
    </div>
@endif


<a href="{{ route('admin.manajemen-lct.history', $laporan->id_laporan_lct) }}" class="inline-block">
    <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
        <i class="fas fa-history mr-2"></i>History
    </button>
</a>

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
                this.tasks.push({ taskName: '', picId: '', dueDate: '', attachment: '' });
            }
        },

        addRow(index) {
            if (index === this.tasks.length - 1) {
                this.tasks.push({ taskName: '', picId: '', dueDate: '', attachment: '' });
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
    document.addEventListener('alpine:init', () => {
        Alpine.data('fileUpload', () => ({
            selectedFiles: [],

            handleFileChange(event) {
                this.selectedFiles = Array.from(event.target.files);
            }
        }));
    });
</script>



<script>
    document.querySelectorAll('.status-checkbox').forEach((checkbox) => {
        checkbox.addEventListener('change', async function () {
            const taskId = this.getAttribute('data-task-id');
            const isChecked = this.checked;
            const newStatus = isChecked ? 'completed' : 'pending';
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

                // Update label teks sesuai status
                const label = this.closest('label').querySelector('span');
                label.textContent = isChecked ? '✅ Completed' : '⏳ Pending';

                // Efek sukses
                this.closest('td').classList.add('bg-green-100');
                setTimeout(() => this.closest('td').classList.remove('bg-green-100'), 2000);

            } catch (error) {
                console.error("Failed to update status:", error);
                alert("Gagal memperbarui status. Silakan coba lagi.");
            }
        });
    });
</script>

