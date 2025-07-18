<div class="overflow-x-auto rounded-lg border border-gray-200 text-sm">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr class="text-left font-semibold text-gray-600">
                <th scope="col" class="px-3 py-2 text-[11px] w-8 text-center">No</th>
                <th scope="col" class="px-3 py-2 text-[11px] w-24">Date Finding</th>
                <th scope="col" class="px-3 py-2 text-[11px] w-28">Area</th>
                <th scope="col" class="px-3 py-2 text-[11px] w-28">Category</th>
                <th scope="col" class="px-3 py-2 text-[11px] w-24 text-center">Actions</th>
            </tr>                
        </thead>                
        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse($laporans as $index => $laporan)
            <tr class="hover:bg-gray-100 transition duration-200 ease-in-out">
                <!-- Nomor Urut -->
                <td class="px-3 py-2 text-[11px] text-center font-semibold text-gray-800 w-8">
                    {{ $index + 1 }}
                </td>

                <!-- Tanggal Temuan -->
                <td class="px-3 py-2 text-[11px] text-gray-800 whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('d M Y') }}
                </td>
        
                <!-- Area -->
                <td class="px-3 py-2 text-[11px] text-gray-800 whitespace-nowrap">
                    {{ $laporan->area->nama_area ?? '-' }}
                </td>
                    
                <!-- Kategori -->
                <td class="px-3 py-2 text-[11px] text-gray-800 whitespace-nowrap">
                    {{
                        \Illuminate\Support\Str::startsWith($laporan->kategori?->nama_kategori, '5S')
                            ? '5S'
                            : ($laporan->kategori?->nama_kategori ?? '-')
                    }}
                </td>
                
                <!-- Aksi -->
                <td class="px-3 py-2 text-[11px] text-center w-24">
                    <div class="flex flex-col space-y-1 items-center">
                        <!-- Tombol Detail -->
                        @php
                            $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
                            $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');
                        @endphp

                        <a href="{{ route($roleName === 'ehs' ? 'ehs.reporting.show.new' : 'admin.reporting.show.new', $laporan->id_laporan_lct) }}"
                            class="text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1">
                            Detail
                        </a>


                        @php
                            // Cek apakah pengguna adalah EHS atau bukan
                            if (Auth::guard('ehs')->check()) {
                                $user = Auth::guard('ehs')->user();
                                $userRole = 'ehs';
                            } else {
                                $user = Auth::guard('web')->user();
                                // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
                                $userRole = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
                            }
                        @endphp

                        @if ($userRole == 'ehs')
                        <!-- Tombol Closed (dengan SweetAlert2) -->
                            <form id="form-close-{{ $laporan->id_laporan_lct }}"
                                action="{{ route('ehs.laporan-lct.closed', $laporan->id_laporan_lct) }}"
                                method="POST" class="inline-block">
                            @csrf
                            <button type="button"
                                    class="text-green-700 hover:text-green-900 hover:underline flex items-center gap-1"
                                    onclick="confirmClose('{{ $laporan->id_laporan_lct }}')">
                                Closed
                            </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-4 text-gray-500">
                    <div class="flex flex-col items-center">
                        <svg class="w-8 h-8 mb-1 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6"></path>
                        </svg>
                        <p class="text-sm">No reports available</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
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

