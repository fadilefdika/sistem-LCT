<div class="bg-white dark:bg-gray-800 p-6 relative shadow-md sm:rounded-lg overflow-y-auto">
    <div class="flex flex-wrap md:flex-nowrap items-center justify-between p-4 gap-2">
        <div class="w-full md:w-1/2">
            <input type="text" wire:model.debounce.300ms="search"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full pl-3 p-2.5 focus:border-blue-500 focus:ring focus:ring-blue-300"
                placeholder="Cari laporan..." />
        </div>

        <!-- Dropdown untuk memilih kategori -->
        <div>
            <select wire:model="filterKategori"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2 focus:border-blue-500 focus:ring focus:ring-blue-300">
                <option value="">Semua Kategori</option>
                @foreach($kategoriOptions as $kategori)
                    <option value="{{ $kategori }}">{{ $kategori }}</option>
                @endforeach
            </select>
        </div>

        <!-- Dropdown untuk memilih jumlah baris per halaman -->
        <div>
            <select wire:model="perPage"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-1 pr-6">
                <option value="5">5 Baris</option>
                <option value="10">10 Baris</option>
                <option value="25">25 Baris</option>
                <option value="50">50 Baris</option>
            </select>
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-sm font-semibold text-gray-600">
                    <th scope="col" class="px-4 py-3 w-12 text-center">No</th>
                    <th scope="col" class="px-4 py-3 w-40">Nama Pelapor</th>
                    <th scope="col" class="px-4 py-3 w-60">Temuan Ketidaksesuaian</th>
                    <th scope="col" class="px-4 py-3 w-32">Tanggal</th>
                    <th scope="col" class="px-4 py-3 w-36">Area</th>
                    <th scope="col" class="px-4 py-3 w-24 text-center">Foto</th>
                    <th scope="col" class="px-4 py-3 w-36">Kategori</th>
                    <th scope="col" class="px-4 py-3 w-28 text-center">Actions</th>
                </tr>
            </thead>                
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($laporans as $index => $laporan)
                <tr class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out">
                    <td class="px-4 py-3 text-center font-semibold text-gray-800">{{ $laporans->firstItem() + $index }}</td>
                    <td class="px-4 py-3 text-gray-800">{{ $laporan->user->fullname }}</td>
                    <td class="px-4 py-3 text-gray-800 max-w-xs break-words truncate cursor-pointer relative group">
                        <span class="temuan-clamp" title="{{ $laporan->temuan_ketidaksesuaian }}">
                            {{ $laporan->temuan_ketidaksesuaian }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-800">{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->locale('id')->translatedFormat('d F Y') }}</td>
                    <td class="px-4 py-3 text-gray-800">{{ $laporan->area }}</td>
                    <td class="px-4 py-3 text-center">
                        @if (!empty($laporan->bukti_temuan))
                            @php
                                $gambar = json_decode($laporan->bukti_temuan, true)[0] ?? null;
                            @endphp
                            @if ($gambar)
                                <img src="{{ asset('storage/' . $gambar) }}" class="w-16 h-16 object-cover rounded-lg shadow">
                            @else
                                <span class="text-gray-500 italic">Tidak ada gambar</span>
                            @endif
                        @else
                            <span class="text-gray-500 italic">Tidak ada gambar</span>
                        @endif
                    </td>                        
                    <td class="px-4 py-3 text-gray-800">{{ $laporan->kategori_temuan }}</td>
                    <td class="px-4 py-3 flex items-center justify-center gap-2">
                        <a href="{{ route('admin.laporan-lct.show', $laporan->id_laporan_lct) }}" class="text-blue-600 hover:underline">Detail</a>
                        <button wire:click="delete({{ $laporan->id }})" class="text-red-600 hover:underline">Hapus</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-6 text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6"></path>
                            </svg>
                            <p class="text-sm">Tidak ada laporan yang tersedia.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    

    @if(isset($message))
        <div class="p-4 bg-yellow-100 text-yellow-800 rounded-md">
            {{ $message }}
        </div>
    @endif

    <style>
        .temuan-clamp {
            max-width: 250px; /* Atur sesuai kebutuhan */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal; /* Pastikan teks bisa wrapping */
            word-wrap: break-word; /* Pastikan teks bisa terpotong */
        }
    </style>
    
    <div class="mt-6 flex justify-between items-center border-t px-5 py-3">
        <span class="text-sm text-gray-600">
            Showing {{ $laporans->firstItem() }} to {{ $laporans->lastItem() }} of {{ $laporans->total() }} entries
        </span>
        <div>
            {{ $laporans->links('pagination::tailwind') }}
        </div>
    </div>
</div>
