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

                <div class="hover:bg-gray-50 text-[11px] transition duration-200 ease-in-out border-b px-3 py-2">
                    <div class="flex flex-row items-start gap-3 flex-wrap">

                        <!-- Number -->
                        <div class="w-6 text-gray-800 font-semibold flex-shrink-0 pt-1">
                            {{ $loop->iteration }}
                        </div>

                        <!-- Image + Text + Action -->
                        <div class="flex flex-1 flex-col sm:flex-row sm:items-start md:items-center gap-3 w-full">

                            <!-- Image -->
                            @if($bukti_temuan_urls->isNotEmpty())
                                <img src="{{ $bukti_temuan_urls->first() }}" alt="Evidence Image"
                                    class="w-12 h-12 object-cover rounded-md shadow-sm border border-gray-100">
                            @else
                                <div class="w-12 h-12 flex items-center justify-center bg-gray-100 text-gray-400 rounded-md border">
                                    No Image
                                </div>
                            @endif

                            <!-- Text Details -->
                            <div class="flex-1 space-y-1">
                                <p class="text-gray-500">
                                    Finding date: {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('F j, Y') }}
                                </p>
                                <p class="text-gray-800 leading-tight">
                                    {{ $laporan->temuan_ketidaksesuaian }} found in 
                                    <strong>{{ $laporan->area->nama_area }}</strong>
                                    <span class="text-gray-600">— {{ $laporan->detail_area }}</span>
                                </p>
                            </div>

                            <!-- Action -->
                            <div class="text-left sm:text-right pt-1 sm:pt-0 min-w-[90px]">
                                @php
                                    if (Auth::guard('ehs')->check()) {
                                        $user = Auth::guard('ehs')->user();
                                        $roleName = 'ehs';
                                    } else {
                                        $user = Auth::guard('web')->user();
                                        $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
                                    }
                                @endphp

                                @if (in_array($roleName, ['ehs', 'manajer', 'user']))
                                    <a href="{{ route($roleName === 'ehs' ? 'ehs.reporting.show' : 'admin.reporting.show', $laporan->id_laporan_lct) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                        View Details
                                    </a>
                                @else
                                    <a href="{{ route('admin.manajemen-lct.show', $laporan->id_laporan_lct) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                        View Details
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

