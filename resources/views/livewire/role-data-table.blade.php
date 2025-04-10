<div class="overflow-x-auto bg-white p-6 shadow-md rounded-xl flex flex-col gap-6">
    <!-- Add PIC Button -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-700">Manage PIC</h2>
        <button id="openModal" class="bg-blue-600 text-white py-2 px-4 text-xs rounded-lg shadow-sm hover:bg-blue-700 transition-all cursor-pointer">
            + Add PIC
        </button>
    </div>

    <!-- Table -->
    <div class="border border-gray-300 rounded-lg overflow-hidden">
        <div class="overflow-x-auto rounded-lg border border-gray-300 overflow-hidden"> 
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead class="bg-gray-100 text-gray-600 text-sm font-semibold border-b border-gray-300">
                    <tr>
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Department</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white text-sm">
                    @foreach($data as $index => $row)
                        <tr class="hover:bg-gray-50 transition duration-200 ease-in-out">
                            <td class="py-3 px-4 text-gray-800">{{ $pics->firstItem() + $index }}</td>
                            <td class="py-3 px-4 text-gray-800">{{ $row['user_name'] }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $row['user_email'] }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $row['departments'] }}</td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <!-- Tombol Edit -->
                                    <button onclick="openModal(true, {
                                        id: {{ $row['id'] ?? 'null' }},
                                        pic_id: {{ $row['pic_id'] ?? 'null' }},
                                        department_id: {{ $row['department_id'] ?? 'null' }},
                                        department_name: '{{ $row['departments'] ?? '' }}',
                                        user_id: {{ $row['user_id'] ?? 'null' }},
                                        user_name: '{{ $row['user_name'] ?? '' }}',
                                    })"
                                    class="flex items-center gap-1 text-blue-600 border border-blue-600 rounded-md px-3 py-1.5 transition-all duration-200 ease-in-out 
                                        hover:bg-blue-600 hover:text-white hover:shadow-md hover:scale-105 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536l-10.036 10.036H7v-3.036L16.464 3.464z" />
                                        </svg>
                                        Edit
                                    </button>                                
                                    <!-- Tombol Delete -->
                                    <button onclick="deletePic({{ $row['id'] }})"
                                    class="flex items-center gap-1 text-red-600 border border-red-600 rounded-md px-3 py-1.5 transition-all duration-200 ease-in-out 
                                        hover:bg-red-600 hover:text-white hover:shadow-md hover:scale-105 cursor-pointer"
                                        data-id="{{ $row['id'] }}" 
                                        aria-label="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                </tbody>            
            </table>
            
            
        </div>
        <!-- Pagination -->
        <div class="mt-4 flex justify-between items-center border-t px-5 py-3 text-gray-600 text-sm">
            <span>Showing {{ $pics->firstItem() }} to {{ $pics->lastItem() }} of {{ $pics->total() }} entries</span>
            <div>{{ $pics->links('pagination::tailwind') }}</div>
        </div>

    </div>
    <!-- Modal Tambah/Edit PIC -->
