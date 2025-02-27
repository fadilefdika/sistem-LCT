<div class="overflow-x-auto">
    @if($laporans->isEmpty())
        <div class="p-4 bg-yellow-100 text-yellow-800 rounded-md text-center">
            Tidak ada laporan yang sedang diperbaiki.
        </div>
    @else
        <table class="w-full text-sm text-left text-gray-700 border-collapse">
            <thead class="text-xs text-black uppercase bg-gray-200">
                <tr>
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
            <tbody>
                @foreach($laporans as $index => $laporan)
                <tr class="border-b hover:bg-gray-100">
                    <td class="px-4 py-3 text-center">{{ $laporans->firstItem() + $index }}</td>
                    <td class="px-4 py-3">{{ $laporan->temuan_ketidaksesuaian }}</td>
                    <td class="px-4 py-3">{{ $laporan->nama_pic ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $laporan->tingkat_bahaya ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $laporan->progress_status }}</td>
                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->translatedFormat('d F Y') }}</td>
                    <td class="px-4 py-3">
                        @if($laporan->tanggal_selesai)
                            {{ \Carbon\Carbon::parse($laporan->tanggal_selesai)->translatedFormat('d F Y') }}
                        @else
                            <span class="text-red-500">Belum Selesai</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('progress.perbaikan.detail', $laporan->id_laporan_lct) }}" 
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
