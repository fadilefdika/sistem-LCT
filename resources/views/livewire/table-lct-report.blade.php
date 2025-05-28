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
                    <th scope="col" class="px-4 py-3 w-40">Finder Name</th>
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
                         @php
                            $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
                            $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');
                        @endphp

                            <a href="{{ route($roleName === 'ehs' ? 'ehs.reporting.show.new' : 'admin.reporting.show.new', $laporan->id_laporan_lct) }}"
                                class="text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Detail
                            </a>

                            @php
                                // Cek apakah pengguna adalah EHS atau bukan
                                if (Auth::guard('ehs')->check()) {
                                    // Jika pengguna adalah EHS, ambil role dari relasi 'roles' di model EhsUser
                                    $userRole = optional(Auth::guard('ehs')->user()->roles->first())->name;
                                } else {
                                    // Jika pengguna bukan EHS, ambil role dari model User dengan roleLct
                                    $userRole = optional(auth()->user()->roleLct->first())->name;
                                }
                            @endphp

                            @if ($userRole == 'ehs')
                            <!-- Tombol Closed (dengan SweetAlert2) -->
                                <form id="form-close-{{ $laporan->id_laporan_lct }}"
                                    action="{{ route('admin.laporan-lct.closed', $laporan->id_laporan_lct) }}"
                                    method="POST" class="inline-block">
                                @csrf
                                <button type="button"
                                        class="text-green-700 hover:text-green-900 hover:underline flex items-center gap-1"
                                        onclick="confirmClose('{{ $laporan->id_laporan_lct }}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Closed
                                </button>
                                </form>

                            @endif



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
    function confirmClose(id) {
        Swal.fire({
            title: 'Are you sure you want to close this report?',
            text: "The report will be marked as closed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, close it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Ambil action dan token dari form
                const form = document.getElementById(`form-close-${id}`);
                const action = form.getAttribute('action');
                const csrf = form.querySelector('input[name="_token"]').value;

                fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error("Failed");
                    return res.json(); // opsional, tergantung response controller kamu
                })
                .then(data => {
                    Swal.fire({
                        title: 'Closed!',
                        text: 'The report has been successfully closed.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Refresh halaman setelah modal sukses ditutup
                    });
                })
                .catch(err => {
                    Swal.fire('Error', 'Failed to close the report.', 'error');
                });
            }
        });
    }

</script>

