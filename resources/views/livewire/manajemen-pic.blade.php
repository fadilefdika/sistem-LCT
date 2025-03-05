<div class="p-4">
    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-lg">
        <thead>
            <tr class="bg-gray-50 text-gray-800">
                <th class="py-4 px-6 border-b text-left font-medium text-sm tracking-wider">No</th>
                <th class="py-4 px-6 border-b text-left font-medium text-sm tracking-wider">Name</th>
                <th class="py-4 px-6 border-b text-left font-medium text-sm tracking-wider">Email</th>
                <th class="py-4 px-6 border-b text-left font-medium text-sm tracking-wider">Department</th>
                <th class="py-4 px-6 border-b text-left font-medium text-sm tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pics as $index => $pic)
                <tr class="hover:bg-gray-100 transition duration-200 ease-in-out">
                    <td class="py-4 px-6 border-b text-sm text-gray-700">{{ $pics->firstItem() + $index }}</td>
                    <td class="py-4 px-6 border-b text-sm text-gray-700">{{ $pic->user->fullname }}</td>
                    <td class="py-4 px-6 border-b text-sm text-gray-700">{{ $pic->user->email }}</td>
                    <td class="py-4 px-6 border-b text-sm text-gray-700">{{ $pic->departemen->first()->nama_departemen }}</td>
                    <td class="py-4 px-6 border-b text-sm">
                        <div class="flex space-x-3">
                            <button wire:click="edit({{ $pic->id }})" class="bg-yellow-400 text-yellow-900 py-2 px-3 rounded-lg hover:bg-yellow-300 transition duration-200">
                                Edit
                            </button>
                            <button wire:click="delete({{ $pic->id }})" class="bg-red-400 text-red-900 py-2 px-3 rounded-lg hover:bg-red-300 transition duration-200">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6 flex justify-between items-center">
        <span class="text-sm text-gray-600">
            Showing {{ $pics->firstItem() }} to {{ $pics->lastItem() }} of {{ $pics->total() }} entries
        </span>
        <div>
            {{ $pics->links('pagination::tailwind') }}
        </div>
    </div>
</div>
