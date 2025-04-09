<div class="bg-white dark:bg-gray-800 p-6 relative shadow-md sm:rounded-lg overflow-y-auto">
    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <!-- Date Range Selector -->
        <div class="mb-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Select Report Period</label>
            <div class="flex flex-wrap gap-3">
                <select wire:model="rangeType"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-300 w-full sm:w-auto">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="semester">Semester</option>
                    <option value="yearly">Yearly</option>
                    {{-- <option value="custom">Custom Date Range</option> --}}
                </select>
        
                {{-- @if($rangeType === 'custom')
                    <div class="flex gap-3 w-full sm:w-auto">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Start Date</label>
                            <input type="date" wire:model="startDate"
                                   class="px-3 py-2 border rounded-md text-sm border-gray-300">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">End Date</label>
                            <input type="date" wire:model="endDate"
                                   class="px-3 py-2 border rounded-md text-sm border-gray-300">
                        </div>
                    </div>
                @endif --}}
            </div>
        </div>
        
        <!-- Export Buttons with Clear Distinction -->
        <div class="border-t pt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Export Reports</label>
            <div class="flex flex-wrap gap-3">
                <!-- PPT Button with PowerPoint styling -->
                <div class="export-option ppt-option">
                    <span class="block text-xs font-semibold text-orange-600 mb-1">Presentation Format</span>
                    <button wire:click="exportToPPT"
                            class="flex cursor-pointer items-center px-4 py-2 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-md transition duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                        Download PPT Report
                    </button>
                </div>
                
                <!-- Excel Button with Excel styling -->
                <div class="export-option excel-option">
                    <span class="block text-xs font-semibold text-green-600 mb-1">Data Format</span>
                    <button wire:click="exportToExcel"
                            class="flex cursor-pointer items-center px-4 py-2 text-sm font-medium text-white bg-green-500 hover:bg-green-600 rounded-md transition duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        Export Excel Report
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    
    <div class="overflow-x-auto rounded-lg border border-gray-200"> 
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-sm font-semibold text-gray-600">
                    <th class="py-3 px-4">No</th>
                    <th class="py-3 px-4">Non-Conformity Findings</th>
                    <th class="py-3 px-4">Date of Finding</th>
                    <th class="py-3 px-4">SVP Name</th>
                    <th class="py-3 px-4">Risk Level</th>
                    <th class="py-3 px-4">Final Status</th>
                    <th class="px-4 py-3">Completion Date</th>
                    <th class="py-3 px-4">Action</th>
                </tr>                
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($laporans as $index => $laporan)
                <tr class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out border-b bg-white">
                    <td class="px-6 py-4 text-center font-semibold text-gray-800">
                        {{ $laporans->firstItem() + $index }}
                    </td>
                    <td class="py-4 px-6 border-b text-gray-800">{{ $laporan->temuan_ketidaksesuaian }}</td>
                    <td class="py-4 px-6 border-b text-gray-800">
                        {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->translatedFormat('d F Y') }}
                    </td>
                    <td class="py-4 px-6 border-b text-gray-800">
                        {{ $laporan->picUser->fullname ?? '-' }}
                    </td>
                    <td class="py-4 px-6 border-b text-gray-800">
                        {{ $laporan->tingkat_bahaya ?? '-' }}
                    </td>

                    <!-- Status Akhir dengan Badge Warna -->
                    <td class="py-4 px-6 border-b text-gray-800">
                        @php
                            $statusColors = [
                                'closed' => 'bg-green-700', 
                                'revision' => 'bg-red-500', 
                                'progress_work' => 'bg-yellow-500', 
                                'waiting_approval' => 'bg-blue-500', 
                                'approved' => 'bg-green-500', 
                                'in_progress' => 'bg-gray-500'
                            ];
                        @endphp
                        <span class="inline-flex items-center justify-center px-3 py-1 text-xs font-semibold text-white rounded-full 
                            {{ $statusColors[$laporan->status_lct] ?? 'bg-gray-400' }} whitespace-nowrap">
                            {{ ucwords(str_replace('_', ' ', $laporan->status_lct)) }}
                        </span>
                    </td>

                    <!-- Tenggat Waktu -->
                    <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                        {{ $laporan->date_completion ? \Carbon\Carbon::parse($laporan->date_completion)->format('F d, Y') : '-' }}
                    </td>

                    <!-- Tombol Aksi -->
                    <td class="py-4 px-6 border-b">
                        <a href="{{ route('admin.riwayat-lct.show', $laporan->id_laporan_lct) }}" 
                            class="text-blue-600 hover:underline font-semibold">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6"></path>
                            </svg>
                            <p class="text-sm font-medium">No reports are available.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
<div class="mt-6 flex justify-between items-center border-t px-5 py-3">
    <span class="text-sm text-gray-600">
        Showing {{ $laporans->firstItem() }} to {{ $laporans->lastItem() }} of {{ $laporans->total() }} entries
    </span>
    <div>
        {{ $laporans->links('pagination::tailwind') }}
    </div>
</div>
</div>