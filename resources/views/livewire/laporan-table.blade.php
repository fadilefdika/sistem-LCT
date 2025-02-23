{{-- <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
    <div class="flex flex-col md:flex-row items-center justify-between p-4">
        <div class="w-full md:w-1/2">
            <input type="text" wire:model.debounce.300ms="search"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full pl-10 p-2"
                placeholder="Search..." />
        </div>
        <div>
            <select wire:model="filterKategori"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2">
                <option value="">Semua Kategori</option>
                @foreach($kategoriOptions as $kategori)
                    <option value="{{ $kategori }}">{{ $kategori }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3">No</th>
                    <th scope="col" class="px-4 py-3">Nama Pelapor</th>
                    <th scope="col" class="px-4 py-3">Temuan</th>
                    <th scope="col" class="px-4 py-3">Tanggal</th>
                    <th scope="col" class="px-4 py-3">Area</th>
                    <th scope="col" class="px-4 py-3">Foto</th>
                    <th scope="col" class="px-4 py-3">Kategori</th>
                    <th scope="col" class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporans as $index => $laporan)
                <tr class="border-b">
                    <td class="px-4 py-3">{{ $laporans->firstItem() + $index }}</td>
                    <td class="px-4 py-3">{{ $laporan->nama_pelapor }}</td>
                    <td class="px-4 py-3">{{ $laporan->temuan }}</td>
                    <td class="px-4 py-3">{{ $laporan->tanggal_temuan }}</td>
                    <td class="px-4 py-3">{{ $laporan->area }}</td>
                    <td class="px-4 py-3">
                        <img src="{{ asset('storage/' . $laporan->foto_temuan) }}" class="w-20 h-20 object-cover">
                    </td>
                    <td class="px-4 py-3">{{ $laporan->kategori_temuan }}</td>
                    <td class="px-4 py-3 flex items-center gap-2">
                        <a href="#" class="text-blue-500">Detail</a>
                        <button wire:click="delete({{ $laporan->id }})" class="text-red-500">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="p-4">
        {{ $laporans->links() }}
    </div>
</div> --}}

<div>
    <h1 class="text-xl font-bold">Laporan Table</h1>
</div>
