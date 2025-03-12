<div class="overflow-x-auto bg-white p-6 shadow-md rounded-xl">
    <div class="rounded-lg border border-gray-300">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-100 text-gray-700 text-sm font-semibold">
                <tr>
                    <th class="py-3 px-5 text-left">No</th>
                    <th class="py-3 px-5 text-left">Category Name</th>
                    <th class="py-3 px-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach ($kategories as $index => $kategori)
                    <tr class="hover:bg-gray-100 transition duration-150 ease-in-out">
                        <td class="py-4 px-5 text-gray-800">{{ $kategories->firstItem() + $index }}</td>
                        <td class="py-4 px-5 text-gray-800">{{ $kategori->nama_kategori }}</td>
                        <td class="py-4 px-5 text-center">
                            <div class="flex justify-center space-x-2">
                                <!-- Edit Button -->
                                <button wire:click="edit({{ $kategori->id }})"
                                    class="flex items-center bg-yellow-500 text-white py-2 px-3 rounded-md hover:bg-yellow-400 transition duration-200"
                                    aria-label="Edit">
                                    ✏️ Edit
                                </button>
                                
                                <!-- Delete Button -->
                                <button wire:click="delete({{ $kategori->id }})"
                                    class="flex items-center bg-red-500 text-white py-2 px-3 rounded-md hover:bg-red-400 transition duration-200"
                                    aria-label="Delete">
                                    🗑️ Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination Info & Controls -->
        <div class="mt-4 flex flex-col sm:flex-row justify-between items-center border-t px-5 py-3 text-gray-700 text-sm">
            <span>
                Showing {{ $kategories->firstItem() }} to {{ $kategories->lastItem() }} of {{ $kategories->total() }} entries
            </span>
            <div class="mt-3 sm:mt-0">
                {{ $kategories->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>
