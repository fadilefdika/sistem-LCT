<!-- HTML utama -->
<div class="overflow-x-auto bg-white p-4 shadow-sm rounded-lg border border-gray-200">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-medium text-gray-800">Area List</h2>
        <button id="openModal" class="bg-blue-600 text-white text-xs py-2 px-3 rounded-md hover:bg-blue-500 transition cursor-pointer">
            + Add Area
        </button>        
    </div>

    <div class="border border-gray-300 rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-gray-700 text-sm font-medium">
                <tr>
                    <th class="py-2 px-4 text-left">No</th>
                    <th class="py-2 px-4 text-left">Area Name</th>
                    <th class="py-2 px-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white text-sm">
                @foreach ($areas as $index => $area)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4">{{ ($areas->currentPage() - 1) * $areas->perPage() + $index + 1 }}</td>
                        <td class="py-3 px-4">{{ $area->nama_area }}</td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex justify-center space-x-2">
                                <button onclick="openModal(true, {
                                    id: {{ $area->id }},
                                    nama_area: '{{ $area->nama_area }}',
                                })"
                                    class="bg-yellow-500 text-white py-1.5 px-3 rounded hover:bg-yellow-400 transition text-xs cursor-pointer">
                                    ✏️ Edit
                                </button>
                            
                                <button onclick="deletearea({{ $area->id }})"
                                    class="bg-red-500 text-white py-1.5 px-3 rounded hover:bg-red-400 transition text-xs cursor-pointer">
                                    🗑️ Delete
                                </button>
                            </div>                            
                        </td>
                    </tr>
                @endforeach
            </tbody>            
        </table>

        <div class="mt-3 flex flex-col sm:flex-row justify-between items-center border-t px-4 py-3 text-gray-700 text-sm">
            <span>Showing {{ $areas->firstItem() }} - {{ $areas->lastItem() }} of {{ $areas->total() }} records</span>
            <div class="mt-2 sm:mt-0">{{ $areas->links('pagination::tailwind') }}</div>
        </div>        
    </div>

    <!-- Modal Tambah/Edit Area -->
    <div id="areaModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 px-4 z-50 hidden">
        <div class="bg-white p-5 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-lg font-medium text-gray-800 mb-3">
                <span id="modalTitle">Add Area</span>
            </h2>

            <!-- Form -->
            <form id="areaForm">
                @csrf
                <input type="hidden" id="areaId" name="area_id">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Area Name</label>
                    <input type="text" id="namaArea" name="nama_area" class="w-full border border-gray-300 rounded-md p-2 mt-1 text-sm focus:ring focus:ring-blue-200" placeholder="Enter area Name...">
                </div>

                <div class="mt-4 flex justify-end space-x-2">
                    <button type="button" id="closeModal" class="px-4 py-2 bg-gray-400 text-white rounded-md text-sm hover:bg-gray-500 cursor-pointer">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-500 cursor-pointer">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPT -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        function openModal(editMode = false, area = null) {
            const modal = document.getElementById("areaModal");
            modal.classList.remove("hidden");

            if (editMode) {
                document.getElementById("modalTitle").textContent = "Edit Area";
                document.getElementById("areaId").value = area.id;
                document.getElementById("namaArea").value = area.nama_area;
            } else {
                document.getElementById("modalTitle").textContent = "Add Area";
                document.getElementById("areaId").value = "";
                document.getElementById("namaArea").value = "";
            }
        }

        window.openModal = openModal;

        document.getElementById("closeModal").addEventListener("click", function () {
            document.getElementById("areaModal").classList.add("hidden");
        });

        document.getElementById("openModal").addEventListener("click", function () {
            openModal(false);
        });

        document.getElementById("areaForm").addEventListener("submit", async function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            let areaId = document.getElementById("areaId").value;
            let url = areaId ? `/master-data/area-data/${areaId}` : "/master-data/area-data";

            if (areaId) {
                formData.append('_method', 'PUT'); // Untuk Laravel PUT
            }

            try {
                let response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                let result = await response.json();
                document.getElementById("areaModal").classList.add("hidden");

                if (response.ok) {
                    Swal.fire({
                        title: "Success!",
                        text: "Area has been successfully saved!",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: "Error!",
                        text: result.message || "An error occurred while saving the data.",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            } catch (error) {
                console.error("Error saving area:", error);
                Swal.fire("Error!", "Failed to save area.", "error");
            }
        });
    });

    function deletearea(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "Deleted areas cannot be restored!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "Cancel"
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    let response = await fetch(`/master-data/area-data/${id}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (response.ok) {
                        Swal.fire("Deleted!", "The area has been successfully deleted.", "success")
                            .then(() => location.reload());
                    } else {
                        Swal.fire("Error!", "Failed to delete area.", "error");
                    }
                } catch (error) {
                    console.error("Error deleting area:", error);
                    Swal.fire("Error!", "An error occurred while deleting the data.", "error");
                }
            }
        });
    }
</script>
