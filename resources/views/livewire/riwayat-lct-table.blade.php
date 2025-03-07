<div class="overflow-x-auto bg-white p-6 shadow-sm rounded-xl">
    @if($laporans->isEmpty())
        <div class="p-4 bg-yellow-100 text-yellow-800 rounded-md text-center">
            Belum ada riwayat laporan.
        </div>
    @else
        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr class="text-left text-sm font-semibold text-gray-600">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Temuan</th>
                        <th class="py-3 px-4">Tanggal Temuan</th>
                        @if($role == 'ehs')
                            <th class="py-3 px-4">Nama PIC</th>
                        @endif
                        <th class="py-3 px-4">Status Akhir</th>
                        <th class="py-3 px-4">Feedback</th>
                        <th class="py-3 px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($laporans as $index => $laporan)
                    <tr class="hover:bg-gray-100 transition duration-200 ease-in-out border-b bg-white">
                        <td class="px-6 py-4 font-medium">{{ $laporans->firstItem() + $index }}</td>
                        <td class="py-4 px-6 border-b text-sm text-gray-800">{{ $laporan->temuan_ketidaksesuaian }}</td>
                        <td class="py-4 px-6 border-b text-sm text-gray-800">{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->translatedFormat('d F Y') }}</td>
                        @if($role == 'ehs')
                            <td class="py-4 px-6 border-b text-sm text-gray-800">
                                {{ isset($laporan->pic) ? $laporan->pic->nama : '-' }}
                            </td>
                        @endif
                        <td class="py-4 px-6 border-b text-sm text-gray-800">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $laporan->status_lct == 'closed' ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700' }}">
                                {{ ucfirst($laporan->status_lct) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 border-b text-sm text-gray-800">{{ $laporan->feedback_reject ?? '-' }}</td>
                        <td class="py-4 px-6 border-b text-sm ">
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

    <div class="mt-6 flex justify-between items-center border-t px-5 py-3">
        <span class="text-sm text-gray-600">
            Showing {{ $laporans->firstItem() }} to {{ $laporans->lastItem() }} of {{ $laporans->total() }} entries
        </span>
        <div>
            {{ $laporans->links('pagination::tailwind') }}
        </div>
    </div>
</div>
