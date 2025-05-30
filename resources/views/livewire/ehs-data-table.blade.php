<!-- HEADER DAN TOMBOL -->
<div class="overflow-x-auto bg-white p-4 shadow-sm rounded-lg border border-gray-200">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-medium text-gray-800">Daftar EHS</h2>
        <button id="openModal" class="bg-blue-600 text-white text-xs py-2 px-3 rounded-md hover:bg-blue-500 transition cursor-pointer">
            + Tambah EHS
        </button>        
    </div>

    <!-- TABLE -->
    <div class="border border-gray-300 rounded-lg overflow-hidden">
        <div class="rounded-lg overflow-x-auto border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-gray-700 text-sm font-medium">
                    <tr>
                        <th class="py-2 px-4 text-left">No</th>
                        <th class="py-2 px-4 text-left">Nama EHS</th>
                        <th class="py-2 px-4 text-left">Username</th>
                        <th class="py-2 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white text-sm">
                    @foreach ($ehs as $index => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4">{{ ($ehs->currentPage() - 1) * $ehs->perPage() + $index + 1 }}</td>
                            <td class="py-3 px-4">{{ optional($item->user)->fullname ?? '-' }}</td>
                            <td class="py-3 px-4">{{ $item->username }}</td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex justify-center space-x-2">
                                    <button onclick="openModal(true, {
                                        id: {{ $item->id }},
                                        username: '{{ $item->username }}',
                                        user_id: '{{ $item->user_id }}',
                                        ehs_name: '{{ $item->user->fullname ?? '' }}'
                                    })"`
                                        class="bg-yellow-500 text-white py-1.5 px-3 rounded hover:bg-yellow-400 transition text-xs cursor-pointer">
                                        ✏️ Edit
                                    </button>
                                
                                    <button onclick="deleteehs({{ $item->id }})"
                                        class="bg-red-500 text-white py-1.5 px-3 rounded hover:bg-red-400 transition text-xs cursor-pointer">
                                        🗑️ Hapus
                                    </button>
                                </div>                            
                            </td>
                        </tr>
                    @endforeach
                </tbody>            
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="mt-3 flex flex-col sm:flex-row justify-between items-center border-t px-4 py-3 text-gray-700 text-sm">
            <span>Menampilkan {{ $ehs->firstItem() }} - {{ $ehs->lastItem() }} dari total {{ $ehs->total() }} data</span>
            <div class="mt-2 sm:mt-0">{{ $ehs->links('pagination::tailwind') }}</div>
        </div>        
    </div>



<!-- MODAL EHS -->
<div id="ehsModal" class="fixed inset-0 flex items-center justify-center bg-black/40 bg-opacity-50 px-4 z-60 hidden">
    <div class="bg-white p-5 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-lg font-medium text-gray-800 mb-3">
            <span id="modalTitle">Tambah EHS</span>
        </h2>

        <!-- FORM -->
        <form id="ehsForm">
            @csrf
            <input type="hidden" id="ehsId" name="ehs_id">

            <!-- Username -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="usernameInput" name="username" class="w-full border border-gray-300 rounded-md p-2 mt-1 text-sm focus:ring focus:ring-blue-200" placeholder="Masukkan username EHS...">
            </div>

            <!-- Cari Pengguna -->
            <div class="mt-4 relative">
                <label class="block text-sm font-medium text-gray-700">Nama Lengkap EHS</label>
                <input type="text" id="searchUser" class="w-full border border-gray-300 rounded-md p-2 mt-1 text-sm focus:ring focus:ring-blue-200" placeholder="Cari pengguna EHS...">
                
                <div id="loadingSpinner" class="absolute right-2 top-1/2 transform -translate-y-1/2 hidden">
                    <svg class="animate-spin h-5 w-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 2v4M12 22v-4M2 12h4M22 12h-4"></path>
                    </svg>
                </div>

                <!-- Dropdown -->
                <ul id="userList" class="absolute left-0 w-full border border-gray-300 rounded-md mt-1 bg-white shadow-lg hidden z-50 max-h-48 overflow-auto"></ul>
                <input type="hidden" id="selectedUserId" name="user_id">
            </div>

            <!-- Tombol -->
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" id="closeModal" class="px-4 py-2 bg-gray-400 text-white rounded-md text-sm hover:bg-gray-500 cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-500 cursor-pointer">Simpan</button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        function openModal(editMode = false, ehs = null) {
            const modal = document.getElementById("ehsModal");
            modal.classList.remove("hidden");
            
            if (editMode) {
                document.getElementById("modalTitle").textContent = "Edit Departemen";
                document.getElementById("ehsId").value = ehs.id;
                document.getElementById("usernameInput").value = ehs.username;
                document.getElementById("selectedUserId").value = ehs.user_id;
                document.getElementById("searchUser").value = ehs.ehs_name;
            } else {
                document.getElementById("modalTitle").textContent = "Tambah Departemen";
                document.getElementById("ehsId").value = "";
                document.getElementById("usernameInput").value = "";
                document.getElementById("selectedUserId").value = "";
                document.getElementById("searchUser").value = "";
            }
        }

        window.openModal = openModal;
    
        document.getElementById("closeModal").addEventListener("click", function () {
            document.getElementById("ehsModal").classList.add("hidden");
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
    
                    let response = await fetch(`/master-data/ehs-data/search-users?q=${query}`);
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
    
        document.getElementById("ehsForm").addEventListener("submit", async function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            let ehsId = document.getElementById("ehsId").value;
            let url = ehsId ? `/master-data/ehs-data/${ehsId}` : "/master-data/ehs-data";
            let method = ehsId ? "PUT" : "POST";
            formData.append('password', 'Avi123!'); 
    
            try {
                let options = {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
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
                document.getElementById("ehsModal").classList.add("hidden"); // **Close modal earlier**

                if (response.ok) {
                    Swal.fire({
                        title: "Success!",
                        text: "ehs has been successfully saved!",
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
                console.log("Error saving ehs:", error);
                Swal.fire({
                    title: "Failed!",
                    text: "Failed to save data.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    
        document.getElementById("openModal").addEventListener("click", function () {
            openModal(false);
        });
    });

    function deleteehs(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "Deleted ehs cannot be restored!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "Cancel"
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    let response = await fetch(`/master-data/ehs-data/${id}`, {
                        method: "DELETE",
                        headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }
                    });

                    if (response.ok) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "The ehs has been successfully deleted.",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => location.reload());
                    } else {
                        Swal.fire("Error!", "Failed to delete ehs.", "error");
                    }
                } catch (error) {
                    console.error("Error deleting ehs:", error);
                    Swal.fire("Error!", "An error occurred while deleting the data.", "error");
                }
            }
        });
    }

</script>
    