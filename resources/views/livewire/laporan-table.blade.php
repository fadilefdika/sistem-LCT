<div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
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

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700 border-collapse">
            <thead class="text-xs text-black uppercase">
                <tr>
                    <th scope="col" class="px-4 py-3">No</th>
                    <th scope="col" class="px-4 py-3">Nama Pelapor</th>
                    <th scope="col" class="px-4 py-3">Temuan Ketidaksesuaian</th>
                    <th scope="col" class="px-4 py-3">Tanggal</th>
                    <th scope="col" class="px-4 py-3">Area</th>
                    <th scope="col" class="px-4 py-3">Foto</th>
                    <th scope="col" class="px-4 py-3">Kategori</th>
                    <th scope="col" class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporans as $index => $laporan)
                <tr class="border-b hover:bg-gray-100">
                    <td class="px-4 py-3">{{ $laporans->firstItem() + $index }}</td>
                    <td class="px-4 py-3">{{ $laporan->user->fullname }}</td>
                    <td class="px-4 py-3 max-w-xs truncate cursor-pointer relative group">
                        <span class="temuan-clamp" title="{{ $laporan->temuan_ketidaksesuaian }}">
                            {{ $laporan->temuan_ketidaksesuaian }}
                        </span>
                        <span class="absolute hidden group-hover:block bg-black text-white text-xs rounded p-1 left-0 top-full w-auto max-w-sm">
                            {{ $laporan->temuan_ketidaksesuaian }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->locale('id')->translatedFormat('d F Y') }}</td>
                    <td class="px-4 py-3">{{ $laporan->area }}</td>
                    <td class="px-4 py-3">
                        <img src="{{ asset('storage/' . $laporan->foto_temuan) }}" class="w-20 h-20 object-cover rounded-lg shadow">
                    </td>
                    <td class="px-4 py-3">{{ $laporan->kategori_temuan }}</td>
                    <td class="px-4 py-3 flex items-center gap-2">
                        <a href="{{ route('admin.laporan-lct.show', $laporan->id_laporan_lct) }}" class="text-blue-600 hover:underline">Detail</a>
                        <button wire:click="delete({{ $laporan->id }})" class="text-red-600 hover:underline">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-between items-center my-4 px-4">
        <div>
            {{ $laporans->links() }} <!-- Livewire Pagination -->
        </div>
    </div>

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
    
</div>
