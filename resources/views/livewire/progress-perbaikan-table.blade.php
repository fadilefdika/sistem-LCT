<div class="overflow-x-auto">
    @if($laporans->isEmpty())
        <div class="p-4 bg-yellow-100 text-yellow-800 rounded-md text-center">
            Tidak ada laporan yang sedang diperbaiki.
        </div>
    @else
    <table class="w-full text-sm text-left text-gray-700 border-collapse rounded-lg overflow-hidden shadow-md">
        <thead class="text-xs text-gray-700 uppercase bg-white border-b">
            <tr>
                <th class="px-6 py-3">No</th>
                <th class="px-6 py-3">Temuan Ketidaksesuaian</th>
                <th class="px-6 py-3">Nama PIC</th>
                <th class="px-6 py-3">Tingkat Bahaya</th>
                <th class="px-6 py-3">Status Progress</th>
                <th class="px-6 py-3">Tenggat Waktu</th>
                <th class="px-6 py-3">Tanggal Selesai</th>
                <th class="px-6 py-3">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporans as $index => $laporan)
            <tr class="border-b hover:bg-gray-100 transition bg-white duration-200">
                <td class="px-6 py-4 text-center font-semibold text-gray-800">
                    {{ $laporans->firstItem() + $index }}
                </td>
                <td class="px-6 py-4">{{ $laporan->temuan_ketidaksesuaian }}</td>
                <td class="px-6 py-4">{{ $laporan->picUser->fullname ?? '-' }}</td>
                
                <!-- Tingkat Bahaya dengan Badge Warna -->
                <td class="px-6 py-4">
                    <span class="px-3 py-1 text-xs font-semibold text-white rounded-full 
                        {{ $laporan->tingkat_bahaya === 'High' ? 'bg-red-500' : ($laporan->tingkat_bahaya === 'Medium' ? 'bg-yellow-500' : 'bg-green-500') }}">
                        {{ $laporan->tingkat_bahaya }}
                    </span>
                </td>
    
                <!-- Status Progress dengan Badge Warna -->
                <td class="px-6 py-4">
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
                <td class="px-6 py-4 text-gray-600">
                    {{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->translatedFormat('d F Y') }}
                </td>
    
                <!-- Tanggal Selesai -->
                <td class="px-6 py-4">
                    @if($laporan->tanggal_selesai)
                        {{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->translatedFormat('d F Y') }}
                    @else
                        <span class="text-red-500 font-semibold">Belum Selesai</span>
                    @endif
                </td>
    
                <!-- Tombol Aksi -->
                <td class="px-6 py-4">
                    <a href="{{ route('admin.progress-perbaikan.show', $laporan->id_laporan_lct) }}" 
                        class="text-blue-600 hover:underline">
                        Detail
                    </a>                     
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @endif

    <div class="mt-4">
        {{ $laporans->links() }}
    </div>
</div>