<div id="picModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 px-4 z-50 hidden">
    <div class="bg-white p-5 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-lg font-medium text-gray-800 mb-3">
            <span id="modalTitle">Add PIC</span>
        </h2>

        <form id="picForm">
            @csrf
            <input type="hidden" id="picId" name="pic_id">

            <!-- Search PIC -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Pic</label>

                <div class="relative">
                    <input type="text" id="searchUser" class="w-full pr-10 border border-gray-300 rounded-md p-2 mt-1 text-sm focus:ring focus:ring-blue-200" placeholder="Search PIC...">

                    <!-- Loading Spinner -->
                    <div id="loadingSpinnerUser" class="absolute inset-y-0 right-3 flex items-center hidden">
                        <svg class="animate-spin h-5 w-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 2v4M12 22v-4M2 12h4M22 12h-4"></path>
                        </svg>
                    </div>

                    <!-- Dropdown User List -->
                    <ul id="userList" class="absolute left-0 w-full border border-gray-300 rounded-md mt-1 bg-white shadow-lg hidden z-50 max-h-48 overflow-auto"></ul>
                </div>

                <input type="hidden" id="selectedUserId" name="user_id">
            </div>

            <!-- Search Department -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">Department</label>

                <div class="relative">
                    <input type="text" id="searchDepartment" class="w-full pr-10 border border-gray-300 rounded-md p-2 mt-1 text-sm focus:ring focus:ring-blue-200" placeholder="Search Department...">

                    <!-- Loading Spinner -->
                    <div id="loadingSpinnerDept" class="absolute inset-y-0 right-3 flex items-center hidden">
                        <svg class="animate-spin h-5 w-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 2v4M12 22v-4M2 12h4M22 12h-4"></path>
                        </svg>
                    </div>

                    <!-- Dropdown Department List -->
                    <ul id="departmentList" class="absolute left-0 w-full border border-gray-300 rounded-md mt-1 bg-white shadow-lg hidden z-50 max-h-48 overflow-auto"></ul>
                </div>

                <input type="hidden" id="selectedDepartmentId" name="department_id">
            </div>

            <!-- Buttons -->
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" id="closeModal" class="px-4 py-2 bg-gray-400 text-white rounded-md text-sm hover:bg-gray-500 cursor-pointer">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-500 cursor-pointer">Save</button>
            </div>
        </form>
    </div>
