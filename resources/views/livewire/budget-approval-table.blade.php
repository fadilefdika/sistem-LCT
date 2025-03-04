<div class="bg-white p-6 shadow-sm rounded-xl">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <input type="text" wire:model="search" placeholder="Cari budget request..."
            class="w-1/3 p-2 border border-gray-300 rounded-lg focus:ring focus:ring-gray-200 outline-none"
        >
        <select wire:model="perPage"
            class="p-2 border border-gray-300 rounded-lg focus:ring focus:ring-gray-200 outline-none"
        >
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="25">25</option>
        </select>
    </div>

    <!-- Tabel -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead class="bg-gray-100 text-gray-700">
                <tr class="text-left">
                    <th wire:click="sortBy('nama')" class="cursor-pointer px-4 py-3">Nama PIC</th>
                    <th wire:click="sortBy('deskripsi')" class="cursor-pointer px-4 py-3">Deskripsi</th>
                    <th wire:click="sortBy('jumlah')" class="cursor-pointer px-4 py-3">Jumlah</th>
                    <th wire:click="sortBy('created_at')" class="cursor-pointer px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($budgets as $budget)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">{{ $budget->pic->user->fullname ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $budget->deskripsi }}</td>
                        <td class="px-4 py-3">Rp {{ number_format($budget->jumlah, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">{{ $budget->created_at->format('d-m-Y') }}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button wire:click="approve({{ $budget->id }})"
                                class="px-3 py-1 bg-green-500 text-white rounded-lg flex items-center space-x-1 hover:bg-green-600 transition"
                            >
                                ✅ <span>Approve</span>
                            </button>
                            <button wire:click="reject({{ $budget->id }})"
                                class="px-3 py-1 bg-red-500 text-white rounded-lg flex items-center space-x-1 hover:bg-red-600 transition"
                            >
                                ❌ <span>Reject</span>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex justify-end">
        {{ $budgets->links() }}
    </div>
</div>
