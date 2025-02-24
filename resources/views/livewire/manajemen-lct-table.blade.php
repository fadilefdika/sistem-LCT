<div class="bg-white shadow-md rounded-lg p-4">
    <input type="text" wire:model="search" placeholder="Cari laporan..." class="border p-2 mb-3 w-full rounded-md focus:ring focus:ring-blue-200">

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700 border-collapse">
            <thead class="text-xs text-black uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 ">No</th>
                    <th scope="col" class="px-4 py-3 ">Temuan Ketidaksesuaian</th>
                    <th scope="col" class="px-4 py-3 ">Detail Area</th>
                    <th scope="col" class="px-4 py-3 ">Tingkat Bahaya</th>
                    <th scope="col" class="px-4 py-3 ">Tenggat Waktu</th>
                    <th scope="col" class="px-4 py-3 ">Status Progress</th>
                    <th scope="col" class="px-4 py-3 ">Tanggal Selesai</th>
                    <th scope="col" class="px-4 py-3 ">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporans as $index => $laporan)
                    <tr class="border-b hover:bg-gray-100">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">{{ $laporan->temuan_ketidaksesuaian }}</td>
                        <td class="px-4 py-3">{{ $laporan->detail_area }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-white text-xs font-semibold
                                {{ $laporan->tingkat_bahaya === 'Tinggi' ? 'bg-red-500' : ($laporan->tingkat_bahaya === 'Sedang' ? 'bg-yellow-500' : 'bg-green-500') }}">
                                {{ $laporan->tingkat_bahaya }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-white text-xs font-semibold
                                {{ $laporan->status_progress === 'Selesai' ? 'bg-green-500' : ($laporan->status_progress === 'Proses' ? 'bg-yellow-500' : 'bg-gray-500') }}">
                                {{ $laporan->status_progress }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            {{ $laporan->tanggal_selesai ? \Carbon\Carbon::parse($laporan->tanggal_selesai)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            
                        <a href="#" class="text-blue-600 hover:underline">Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $laporans->links() }}
    </div>
</div>
