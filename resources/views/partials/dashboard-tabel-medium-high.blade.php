<div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
    <div class="min-w-full divide-y divide-gray-300 bg-white rounded-lg">
        @if($laporans->isEmpty())
            <div class="flex flex-col items-center justify-center px-4 py-8 text-gray-500">
                <i class="fa-solid fa-face-smile text-3xl mb-2"></i>
                <p class="text-[11px] font-medium">All in good condition 🎉</p>
                <p class="text-[11px] text-gray-400">There are no reports at this time. Enjoy your day!</p>
            </div>
        @else
            @foreach($laporans as $laporan)
                @php
                    $bukti_temuan = json_decode($laporan->bukti_temuan, true);
                    $bukti_temuan_urls = collect($bukti_temuan)->map(fn($path) => asset('storage/' . $path));
                @endphp

                <div class="hover:bg-gray-50 text-[11px] transition duration-200 ease-in-out">
                    <div class="flex items-center px-3 py-2 space-x-3">
                        <!-- Number -->
                        <div class="w-8 text-center font-semibold text-gray-800">
                            {{ $loop->iteration }}
                        </div>

                        <!-- Image -->
                        @if($bukti_temuan_urls->isNotEmpty())
                            <img src="{{ $bukti_temuan_urls->first() }}" alt="Evidence Image" class="w-16 h-16 object-cover rounded-md shadow-sm border border-gray-100">
                        @else
                            <div class="w-16 h-16 flex items-center justify-center bg-gray-100 text-[11px] text-gray-400 rounded-md border">
                                No Image
                            </div>
                        @endif

                        <!-- Text Details -->
                        <div class="flex-1">
                            <p class="text-gray-500 text-[11px]">Finding date: {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('F j, Y') }}</p>
                            <p class="text-gray-800 text-[11px]">
                                {{ $laporan->temuan_ketidaksesuaian }} found in 
                                <br>
                                <strong class="text-[11px]">{{ $laporan->area->nama_area }}</strong>
                                <span class="text-gray-600 text-[11px]">— {{ $laporan->detail_area }}</span>
                            </p>
                        </div>

                        @php
                            // Cek apakah pengguna menggunakan guard 'ehs' atau 'web' untuk pengguna biasa
                            if (Auth::guard('ehs')->check()) {
                                // Jika pengguna adalah EHS, ambil role dari relasi 'roles' pada model EhsUser
                                $user = Auth::guard('ehs')->user();
                                $roleName = optional($user->roles->first())->name;
                            } else {
                                // Jika pengguna adalah User biasa, ambil role dari relasi 'roleLct' pada model User
                                $user = Auth::user();
                                $roleName = optional($user->roleLct->first())->name;
                            }
                        @endphp
                        
                        <!-- Action -->
                        <td class="w-20 text-right">
                            @if (in_array($roleName, ['ehs', 'manajer', 'user']))
                                <a href="{{ route(
                                    $roleName === 'ehs' 
                                        ? 'ehs.reporting.show' 
                                        : 'admin.reporting.show', 
                                    $laporan->id_laporan_lct
                                ) }}" 
                                class="text-blue-600 hover:text-blue-800 text-[11px] font-medium">
                                    View Details
                                </a>
                            @else
                                <a href="{{ route('admin.manajemen-lct.show', $laporan->id_laporan_lct) }}" 
                                class="text-blue-600 hover:text-blue-800 text-[11px] font-medium">
                                    View Details
                                </a>
                            @endif
                        </td>                        
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

