<div class="bg-white p-6 shadow-sm rounded-xl">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <input type="text" wire:model="search" placeholder="Cari budget request..."
            class="w-1/3 p-2 border border-gray-300 rounded-lg focus:ring focus:ring-gray-200 outline-none"
        >
        
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-sm font-semibold text-gray-600">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">PIC Name</th> 
                    <th class="px-4 py-3">Risk Level</th> 
                    <th class="px-4 py-3">Total Amount</th> 
                    <th class="px-4 py-3">Submission Date</th> 
                    <th class="px-4 py-3">Budget Status</th> 
                    <th class="px-4 py-3 text-center">Actions</th> 
                </tr>                
            </thead>            
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($budgets as $index => $budget)
                    <tr class="hover:bg-gray-100 transition duration-200 ease-in-out border-b bg-white">
                        <td class="px-4 py-4 text-sm text-gray-800">{{ $index + 1 }}</td>
                        <td class="px-4 py-4 text-sm text-gray-800">{{ $budget->pic->user->fullname ?? '-' }}</td>
                        <td class="px-4 py-4 text-sm text-gray-800">{{$budget->laporanLct->tingkat_bahaya}}</td>
                        <td class="px-4 py-4 text-gray-900 font-medium">
                            Rp {{ number_format($budget->budget, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-800">
                            {{ $budget->created_at->translatedFormat('d F Y') }}
                        </td>                        
                        <td class="px-4 py-4 text-sm text-gray-800">
                            <p class="truncate block max-w-xs">
                                {{$budget->status_budget}}
                            </p>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <a href="{{ route('admin.budget-approval-history.show', $budget->id_laporan_lct) }}" 
                                class="text-blue-500 hover:text-blue-700 font-medium hover:underline ">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6"></path>
                                </svg>
                                <p class="text-sm">Tidak ada data budget request yang tersedia.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex justify-between items-center border-t px-5 py-3">
        <span class="text-sm text-gray-600">
            Showing {{ $budgets->firstItem() }} to {{ $budgets->lastItem() }} of {{ $budgets->total() }} entries
        </span>
        <div>
            {{ $budgets->links('pagination::tailwind') }}
        </div>
    </div>
</div>
