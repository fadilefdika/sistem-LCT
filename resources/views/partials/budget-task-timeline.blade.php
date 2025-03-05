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
        <table class="min-w-full table-auto mt-10 border-collapse">
            <thead>
                <tr class="bg-gray-200 text-gray-700 text-left">
                    <th class="px-4 py-2 border text-center">No</th>
                    <th class="px-4 py-2 border">Task Name</th>
                    <th class="px-4 py-2 border">Status Task</th>
                    <th class="px-4 py-2 border">Due Date</th>
                    <th class="px-4 py-2 border text-center">Validasi EHS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $index => $task)
                    <tr class="border-b hover:bg-gray-100 transition">
                        <td class="px-4 py-2 border text-center">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 border">{{ $task->task_name }}</td>
                        <td class="px-4 py-2 border">{{ ucfirst($task->status_task) }}</td>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                        <td class="px-4 py-2 border text-center">
                            <input type="checkbox" class="cursor-pointer" {{ $task->validate_by_ehs ? 'checked' : '' }} disabled>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-2 border text-center text-gray-500">Tidak ada task tersedia</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>    
</div>
