<div class="w-full bg-[#F3F4F6] max-h-[calc(100vh)] pb-28 overflow-y-auto 
                    [&::-webkit-scrollbar]:w-1
                    [&::-webkit-scrollbar-track]:rounded-full
                    [&::-webkit-scrollbar-track]:bg-gray-100
                    [&::-webkit-scrollbar-thumb]:rounded-full
                    [&::-webkit-scrollbar-thumb]:bg-gray-300
                    dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                    dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
    <div class="my-3 max-h-min rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Laporan dari Pelapor (Full Width) -->
    <div class="col-span-2 bg-white p-6 rounded-xl shadow-md">
        <h5 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
            📝 Report from the Reporter
        </h5>
        <div class="w-full h-[2px] bg-gray-200 my-3"></div>
        <p class="text-gray-500 text-xs">Non-Conformity Finding</p>
        <p class="text-gray-900 font-semibold text-lg">{{$laporan->temuan_ketidaksesuaian}}</p>
    </div>

    <!-- Informasi Pelapor -->
    <div class="bg-white p-6 rounded-xl shadow-md border-l-4 border-blue-500">
        <div class="grid grid-cols-2 gap-3">
            <div>
                <p class="text-gray-500 text-xs flex items-center gap-1">
                    <i class="fas fa-user text-blue-500"></i> Reporter Name
                </p>
                <p class="text-gray-900 font-semibold text-sm mt-1">{{$laporan->user->fullname}}</p>
            </div>
            <div>
                <p class="text-gray-500 text-xs flex items-center gap-1">
                    <i class="fas fa-calendar-alt text-green-500"></i> Finding Date
                </p>
                <p class="text-gray-900 font-semibold text-sm mt-1">
                    {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->locale('en')->translatedFormat('l, d F Y') }}
                </p>                
            </div>
            
        </div>
    </div>

    <!-- Area Temuan -->
    <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-red-500">
        <p class="text-gray-500 text-xs flex items-center gap-1">
            <i class="fas fa-map-marker-alt text-red-500"></i> Finding Area
        </p>
        <p class="text-gray-900 font-semibold text-sm mt-1">{{$laporan->area}} - {{$laporan->detail_area}}</p>
    </div>

    <!-- Finding Category -->
    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 ease-in-out col-span-2 border-l-4 border-yellow-500">
        <p class="text-gray-500 text-base flex items-center gap-2">
            <i class="fa-solid fa-flag text-yellow-500"></i> Finding Category
        </p>
        <p class="text-gray-900 font-semibold mt-2 bg-yellow-100 p-2 rounded-lg hover:bg-yellow-200 transition-all duration-200 ease-in-out">
            {{$laporan->kategori->nama_kategori}}
        </p>
    </div>

    <!-- Rekomendasi Safety -->
    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-all border-l-4 border-green-300 duration-300 ease-in-out col-span-2">
        <p class="text-gray-500 text-base flex items-center gap-2">
            <i class="fa-solid fa-shield-alt text-green-500"></i> Safety Recommendation
        </p>
        <p class="text-gray-900 mt-2 text-justify leading-relaxed text-sm">
            {{$laporan->rekomendasi_safety}}
        </p>
    </div>

            <!-- Card Non-Conformity Image -->
            <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
                <p class="text-gray-700 text-lg font-semibold text-center">Non-Conformity Image</p>
                <div class="flex justify-center gap-1.5 mt-2">
                    @foreach ($bukti_temuan->take(5) as $gambar)
                        <img src="{{ $gambar }}" 
                            class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:scale-110 transition-transform"
                            alt="Finding Image"
                            onclick="openModal('{{ $gambar }}')">
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>