<h2 class="text-xl font-bold mb-4">{{ $title }}</h2>

<div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
    <div class="min-w-full divide-y divide-gray-300 bg-white rounded-lg">
        @if($laporans->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-12 text-gray-500">
                <i class="fa-solid fa-face-smile text-4xl mb-3"></i>
                <p class="text-sm font-medium">All in good condition 🎉</p>
                <p class="text-xs text-gray-400">There are no reports at this time. Enjoy your day!</p>
            </div>
        @else
            @foreach($laporans as $laporan)
                @php
                    $bukti_temuan = json_decode($laporan->bukti_temuan, true);
                    $bukti_temuan_urls = collect($bukti_temuan)->map(fn($path) => asset('storage/' . $path));
                @endphp

                <div class="hover:bg-gray-50 text-sm transition duration-200 ease-in-out">
                    <div class="flex items-center px-4 py-3 space-x-4">
                        <!-- Number -->
                        <div class="w-10 text-center font-semibold text-gray-800">
                            {{ $loop->iteration }}
                        </div>

                        <!-- Image -->
                        @if($bukti_temuan_urls->isNotEmpty())
                            <img src="{{ $bukti_temuan_urls->first() }}" alt="Evidence Image" class="w-20 h-20 object-cover rounded-md shadow-sm border border-gray-100">
                        @else
                            <div class="w-20 h-20 flex items-center justify-center bg-gray-100 text-xs text-gray-400 rounded-md border">
                                No Image
                            </div>
                        @endif

                        <!-- Text Details -->
                        <div class="flex-1">
                            <p class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('F j, Y') }}</p>
                            <p class="text-gray-800">
                                {{ $laporan->temuan_ketidaksesuaian }} found in 
                                <strong>{{ $laporan->area->nama_area }}</strong>
                                <span class="text-gray-600">— {{ $laporan->detail_area }}</span>
                            </p>
                        </div>

                        @php
                            $user = Auth::user();
                            $roleName = optional($user->roleLct->first())->name;
                        @endphp
                        
                        <!-- Action -->
                        <div class="w-24 text-right">
                            @if(in_array($roleName, ['ehs', 'manajer','user']))
                                <a href="{{ route('admin.progress-perbaikan.show', $laporan->id_laporan_lct) }}" 
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View Details
                                </a>
                            @else
                                <a href="{{ route('admin.manajemen-lct.show', $laporan->id_laporan_lct) }}" 
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View Details
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
