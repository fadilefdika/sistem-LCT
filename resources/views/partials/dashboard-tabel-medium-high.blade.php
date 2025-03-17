<h2 class="text-xl font-bold mb-4">{{ $title }}</h2>

<div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
    <div class="min-w-full divide-y divide-gray-300 rounded-lg bg-white">
        @if($laporans->isEmpty())
            <div class="px-4 py-3 text-center text-gray-500">
                No data available
            </div>
        @else
        @foreach($laporans as $laporan)
        @php
            // Mendecode bukti_temuan jika itu adalah JSON array
            $bukti_temuan = json_decode($laporan->bukti_temuan, true);
    
            // Jika bukti_temuan adalah array, buat URL untuk setiap gambar
            $bukti_temuan_urls = collect($bukti_temuan)->map(function ($path) {
                return asset('storage/' . $path); // Menambahkan URL ke gambar
            });
        @endphp
    
        <div class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out">
            <div class="flex items-center px-4 py-3">
                <!-- Iteration Number -->
                <div class="w-12 text-center font-semibold text-gray-800">
                    {{ $loop->iteration }}
                </div>
    
                <!-- Image and Details -->
                <div class="flex items-center space-x-4">
                    <!-- Jika bukti_temuan adalah array, tampilkan gambar pertama -->
                    @if($bukti_temuan_urls->isNotEmpty())
                        <img src="{{ $bukti_temuan_urls->first() }}" alt="Image" class="w-24 h-24 object-cover rounded-lg shadow-md">
                    @else
                        <span>No Image Available</span>
                    @endif
    
                    <div class="flex flex-col space-y-1">
                        <p class="text-gray-500 text-sm">{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('F j, Y') }}</p>
                        <p class="text-gray-700">{{ $laporan->temuan_ketidaksesuaian }} detected in <strong>{{ $laporan->area }}</strong> - {{ $laporan->detail_area }}</p>
                    </div>
                </div>
    
                <!-- Action Link -->
                <div class="px-4 py-3 flex items-center justify-center w-28">
                    <a href="{{ route('admin.progress-perbaikan.show', $laporan->id_laporan_lct) }}" class="text-blue-600 hover:underline text-sm font-semibold">
                        Detail
                    </a>
                </div>
            </div>
        </div>
    @endforeach
    
        @endif
    </div>
</div>
