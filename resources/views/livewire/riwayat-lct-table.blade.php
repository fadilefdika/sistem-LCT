<div class="overflow-x-auto">
    @if($laporans->isEmpty())
        <div class="p-4 bg-yellow-100 text-yellow-800 rounded-md text-center">
            Belum ada riwayat laporan.
        </div>
    @else
        <table class="w-full text-sm text-left text-gray-700 border-collapse">
            <thead class="text-xs text-black uppercase">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Temuan</th>
                    <th class="px-4 py-3">Tanggal Temuan</th>
                    <th class="px-4 py-3">Status Akhir</th>
                    <th class="px-4 py-3">Feedback</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporans as $index => $laporan)
                <tr class="border-b hover:bg-gray-100">
                    <td class="px-4 py-3">{{ $laporans->firstItem() + $index }}</td>
                    <td class="px-4 py-3">{{ $laporan->temuan_ketidaksesuaian }}</td>
                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->translatedFormat('d F Y') }}</td>
                    <td class="px-4 py-3 text-{{ $laporan->status_lct == 'closed' ? 'green' : 'red' }}-600 font-semibold">
                        {{ ucfirst($laporan->status_lct) }}
                    </td>
                    <td class="px-4 py-3">{{ $laporan->feedback_reject ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('riwayat.lct.detail', $laporan->id_laporan_lct) }}" class="text-blue-600 hover:underline">Detail</a>
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
