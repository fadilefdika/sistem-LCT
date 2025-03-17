<div class="bg-white dark:bg-gray-800 p-6 relative shadow-md sm:rounded-lg overflow-y-auto">
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