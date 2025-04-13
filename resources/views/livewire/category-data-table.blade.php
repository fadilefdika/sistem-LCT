<div class="overflow-x-auto bg-white p-4 shadow-sm rounded-lg border border-gray-200">
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-medium text-gray-800">Category List</h2>
        <button id="openModal" class="bg-blue-600 text-white py-2 px-3 text-xs rounded-md hover:bg-blue-500 transition cursor-pointer">
            + Add Category
        </button>        
    </div>
    

    <div class="border border-gray-300 rounded-lg overflow-hidden">
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full table-auto text-sm text-left text-gray-700">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama Kategori</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($categories as $index => $category)
                <tr class="hover:bg-gray-50 transition-colors" id="row-{{ $category->id }}">
                    <td class="px-4 py-2">{{ $categories->firstItem() + $index }}</td>
                    <td class="px-4 py-2">{{ $category->nama_kategori }}</td>
                    <td class="px-4 py-2 flex gap-2">
                        <button 
                            class="edit-category-btn flex items-center gap-1 text-blue-600 border border-blue-600 rounded-md px-3 py-1.5 transition-all duration-200 ease-in-out hover:bg-blue-600 hover:text-white hover:shadow-md hover:scale-105 cursor-pointer"data-id="{{ $category->id }}"
                            data-nama="{{ $category->nama_kategori }}"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536l-10.036 10.036H7v-3.036L16.464 3.464z" />
                        </svg>
                        Edit
                        </button>
                        <button 
                        class="delete-category-btn flex items-center gap-1 text-red-600 border border-red-600 rounded-md px-3 py-1.5 transition-all duration-200 ease-in-out 
                        hover:bg-red-600 hover:text-white hover:shadow-md hover:scale-105 cursor-pointer"
                        data-id="{{ $category->id }}"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Delete
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>            
        </table>
        

    </div>
    <div class="mt-3 flex flex-col sm:flex-row justify-between items-center border-t px-4 py-3 text-gray-700 text-sm">
        <span>Showing {{ $categories->firstItem() }} - {{ $categories->lastItem() }} of {{ $categories->total() }} records</span>
        <div class="mt-2 sm:mt-0">{{ $categories->links('pagination::tailwind') }}</div>
    </div> 
    </div>

    <!-- Modal Tambah/Edit Category -->
    <div id="categoryModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 px-4 z-50 hidden">
        <div class="bg-white p-5 rounded-lg shadow-md w-full max-w-md">
            <h2 class="text-lg font-medium text-gray-800 mb-3">
                <span id="modalTitle">Add Category</span>
            </h2>

            <!-- Form -->
            <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId" name="category_id">

                <!-- Nama Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" id="namaCategory" name="nama_kategori" class="w-full border border-gray-300 rounded-md p-2 mt-1 text-sm focus:ring focus:ring-blue-200" placeholder="Enter Category Name...">
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
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('categoryModal');
        const openModalBtn = document.getElementById('openModal');
        const closeModalBtn = document.getElementById('closeModal');
        const form = document.getElementById('categoryForm');
        const modalTitle = document.getElementById('modalTitle');
        const categoryIdInput = document.getElementById('categoryId');
        const namaInput = document.getElementById('namaCategory');

        // Open Add Modal
        if (openModalBtn) {
            openModalBtn.addEventListener('click', () => {
                form.reset();
                modalTitle.textContent = 'Add Category';
                categoryIdInput.value = '';
                modal.classList.remove('hidden');
            });
        }

        // Close Modal
        closeModalBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        // Bind Edit Buttons
        function bindEditButtons() {
            document.querySelectorAll('.edit-category-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const nama = btn.dataset.nama;

                    modalTitle.textContent = 'Edit Category';
                    categoryIdInput.value = id;
                    namaInput.value = nama;
                    modal.classList.remove('hidden');

                    // Highlight row
                    document.querySelectorAll('tr').forEach(tr => tr.classList.remove('bg-yellow-50'));
                    const currentRow = document.getElementById(`row-${id}`);
                    if (currentRow) currentRow.classList.add('bg-yellow-50');
                });
            });
        }

        bindEditButtons();

        if (window.Livewire) {
            Livewire.on('refreshTable', () => {
                setTimeout(() => {
                    bindEditButtons();
                }, 100);
            });
        }

        // Delete with SweetAlert
        document.querySelectorAll('.delete-category-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This category will be soft deleted.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/master-data/category-data/${btn.dataset.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message
                            });
                            window.location.reload();
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'Something went wrong while deleting.'
                            });
                        });
                    }
                });
            });
        });

        // Submit Form (Add/Edit)
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const id = categoryIdInput.value;
            const nama = namaInput.value;

            const url = id ? `/master-data/category-data/${id}` : `/master-data/category-data`;
            const method = id ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ nama_kategori: nama })
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message
                });
                modal.classList.add('hidden');
                window.location.reload();
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while saving.'
                });
                console.error(err);
            });
        });
    });
</script>

