<div class="overflow-x-auto bg-white p-4 shadow-sm rounded-lg border border-gray-200">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-medium text-gray-800">Department List</h2>
        <button id="openModal" class="bg-blue-600 text-white text-xs py-2 px-3 rounded-md hover:bg-blue-500 transition cursor-pointer">
            + Add Department
        </button>        
    </div>

    
    <div class="border border-gray-300 rounded-lg overflow-hidden">
        <div class="rounded-lg overflow-x-auto border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-gray-700 text-sm font-medium">
                    <tr>
                        <th class="py-2 px-4 text-left">No</th>
                        <th class="py-2 px-4 text-left">Manajer Name</th>
                        <th class="py-2 px-4 text-left">Department Name</th>
                        <th class="py-2 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white text-sm">
                    @foreach ($departments as $index => $department)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4">{{ ($departments->currentPage() - 1) * $departments->perPage() + $index + 1 }}</td>
                            <td class="py-3 px-4">{{ optional($department->user)->fullname ?? '-' }}</td>
                            <td class="py-3 px-4">{{ $department->nama_departemen }}</td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="openModal(true, {
                                        id: {{ $department->id }},
                                        nama_departemen: '{{ $department->nama_departemen }}',
                                        user_id: '{{ $department->user_id }}',
                                        manager_name: '{{ $department->user->fullname ?? '' }}'
                                    })"
                                        class="bg-yellow-500 text-white py-1.5 px-3 rounded hover:bg-yellow-400 transition text-xs cursor-pointer">
                                        ✏️ Edit
                                    </button>
                                
                                    <button onclick="deleteDepartment({{ $department->id }})"
                                        class="bg-red-500 text-white py-1.5 px-3 rounded hover:bg-red-400 transition text-xs cursor-pointer">
                                        🗑️ Delete
                                    </button>
                                    
                                </div>                            
                            </td>
                        </tr>
                    @endforeach
                </tbody>            
            </table>
            
        </div>
        <div class="mt-3 flex flex-col sm:flex-row justify-between items-center border-t px-4 py-3 text-gray-700 text-sm">
            <span>Showing {{ $departments->firstItem() }} - {{ $departments->lastItem() }} of {{ $departments->total() }} records</span>
            <div class="mt-2 sm:mt-0">{{ $departments->links('pagination::tailwind') }}</div>
        </div>        
    </div>

<!-- Modal Tambah/Edit Departemen -->
<div id="departmentModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 px-4 z-50 hidden">
    <div class="bg-white p-5 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-lg font-medium text-gray-800 mb-3">
            <span id="modalTitle">Add Department</span>
        </h2>

        <!-- Form -->
        <form id="departmentForm">
            @csrf
            <input type="hidden" id="departmentId" name="department_id">

            <!-- Nama Departemen -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Department Name</label>
                <input type="text" id="namaDepartemen" name="nama_departemen" class="w-full border border-gray-300 rounded-md p-2 mt-1 text-sm focus:ring focus:ring-blue-200" placeholder="Enter Department Name...">
            </div>

            <!-- Search User (Manager) -->
            <div class="mt-4 relative">
                <label class="block text-sm font-medium text-gray-700">Manager</label>
                <input type="text" id="searchUser" class="w-full border border-gray-300 rounded-md p-2 mt-1 text-sm focus:ring focus:ring-blue-200" placeholder="Search Manajer...">
                
                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden">
                    <svg class="animate-spin h-5 w-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 2v4M12 22v-4M2 12h4M22 12h-4"></path>
                    </svg>
                </div>

                <!-- Dropdown User List -->
                <ul id="userList" class="absolute left-0 w-full border border-gray-300 rounded-md mt-1 bg-white shadow-lg hidden z-50 max-h-48 overflow-auto"></ul>

                <input type="hidden" id="selectedUserId" name="user_id">
            </div>


            <!-- Tombol -->
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" id="closeModal" class="px-4 py-2 bg-gray-400 text-white rounded-md text-sm hover:bg-gray-500 cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-500 cursor-pointer">Save</button>
            </div>
        </form>
    </div>
</div>

</div>

