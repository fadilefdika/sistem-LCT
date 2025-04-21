<div class="bg-white dark:bg-gray-800 p-6 relative shadow-md sm:rounded-lg overflow-y-auto">
    <div class="flex flex-row flex-wrap align-items-center gap-3 p-3 border rounded shadow-sm bg-white mb-4">   
        <!-- Filter Tingkat Bahaya -->
        <div class="flex flex-col" style="min-width: 180px;">
            <label for="filterKategori" class="form-label fw-bold text-muted mb-1">Category</label>
            <select wire:model="filterKategori" wire:change="applyFilter" id="filterKategori" class="form-select">
                <option value="">All Category</option>
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
                    <!-- Nomor Urut -->
                    <td class="px-4 py-3 text-center font-semibold text-gray-800 w-12">
                        {{ $laporans->firstItem() + $index }}
                    </td>
            
                    <!-- Nama User -->
                    <td class="px-4 py-3 text-gray-800 whitespace-nowrap">
                        {{ $laporan->user->fullname }}
                    </td>
            
                    <!-- Temuan Ketidaksesuaian -->
                    <td class="px-4 py-3 text-gray-800 max-w-xs break-words truncate cursor-pointer relative group">
                        <span class="temuan-clamp" title="{{ $laporan->temuan_ketidaksesuaian }}">
                            {{ $laporan->temuan_ketidaksesuaian }}
                        </span>
                    </td>
            
                    <!-- Tanggal Temuan -->
                    <td class="px-4 py-3 text-gray-800 text-center w-32">
                        {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('d F Y') }}
                    </td>
            
                    <!-- Area -->
                    <td class="px-4 py-3 text-gray-800 whitespace-nowrap">
                        {{ $laporan->area->nama_area ?? '-' }}
                    </td>
            
                    <!-- Bukti Temuan -->
                    <td class="px-4 py-3 text-center w-24">
                        @if (!empty($laporan->bukti_temuan))
                            @php
                                $gambar = json_decode($laporan->bukti_temuan, true)[0] ?? null;
                            @endphp
                            @if ($gambar)
                                <img src="{{ asset('storage/' . $gambar) }}" class="w-20 h-20 object-cover rounded-lg shadow">
                            @else
                                <span class="text-gray-500 italic">No Image</span>
                            @endif
                        @else
                            <span class="text-gray-500 italic">No Image</span>
                        @endif
                    </td>
            
                    <!-- Kategori -->
                    <td class="px-4 py-3 text-gray-800 text-center">
                        {{ $laporan->kategori ? $laporan->kategori->nama_kategori : 'No categories' }}
                    </td>
            
                    <!-- Aksi -->
                    <td class="px-4 py-3 text-center w-32">
                        <div class="flex flex-row items-center justify-center space-x-3">
                            <!-- Tombol Detail -->
                            <a href="{{ route('admin.laporan-lct.show', $laporan->id_laporan_lct) }}"
                            class="text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Detail
                            </a>

                            <!-- Tombol Delete -->
                            <a href="javascript:void(0)" 
                            class="text-red-600 hover:text-red-800 hover:underline flex items-center gap-1 delete-laporan" 
                            data-id="{{ $laporan->id_laporan_lct }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Delete
                            </a>



                        </div>
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

<script>
    document.querySelectorAll('.delete-laporan').forEach(btn => {
        btn.addEventListener('click', () => {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This report will be deleted and cannot be recovered.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/laporan-lct/${btn.dataset.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error("Failed to delete");
                        return res.json();
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 2000, // Modal auto-close in 2 seconds
                            timerProgressBar: true
                        }).then(() => {
                            window.location.reload(); // Refresh after success
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'Something went wrong while deleting.'
                        });
                    });
                }
            });
        });
    });
</script>

