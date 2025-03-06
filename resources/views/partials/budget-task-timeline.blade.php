<div class="bg-white px-6 pt-6 pb-16 rounded-lg shadow-lg relative" x-data="{ showForm: false }">
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

            <table class="min-w-full bg-white rounded-lg">
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
                                <div class="relative">
                                    <button 
                                        class="status-button appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 text-gray-700 focus:ring-2 focus:outline-none shadow-sm w-full text-left cursor-pointer flex justify-between items-center"
                                        data-task-id="{{ $task->id }}" 
                                        data-laporan-id="{{ $task->id_laporan_lct }}"
                                    >
                                        {{ ucfirst(str_replace('_', ' ', $task->status_task)) }}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06-.02L10 10.586l3.71-3.71a.75.75 0 011.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 01-.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                
                                    <!-- Dropdown Status -->
                                    <ul
                                        class="status-menu hidden absolute left-0 top-full z-50 mt-1 w-full rounded-lg border border-gray-300 bg-white p-2 shadow-lg max-h-[200px] overflow-auto"
                                        data-task-id="{{ $task->id }}"
                                    >
                                        <li class="status-option cursor-pointer text-gray-800 flex w-full text-sm items-center rounded-md p-3 transition-all hover:bg-gray-200 mb-1" data-status="pending">
                                            Pending
                                        </li>
                                        <li class="status-option cursor-pointer text-gray-800 flex w-full text-sm items-center rounded-md p-3 transition-all hover:bg-gray-200 mb-1" data-status="in_progress">
                                            In Progress
                                        </li>
                                        <li class="status-option cursor-pointer text-gray-800 flex w-full text-sm items-center rounded-md p-3 transition-all hover:bg-gray-200" data-status="completed">
                                            Completed
                                        </li>
                                    </ul>
                                </div>
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

            <div class="mt-4 flex justify-end">
                <button class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Selesai
                </button>
            </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Toggle dropdown saat tombol diklik
        document.querySelectorAll(".status-button").forEach((button) => {
            button.addEventListener("click", function (event) {
                event.stopPropagation(); // Mencegah event bubbling ke document

                const dropdown = this.nextElementSibling;
                document.querySelectorAll(".status-menu").forEach((menu) => {
                    if (menu !== dropdown) {
                        menu.classList.add("hidden");
                    }
                });

                dropdown.classList.toggle("hidden");

                // Hitung posisi dropdown
                const dropdownRect = dropdown.getBoundingClientRect();
                const viewportHeight = window.innerHeight;

                if (dropdownRect.bottom > viewportHeight) {
                    dropdown.classList.add("bottom-full");
                    dropdown.classList.remove("top-full");
                } else {
                    dropdown.classList.add("top-full");
                    dropdown.classList.remove("bottom-full");
                }
            });
        });

        // Event delegation untuk menangani klik pada opsi status
        document.addEventListener("click", async function (event) {
            const option = event.target.closest(".status-option");
            if (!option) return;

            const newStatus = option.getAttribute("data-status");
            const dropdown = option.closest("ul");
            const button = dropdown.previousElementSibling;
            const taskId = button.getAttribute("data-task-id");
            const laporanId = button.getAttribute("data-laporan-id");

            // Update tampilan tombol
            button.innerHTML = `
                ${newStatus.replace("_", " ")}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06-.02L10 10.586l3.71-3.71a.75.75 0 011.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 01-.02-1.06z" clip-rule="evenodd" />
                </svg>
            `;

            // Tutup dropdown
            dropdown.classList.add("hidden");

            // Kirim update status ke server
            const updateUrl = `http://127.0.0.1:8000/manajemen-lct/${taskId}/updateStatus`;

            try {
                const response = await fetch(updateUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                    body: JSON.stringify({ status: newStatus }),
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                console.log("Status updated successfully:", newStatus);

            } catch (error) {
                console.error("Fetch Error:", error);
            }
        });

        // Menutup dropdown jika klik di luar elemen terkait
        document.addEventListener("click", function (event) {
            document.querySelectorAll(".status-menu").forEach((menu) => {
                if (!event.target.closest(".status-button") && !event.target.closest(".status-menu")) {
                    menu.classList.add("hidden");
                }
            });
        });
    });
</script>
