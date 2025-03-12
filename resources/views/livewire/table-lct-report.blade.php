<div class="bg-white dark:bg-gray-800 p-6 relative shadow-md sm:rounded-lg overflow-y-auto">
    <div class="flex flex-row flex-wrap align-items-center gap-3 p-3 border rounded shadow-sm bg-white mb-4">   
        <!-- Filter Tingkat Bahaya -->
        <div class="flex flex-col" style="min-width: 180px;">
            <label for="filterKategori" class="form-label fw-bold text-muted mb-1">Filter Kategori</label>
            <select wire:model="filterKategori" wire:change="applyFilter" id="filterKategori" class="form-select">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                @endforeach

            </select>
        </div>
        <!-- Loading Indicator -->
        <div wire:loading wire:target="filterKategori" class="text-sm text-muted mt-8">
            <i class="spinner-border spinner-border-sm"></i> Loading...
        </div>
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr class="text-left text-sm font-semibold text-gray-600">
                    <th scope="col" class="px-4 py-3 w-12 text-center">No</th>
                    <th scope="col" class="px-4 py-3 w-40">Reporter Name</th>
                    <th scope="col" class="px-4 py-3 w-60">Non-Conformity Findings</th>
                    <th scope="col" class="px-4 py-3 w-32">Date</th>
                    <th scope="col" class="px-4 py-3 w-36">Area</th>
                    <th scope="col" class="px-4 py-3 w-24 text-center">Photo</th>
                    <th scope="col" class="px-4 py-3 w-36">Category</th>
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
                                <span class="text-gray-500 italic">Image not found</span>
                            @endif
                        @else
                            <span class="text-gray-500 italic">Image not found</span>
                        @endif
                    </td>                        
                    <td class="px-4 py-3 text-gray-800">{{ $laporan->kategori ? $laporan->kategori->nama_kategori : 'Tidak ada kategori' }}</td>
                    <td class="px-4 py-3 flex items-center justify-center gap-2">
                        <a href="{{ route('admin.laporan-lct.show', $laporan->id_laporan_lct) }}" class="text-blue-600 hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-6 text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6"></path>
                            </svg>
                            <p class="text-sm">No reports are available.</p>
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
