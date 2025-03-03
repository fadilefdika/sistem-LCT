<div class="overflow-x-auto">
    @if($laporans->isEmpty())
        <div class="p-4 bg-yellow-100 text-yellow-800 rounded-md text-center">
            Belum ada riwayat laporan.
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg p-4">
            <table class="w-full text-sm text-left border-separate border-spacing-y-2">
                <thead class="text-xs font-semibold text-gray-600 uppercase bg-gray-100 rounded-lg">
                    <tr>
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Temuan</th>
                        <th class="px-6 py-3">Tanggal Temuan</th>
                        @if($role == 'ehs')
                            <th class="px-6 py-3">Nama PIC</th>
                        @endif
                        <th class="px-6 py-3">Status Akhir</th>
                        <th class="px-6 py-3">Feedback</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach($laporans as $index => $laporan)
                    <tr class="bg-white shadow-sm rounded-lg hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium">{{ $laporans->firstItem() + $index }}</td>
                        <td class="px-6 py-4">{{ $laporan->temuan_ketidaksesuaian }}</td>
                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->translatedFormat('d F Y') }}</td>
                        @if($role == 'ehs')
                            <td class="px-6 py-4">
                                {{ isset($laporan->pic) ? $laporan->pic->nama : '-' }}
                            </td>
                        @endif
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $laporan->status_lct == 'closed' ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700' }}">
                                {{ ucfirst($laporan->status_lct) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $laporan->feedback_reject ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <a href="{{-- route('riwayat.lct.detail', $laporan->id_laporan_lct) --}}" 
                               class="text-blue-500 hover:text-blue-700 font-medium">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-6">
        {{ $laporans->links() }}
    </div>
</div>
