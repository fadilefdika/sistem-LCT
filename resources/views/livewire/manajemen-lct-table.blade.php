<div class="overflow-x-auto bg-white p-6 shadow-sm rounded-xl">
    <input type="text" wire:model="search" placeholder="Cari laporan..." class="border p-2 mb-3 w-full rounded-md focus:ring focus:ring-blue-200">

    <div class="overflow-hidden rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-sm font-semibold text-gray-600">
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
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach($laporans as $index => $laporan)
                    <tr class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out border-b bg-white">
                        <td class="px-6 py-4 border-b text-gray-800">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 border-b text-gray-800">{{ $laporan->temuan_ketidaksesuaian }}</td>
                        <td class="px-6 py-4 border-b text-gray-800"> {{$laporan->area}} - {{ $laporan->detail_area }}</td>
                        <td class="px-6 py-4 border-b text-gray-800">
                            <span class="px-2 py-1 rounded-full text-white text-xs font-semibold
                                {{ $laporan->tingkat_bahaya === 'High' ? 'bg-red-500' : ($laporan->tingkat_bahaya === 'Medium' ? 'bg-yellow-500' : 'bg-green-500') }}">
                                {{ $laporan->tingkat_bahaya }}
                            </span>
                        </td>
                        <td class="px-6 py-4 border-b text-gray-800">{{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->format('d M Y') }}</td>
                        <td class="px-6 py-4 border-b text-gray-800">
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
                        
                        <td class="px-6 py-4 border-b text-gray-800">
                            {{ $laporan->date_completion ? \Carbon\Carbon::parse($laporan->date_completion)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 border-b">
                            
                            <a href="{{ route('admin.manajemen-lct.show', $laporan->id_laporan_lct) }}" class="text-blue-600 hover:underline">Detail</a>
                        </td>
                    </tr>
                @endforeach
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