</div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        function openModal(editMode = false, pic = null) {
            const modal = document.getElementById("picModal");
            modal.classList.remove("hidden");
    
            if (editMode) {
                document.getElementById("modalTitle").textContent = "Edit PIC";
                document.getElementById("picId").value = pic.id;
                document.getElementById("searchUser").value = pic.user_name;
                document.getElementById("selectedUserId").value = pic.user_id;
                document.getElementById("searchDepartment").value = pic.department_name;
                document.getElementById("selectedDepartmentId").value = pic.department_id;
            } else {
                document.getElementById("modalTitle").textContent = "Add PIC";
                document.getElementById("picId").value = "";
                document.getElementById("searchUser").value = "";
                document.getElementById("selectedUserId").value = "";
                document.getElementById("searchDepartment").value = "";
                document.getElementById("selectedDepartmentId").value = "";
            }
        }
    
        window.openModal = openModal;
    
        document.getElementById("closeModal").addEventListener("click", function () {
            document.getElementById("picModal").classList.add("hidden");
        });
    
        document.getElementById("searchUser").addEventListener("input", async function () {
            let query = this.value.trim();
            let userList = document.getElementById("userList");
            let loadingSpinnerUser = document.getElementById("loadingSpinnerUser");

            // Simpan reference untuk membatalkan fetch sebelumnya
            if (window.searchUserController) {
                window.searchUserController.abort();
            }
            window.searchUserController = new AbortController();
            let { signal } = window.searchUserController;

            if (query.length >= 3) {
                try {
                    userList.innerHTML = "";
                    userList.classList.remove("hidden");
                    loadingSpinnerUser.classList.remove("hidden");

                    let responseUsers = await fetch(`/master-data/role-data/search-users?q=${query}`, { signal });
                    let dataUsers = await responseUsers.json();

                    loadingSpinnerUser.classList.add("hidden");
                    userList.innerHTML = ""; // Kosongkan list sebelum menambahkan hasil baru

                    if (dataUsers.length > 0) {
                        dataUsers.forEach(user => {
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
                    if (error.name !== "AbortError") {
                        console.error("Error fetching users:", error);
                        userList.innerHTML = '<li class="p-2 text-red-500">Terjadi kesalahan</li>';
                        loadingSpinnerUser.classList.add("hidden");
                    }
                }
            } else {
                userList.innerHTML = "";
                userList.classList.add("hidden");
                loadingSpinnerUser.classList.add("hidden");
            }
        });

        document.getElementById("searchDepartment").addEventListener("input", async function () {
            let query = this.value.trim();
            let departmentList = document.getElementById("departmentList");
            let loadingSpinnerDept = document.getElementById("loadingSpinnerDept");

            // Simpan reference untuk membatalkan fetch sebelumnya
            if (window.searchDepartmentController) {
                window.searchDepartmentController.abort();
            }
            window.searchDepartmentController = new AbortController();
            let { signal } = window.searchDepartmentController;

            if (query.length >= 1) {
                try {
                    departmentList.innerHTML = "";
                    departmentList.classList.remove("hidden");
                    loadingSpinnerDept.classList.remove("hidden");

                    let response = await fetch(`/master-data/role-data/search-department?q=${query}`, { signal });
                    let dataDepartments = await response.json();

                    loadingSpinnerDept.classList.add("hidden");
                    departmentList.innerHTML = ""; // Kosongkan sebelum menambahkan hasil baru

                    if (dataDepartments.length > 0) {
                        dataDepartments.forEach(dept => {
                            let li = document.createElement("li");
                            li.className = "p-2 cursor-pointer hover:bg-gray-200";
                            li.textContent = dept.nama_departemen; // Perbaikan dari user.fullname
                            li.dataset.id = dept.id;
                            li.addEventListener("click", function () {
                                document.getElementById("selectedDepartmentId").value = this.dataset.id;
                                document.getElementById("searchDepartment").value = this.textContent;
                                departmentList.innerHTML = "";
                                departmentList.classList.add("hidden");
                            });
                            departmentList.appendChild(li);
                        });
                    } else {
                        departmentList.innerHTML = '<li class="p-2 text-gray-500">Tidak ada hasil</li>';
                    }
                } catch (error) {
                    if (error.name !== "AbortError") {
                        console.error("Error fetching departments:", error);
                        departmentList.innerHTML = '<li class="p-2 text-red-500">Terjadi kesalahan</li>';
                        loadingSpinnerDept.classList.add("hidden");
                    }
                }
            } else {
                departmentList.innerHTML = "";
                departmentList.classList.add("hidden");
                loadingSpinnerDept.classList.add("hidden");
            }
        });
    
        document.getElementById("picForm").addEventListener("submit", async function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            let picId = document.getElementById("picId").value;
            let url = picId ? `/master-data/role-data/${picId}` : "/master-data/role-data";
            let method = picId ? "PUT" : "POST";

            console.log("Data sebelum dikirim:");
            formData.forEach((value, key) => console.log(key, value)); // Menampilkan data FormData

    
            try {
                let options = {
                    method: method,
                    body: formData
                };

                console.log(options);

                if (method === "PUT") {
                    let jsonData = {};
                    formData.forEach((value, key) => (jsonData[key] = value));
                    options.body = JSON.stringify(jsonData);
                    options.headers = { "Content-Type": "application/json" };
                }

                let response = await fetch(url, options);
                let result = await response.json();
                

                document.getElementById("picModal").classList.add("hidden"); // **Close modal earlier**

                if (response.ok) {
                    Swal.fire({
                        title: "Success!",
                        text: "Department has been successfully saved!",
                        icon: "success",
                        confirmButtonText: "OK",
                        timer: 2000, // Modal otomatis tertutup setelah 2 detik
                        timerProgressBar: true
                    }).then(() => {
                        setTimeout(() => {
                            location.reload(); // Refresh page setelah modal tertutup
                        }, 500); // Delay sedikit sebelum reload agar transisi lebih smooth
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
                    text: "Failed to save data. Check the console for details.",
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

    function deletePic(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "Deleted pic cannot be restored!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "Cancel"
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    let response = await fetch(`/master-data/role-data/${id}`, {
                        method: "DELETE",
                        headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }
                    });

                    if (response.ok) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "The pic has been successfully deleted.",
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