<div class="bg-white p-6 rounded-lg shadow-lg relative" x-data="{ showForm: false }">
    <h3 class="text-lg font-semibold mb-4">Tasks and Timeline</h3>

    <!-- Button Tambah Task -->
    <button @click="showForm = true"
        class="absolute top-6 right-6 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
        + Tambah Task
    </button>


    <!-- Form Tambah Task -->
    <div x-show="showForm" x-transition 
        class="mt-6 p-4 bg-gray-100 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold mb-4">Tambah Task Baru</h3>
        <form action="{{ route('admin.manajemen-lct.storeTask', ['id_laporan_lct' => $laporan->id_laporan_lct]) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Task Name</label>
                <input type="text" name="task_name" required
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200">
            </div>

            <div class="mb-4" x-data>
                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                <input type="date" name="due_date" required
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200"
                    x-ref="datepicker"
                    @focus="$refs.datepicker.showPicker()">
            </div>            

            <!-- Tambahkan input hidden untuk status_task -->
            <input type="hidden" name="status_task" value="pending">

            <div class="flex justify-end space-x-4">
                <button type="button" @click="showForm = false"
                    class="px-4 py-2 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition cursor-pointer">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition cursor-pointer">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <div class="overflow-x-auto mt-10">
            <table class="min-w-full bg-white shadow-lg rounded-lg">
                <thead>
                    <tr class="text-gray-700 text-left border-b">
                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">No</th>
                        <th class="px-6 py-3 text-sm font-semibold text-gray-600 w-1/3">Task Name</th>
                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">Status Task</th>
                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">Due Date</th>
                        <th class="px-6 py-3 text-sm font-semibold text-gray-600">Validasi EHS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $index => $task)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-center">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm w-1/2">{{ $task->task_name }}</td>
                            <td class="px-6 py-4 text-sm">
                                <select class="status-dropdown border rounded px-2 py-1" data-task-id="{{ $task->id_laporan_lct }}">
                                    <option value="pending" {{ $task->status_task == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $task->status_task == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $task->status_task == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </td>
                            
                            <td class="px-6 py-4 text-sm">{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="cursor-pointer" {{ $task->validate_by_ehs ? 'checked' : '' }} disabled>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada task tersedia</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>            
        </div>        
    </div>    
</div>

<script>
    document.querySelectorAll('.status-dropdown').forEach((dropdown) => {
        dropdown.addEventListener('change', async function () {
            const taskId = dropdown.getAttribute('data-task-id');
            const newStatus = dropdown.value; // Ambil nilai status dari dropdown

            const updateUrl = `http://127.0.0.1:8000/manajemen-lct/${taskId}/updateStatus`;

            try {
                const response = await fetch(updateUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ status: newStatus }),
                });

                console.log("Response Status:", response.status);

                // Cek apakah responsenya berhasil (status 200 atau 201)
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                console.log("Response Data:", data);

            } catch (error) {
                console.error("Fetch Error:", error);
            }
        });
    });
</script>
