<div class="overflow-x-auto bg-white p-6 shadow-sm rounded-xl">
    @if($laporans->isEmpty())
        <div class="p-4 bg-yellow-100 text-yellow-800 rounded-md text-center">
            Tidak ada laporan yang sedang diperbaiki.
        </div>
    @else
    <div class="overflow-hidden rounded-lg border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr class="text-left text-sm font-semibold text-gray-600">
                <th class="py-3 px-4">No</th>
                <th class="py-3 px-4">Temuan Ketidaksesuaian</th>
                <th class="py-3 px-4">Nama PIC</th>
                <th class="py-3 px-4">Tingkat Bahaya</th>
                <th class="py-3 px-4">Status Progress</th>
                <th class="py-3 px-4">Tenggat Waktu</th>
                <th class="py-3 px-4">Tanggal Selesai</th>
                <th class="py-3 px-4">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @foreach($laporans as $index => $laporan)
            <tr class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out border-b bg-white">
                <td class="px-6 py-4 text-center font-semibold text-gray-800">
                    {{ $laporans->firstItem() + $index }}
                </td>
                <td class="py-4 px-6 border-b text-gray-800">{{ $laporan->temuan_ketidaksesuaian }}</td>
                <td class="py-4 px-6 border-b text-gray-800">{{ $laporan->picUser->fullname ?? '-' }}</td>
                
                <!-- Tingkat Bahaya dengan Badge Warna -->
                <td class="py-4 px-6 border-b text-gray-800">
                    <span class="px-3 py-1 text-xs font-semibold text-white rounded-full 
                        {{ $laporan->tingkat_bahaya === 'High' ? 'bg-red-500' : ($laporan->tingkat_bahaya === 'Medium' ? 'bg-yellow-500' : 'bg-green-500') }}">
                        {{ $laporan->tingkat_bahaya }}
                    </span>
                </td>
    
                <!-- Status Progress dengan Badge Warna -->
                <td class="py-4 px-6 border-b text-gray-800">
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
                <td class="py-4 px-6 border-b text-gray-800">
                    {{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->translatedFormat('d F Y') }}
                </td>
    
                <!-- Tanggal Selesai -->
                <td class="py-4 px-6 border-b text-gray-800">
                    @if($laporan->tanggal_selesai)
                        {{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->translatedFormat('d F Y') }}
                    @else
                        <span class="text-red-500 font-semibold">Belum Selesai</span>
                    @endif
                </td>
    
                <!-- Tombol Aksi -->
                <td class="py-4 px-6 border-b">
                    <a href="{{ route('admin.progress-perbaikan.show', $laporan->id_laporan_lct) }}" 
                        class="text-blue-600 hover:underline">
                        Detail
                    </a>                     
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    
    @endif

    <div class="mt-6 flex justify-between items-center border-t px-5 py-3">
        <span class="text-sm text-gray-600">
            Showing {{ $laporans->firstItem() }} to {{ $laporans->lastItem() }} of {{ $laporans->total() }} entries
        </span>
        <div>
            {{ $laporans->links('pagination::tailwind') }}
        </div>
    </div>
</div>
