<div class="overflow-x-auto bg-white p-6 shadow-md rounded-xl">
    <div class="rounded-lg border border-gray-300">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-100 text-gray-700 text-sm font-semibold">
                <tr>
                    <th class="py-3 px-5 text-left">No</th>
                    <th class="py-3 px-5 text-left">Manajer Name</th>
                    <th class="py-3 px-5 text-left">Department Name</th>
                    <th class="py-3 px-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach ($departments as $index => $department)
                    <tr class="hover:bg-gray-100 transition duration-150 ease-in-out">
                        <td class="py-4 px-5 text-gray-800">
                            {{ ($departments->currentPage() - 1) * $departments->perPage() + $index + 1 }}
                        </td>
                        <td class="py-4 px-5 text-gray-800">
                            {{ optional($department->user)->fullname ?? '' }}
                        </td>
                        <td class="py-4 px-5 text-gray-800">{{ $department->nama_departemen }}</td>
                        <td class="py-4 px-5 text-center">
                            <div class="flex justify-center space-x-2">
                                <!-- Edit Button -->
                                <button wire:click="edit({{ $department->id }})"
                                    class="flex items-center bg-yellow-500 text-white py-2 px-3 rounded-md hover:bg-yellow-400 transition duration-200"
                                    aria-label="Edit">
                                    ✏️ Edit
                                </button>
                                
                                <!-- Delete Button -->
                                <button wire:click="delete({{ $department->id }})"
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
                Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }} of {{ $departments->total() }} entries
            </span>
            <div class="mt-3 sm:mt-0">
                {{ $departments->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>
