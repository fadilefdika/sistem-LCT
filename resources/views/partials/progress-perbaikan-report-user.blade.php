<div class="w-full bg-[#F3F4F6] max-h-[calc(100vh)] pb-28 overflow-y-auto 
    [&::-webkit-scrollbar]:w-1
    [&::-webkit-scrollbar-track]:rounded-full
    [&::-webkit-scrollbar-track]:bg-gray-100
    [&::-webkit-scrollbar-thumb]:rounded-full
    [&::-webkit-scrollbar-thumb]:bg-gray-300
    dark:[&::-webkit-scrollbar-track]:bg-neutral-700
    dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">

    <div class="container mx-auto px-3 py-3">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <!-- Report from Reporter -->
            <div class="md:col-span-2 bg-white p-4 md:p-6 rounded-xl shadow-sm hover:shadow-md transition">
                <h5 class="text-base md:text-lg font-semibold text-gray-900 flex items-center gap-2 tracking-wide">
                    📝 Report from the Finder
                </h5>
                <div class="w-full h-[1.5px] bg-gray-200 my-3"></div>
                <p class="text-gray-500 text-xs md:text-sm">Non-Conformity Finding</p>
                <p class="text-gray-900 font-semibold text-sm md:text-base mt-1 leading-snug">
                    {{ $laporan->temuan_ketidaksesuaian }}
                </p>
            </div>
        
            <!-- Informasi Pelapor -->
            <div class="md:col-span-2 bg-white p-4 md:p-6 rounded-xl shadow-sm border-l-4 w-full border-blue-500 hover:shadow-md transition">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-500 text-xs flex items-center gap-1">
                            <i class="fas fa-user text-blue-500"></i> Finder Name
                        </p>
                        <p class="text-gray-900 font-semibold text-sm mt-1 leading-tight">
                            {{ $laporan->user->fullname }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-xs flex items-center gap-1">
                            <i class="fas fa-calendar-alt text-green-500"></i> Finding Date
                        </p>
                        <p class="text-gray-900 font-semibold text-sm mt-1 leading-tight">
                            {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->locale('en')->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                </div>
            </div>
        
            <!-- Finding Area -->
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border-l-4 border-red-500 hover:shadow-md transition md:col-span-2">
                <p class="text-gray-500 text-xs flex items-center gap-1">
                    <i class="fas fa-map-marker-alt text-red-500"></i> Finding Area
                </p>
                <p class="text-gray-900 font-semibold text-sm mt-1 whitespace-normal break-words overflow-hidden text-ellipsis max-h-[3rem]">
                    @if($laporan->area && $laporan->area->nama_area && $laporan->detail_area)
                        {{ $laporan->area->nama_area }} - {{ $laporan->detail_area }}
                    @else
                        <span class="text-gray-400">No area details available</span>
                    @endif
                </p>
            </div>
        
            <!-- Finding Category -->
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border-l-4 border-yellow-500 hover:shadow-md transition md:col-span-2">
                <p class="text-gray-500 text-xs flex items-center gap-1">
                    <i class="fa-solid fa-flag text-yellow-500"></i> Finding Category
                </p>
                <p class="text-gray-900 font-semibold mt-2 text-sm md:text-base bg-yellow-100 p-2 rounded-lg hover:bg-yellow-200 transition">
                    {{ $laporan->kategori->nama_kategori }}
                </p>
            </div>
        
            <!-- Safety Recommendation -->
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border-l-4 border-green-400 hover:shadow-md transition md:col-span-2">
                <p class="text-gray-500 text-xs flex items-center gap-1">
                    <i class="fa-solid fa-shield-alt text-green-500"></i> Safety Recommendation
                </p>
                <p class="text-gray-900 mt-2 text-justify text-sm leading-relaxed">
                    {{ $laporan->rekomendasi_safety }}
                </p>
            </div>
        
            <!-- Non-Conformity Images -->
            <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm hover:shadow-md transition md:col-span-2">
                <p class="text-gray-700 text-base md:text-lg font-semibold text-center">Non-Conformity Image</p>
                <div class="flex flex-wrap justify-center gap-3 mt-4">
                    @foreach ($bukti_temuan->take(5) as $gambar)
                        <img src="{{ $gambar }}" 
                            alt="Finding Image"
                            onclick="openModal('{{ $gambar }}')"
                            class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-lg cursor-pointer hover:scale-110 transition-transform duration-200" />
                    @endforeach
                </div>
            </div>
        
        </div>
        
    </div>
</div>
