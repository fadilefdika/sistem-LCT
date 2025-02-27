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
                            @php
                                $statusColors = [
                                    'in_progress' => 'bg-gray-500', // Abu-abu untuk belum dibuka
                                    'progress_work' => 'bg-blue-500', // Biru untuk sedang dikerjakan
                                    'waiting_approval' => 'bg-yellow-500', // Kuning untuk menunggu persetujuan
                                    'approved' => 'bg-green-500', // Hijau untuk disetujui
                                    'closed' => 'bg-purple-500', // Ungu untuk laporan selesai
                                    'rejected' => 'bg-red-500', // Merah untuk ditolak
                                ];
                        
                                $statusLabels = [
                                    'in_progress' => 'Belum Diproses',
                                    'progress_work' => 'Sedang Dikerjakan',
                                    'waiting_approval' => 'Menunggu Persetujuan',
                                    'approved' => 'Disetujui',
                                    'closed' => 'Selesai',
                                    'rejected' => 'Ditolak',
                                ];
                            @endphp
                        
                            <span class="px-2 py-1 rounded-full text-white text-xs font-semibold {{ $statusColors[$laporan->status_lct] ?? 'bg-gray-300' }}">
                                {{ $statusLabels[$laporan->status_lct] ?? 'Tidak Diketahui' }}
                            </span>
                        </td>
                        
                        <td class="px-4 py-3">
                            {{ $laporan->date_completion ? \Carbon\Carbon::parse($laporan->date_completion)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            
                            <a href="{{ route('admin.manajemen-lct.show', $laporan->id_laporan_lct) }}" class="text-blue-600 hover:underline">Detail</a>
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