<script>
    @php
         $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
         $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');
     @endphp

         // Kirim role ke JS
      const userRole = "{{ $roleName }}";
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        function openModal(editMode = false, department = null) {
            const modal = document.getElementById("departmentModal");
            modal.classList.remove("hidden");
    
            if (editMode) {
                document.getElementById("modalTitle").textContent = "Edit Departemen";
                document.getElementById("departmentId").value = department.id;
                document.getElementById("namaDepartemen").value = department.nama_departemen;
                document.getElementById("selectedUserId").value = department.user_id;
                document.getElementById("searchUser").value = department.manager_name;
            } else {
                document.getElementById("modalTitle").textContent = "Tambah Departemen";
                document.getElementById("departmentId").value = "";
                document.getElementById("namaDepartemen").value = "";
                document.getElementById("selectedUserId").value = "";
                document.getElementById("searchUser").value = "";
            }
        }

        window.openModal = openModal;
    
        document.getElementById("closeModal").addEventListener("click", function () {
            document.getElementById("departmentModal").classList.add("hidden");
        });
    
        document.getElementById("searchUser").addEventListener("input", async function () {
            let query = this.value;
            let userList = document.getElementById("userList");
            let loadingSpinner = document.getElementById("loadingSpinner");
    
            if (query.length >= 3) {
                try {
                    userList.innerHTML = "";
                    userList.classList.remove("hidden");
                    loadingSpinner.classList.remove("hidden");

                    let baseUrl;
                    if (userRole === 'ehs') {
                        baseUrl = '/ehs/master-data/department-data';
                    } else if (userRole === 'manajer') {
                        baseUrl = '/admin/master-data/department-data';
                    } else {
                        baseUrl = '/master-data/department-data';
                    }

                    let response = await fetch(`${baseUrl}/search-users?q=${query}`);
                    let data = await response.json();
    
                    userList.innerHTML = "";
                    loadingSpinner.classList.add("hidden");
    
                    if (data.length > 0) {
                        data.forEach(user => {
                            let li = document.createElement("li");
                            li.className = "p-2 cursor-pointer hover:bg-gray-200";
                            li.textContent = user.fullname;
                            li.dataset.id = user.id;
                            li.addEventListener("click", function () {
                                document.getElementById("selectedUserId").value = this.dataset.id;
                                document.getElementById("searchUser").value = this.textContent;
                                userList.innerHTML = "";
                                userList.classList.add("hidden");
                            });
                            userList.appendChild(li);
                        });
                    } else {
                        userList.innerHTML = '<li class="p-2 text-gray-500">Tidak ada hasil</li>';
                    }
                } catch (error) {
                    console.error("Error fetching users:", error);
                    userList.innerHTML = '<li class="p-2 text-red-500">Terjadi kesalahan</li>';
                    loadingSpinner.classList.add("hidden");
                }
            } else {
                userList.innerHTML = "";
                userList.classList.add("hidden");
                loadingSpinner.classList.add("hidden");
            }
        });
    
        document.getElementById("departmentForm").addEventListener("submit", async function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            let departmentId = document.getElementById("departmentId").value;
            let baseUrl;
            if (userRole === 'ehs') {
                baseUrl = '/ehs/master-data/department-data';
            } else if (userRole === 'manajer') {
                baseUrl = '/admin/master-data/department-data';
            } else {
                baseUrl = '/master-data/department-data';
            }

            let url = departmentId ? `${baseUrl}/${departmentId}` : baseUrl;
            let method = departmentId ? "PUT" : "POST";
    
            try {
                let options = {
                    method: method,
                    body: formData
                };

                if (method === "PUT") {
                    let jsonData = {};
                    formData.forEach((value, key) => (jsonData[key] = value));
                    options.body = JSON.stringify(jsonData);
                    options.headers = { "Content-Type": "application/json" };
                }

                let response = await fetch(url, options);
                let result = await response.json();

                document.getElementById("departmentModal").classList.add("hidden"); // **Close modal earlier**

                if (response.ok) {
                    Swal.fire({
                        title: "Success!",
                        text: "Department has been successfully saved!",
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
                Swal.fire({
                    title: "Failed!",
                    text: "Failed to save data.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
                console.error("Error saving department:", error);
            }
        });
    
        document.getElementById("openModal").addEventListener("click", function () {
            openModal(false);
        });
    });

    function deleteDepartment(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "Deleted departments cannot be restored!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "Cancel"
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    let baseUrl;
                    if (userRole === 'ehs') {
                        baseUrl = '/ehs/master-data/department-data';
                    } else if (userRole === 'manajer') {
                        baseUrl = '/admin/master-data/department-data';
                    } else {
                        baseUrl = '/master-data/department-data';
                    }

                    let response = await fetch(`${baseUrl}/${id}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    if (response.ok) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "The department has been successfully deleted.",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => location.reload());
                    } else {
                        Swal.fire("Error!", "Failed to delete department.", "error");
                    }
                } catch (error) {
                    console.error("Error deleting department:", error);
                    Swal.fire("Error!", "An error occurred while deleting the data.", "error");
                }
            }
        });
    }

</script>
    