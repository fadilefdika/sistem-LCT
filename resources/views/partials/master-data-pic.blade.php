<div class="overflow-x-auto bg-white p-6 shadow-sm rounded-xl">
    <div class="rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-sm font-semibold text-gray-600">
                    <th class="py-3 px-4">No</th>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Email</th>
                    <th class="py-3 px-4">Department</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach ($pics as $index => $pic)
                    <tr class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out border-b bg-white">
                        <td class="py-4 px-6 border-b text-sm text-gray-800">{{ $pics->firstItem() + $index }}</td>
                        <td class="py-4 px-6 border-b text-sm text-gray-800">{{ $pic->user->fullname }}</td>
                        <td class="py-4 px-6 border-b text-sm text-gray-800">{{ $pic->user->email }}</td>
                        <td class="py-4 px-6 border-b text-sm text-gray-800">{{ $pic->departemen->first()->nama_departemen }}</td>
                        <td class="py-4 px-5 text-center">
                            <div class="flex justify-center space-x-2">
                                <!-- Edit Button -->
                                <button wire:click="edit({{ $pic->id }})"
                                    class="flex items-center bg-yellow-500 text-white py-2 px-3 rounded-md hover:bg-yellow-400 transition duration-200"
                                    aria-label="Edit">
                                    ✏️ Edit
                                </button>
                                
                                <!-- Delete Button -->
                                <button wire:click="delete({{ $pic->id }})"
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

        <div class="mt-6 flex justify-between items-center border-t px-5 py-3">
            <span class="text-sm text-gray-600">
                Showing {{ $pics->firstItem() }} to {{ $pics->lastItem() }} of {{ $pics->total() }} entries
            </span>
            <div>
                {{ $pics->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>