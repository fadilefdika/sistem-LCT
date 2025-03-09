<div class="bg-white dark:bg-gray-800 p-6 relative shadow-md sm:rounded-lg overflow-hidden">
    
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-sm font-semibold text-gray-600">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Temuan Ketidaksesuaian</th>
                    <th class="px-4 py-3">Nama PIC</th>
                    <th class="px-4 py-3">Tingkat Bahaya</th>
                    <th class="px-4 py-3">Status Progress</th>
                    <th class="px-4 py-3">Tenggat Waktu</th>
                    <th class="px-4 py-3">Tanggal Selesai</th>
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($laporans as $index => $laporan)
                <tr class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out">
                    <td class="px-4 py-3 text-center font-semibold text-gray-800 w-12">
                        {{ $laporans->firstItem() + $index }}
                    </td>
                    <td class="px-4 py-3 text-gray-800 max-w-xs truncate cursor-pointer relative group">
                        <span class="temuan-clamp" title="{{ $laporan->temuan_ketidaksesuaian }}">
                            {{ $laporan->temuan_ketidaksesuaian }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-800 w-40">{{ $laporan->picUser->fullname ?? '-' }}</td>
    
                    <!-- Tingkat Bahaya -->
                    <td class="px-4 py-3 text-gray-800 w-28">
                        <span class="px-3 py-1 text-xs font-semibold text-white rounded-full
                            {{ $laporan->tingkat_bahaya === 'High' ? 'bg-red-500' : ($laporan->tingkat_bahaya === 'Medium' ? 'bg-yellow-500' : 'bg-green-500') }}">
                            {{ $laporan->tingkat_bahaya }}
                        </span>
                    </td>
    
                    <!-- Status Progress -->
                    <td class="px-4 py-3 text-gray-800 w-36">
                        @php
                            $statusColors = [
                                'in_progress' => 'bg-gray-500',
                                'progress_work' => 'bg-yellow-500',
                                'waiting_approval' => 'bg-blue-500',
                                'approved' => 'bg-green-500',
                                'closed' => 'bg-green-700',
                                'rejected' => 'bg-red-500'
                            ];
                        @endphp
                        <span class="inline-flex items-center justify-center px-3 py-1 text-[10px] font-semibold text-white rounded-full
                            {{ $statusColors[$laporan->status_lct] ?? 'bg-gray-400' }} whitespace-nowrap">
                            {{ ucwords(str_replace('_', ' ', $laporan->status_lct)) }}
                        </span>
                    </td>
    
                    <!-- Tenggat Waktu -->
                    <td class="px-4 py-3 text-gray-800 w-32">
                        {{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->translatedFormat('d F Y') }}
                    </td>
    
                    <!-- Tanggal Selesai -->
                    <td class="px-4 py-3 text-gray-800 w-32">
                        @if($laporan->tanggal_selesai)
                            {{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->translatedFormat('d F Y') }}
                        @else
                            <span class="text-red-500 font-semibold">Belum Selesai</span>
                        @endif
                    </td>
    
                    <!-- Tombol Aksi -->
                    <td class="px-4 py-3 flex items-center gap-2 w-28">
                        <a href="{{ route('admin.progress-perbaikan.show', $laporan->id_laporan_lct) }}"
                            class="text-blue-600 hover:underline">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-6 text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6">
                                </path>
                            </svg>
                            <p class="text-sm">Tidak ada laporan yang tersedia.</p>
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
