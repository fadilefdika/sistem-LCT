<div class="max-h-min rounded-lg">

    <!-- Card Laporan -->
    <div class="bg-white p-5 rounded-xl shadow-md border ">
        <!-- Header -->
        <h5 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
            📝 Laporan dari Pelapor
        </h5>
        
        <!-- Garis Pemisah -->
        <div class="w-full h-[2px] bg-gray-200 my-3"></div>

        <!-- Isi Laporan -->
        <div class="flex flex-col space-y-1 mt-4">
            <p class="text-gray-500 text-xs">Temuan Ketidaksesuaian</p>
            <p class="text-gray-900 font-semibold text-lg">{{$laporan->temuan_ketidaksesuaian}}</p>
        </div>
    </div>


    <!-- Card Informasi Pelapor -->
    <div class="bg-white p-5 rounded-xl shadow-md mt-3 flex flex-row justify-around items-center">
        
        <!-- Nama Pelapor -->
        <div class="flex flex-col items-start">
            <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                <i class="fas fa-user text-blue-500"></i> <!-- Ikon User -->
                <p>Nama Pelapor</p>
            </div>
            <p class="text-gray-900 font-semibold text-sm mt-1">{{$laporan->user->fullname}}</p>
        </div>

        <!-- Garis Pemisah -->
        <div class="w-[2px] bg-gray-300 h-10 rounded-full"></div>

        <!-- Tanggal Temuan -->
        <div class="flex flex-col items-start">
            <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                <i class="fas fa-calendar-alt text-green-500"></i> <!-- Ikon Kalender -->
                <p>Tanggal Temuan</p>
            </div>
            <p class="text-gray-900 font-semibold text-sm mt-1">{{$laporan->tanggal_temuan}}</p>
        </div>

        <!-- Garis Pemisah -->
        <div class="w-[2px] bg-gray-300 h-10 rounded-full"></div>

        <!-- Area Temuan -->
        <div class="flex flex-col items-start">
            <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                <i class="fas fa-map-marker-alt text-red-500"></i> <!-- Ikon Lokasi -->
                <p>Area Temuan</p>
            </div>
            <p class="text-gray-900 font-semibold text-sm mt-1">{{$laporan->area}} - {{$laporan->detail_area}}</p>
        </div>

    </div>

    <!-- Card Kategori Temuan -->
    <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
        <div class="flex items-center space-x-2">
            <i class="fa-solid fa-flag text-yellow-500 text-lg"></i>
            <p class="text-gray-500 text-xs">Kategori Temuan</p>
        </div>
        <p class="text-gray-900 font-semibold mt-2 bg-yellow-100 p-2 rounded-lg hover:bg-yellow-200 transition-all duration-200 ease-in-out">{{$laporan->kategori_temuan}}</p>
    </div>


    <!-- Card Rekomendasi Safety -->
    <div class="bg-white p-4 rounded-lg border-gray-300 mt-3 shadow-md hover:shadow-xl transition-all duration-300 ease-in-out">
        <div class="flex items-center space-x-2 mb-2">
            <i class="fa-solid fa-shield-alt text-green-500 text-lg"></i>
            <p class="text-gray-500 text-xs">Rekomendasi Safety</p>
        </div>
        <p class="text-gray-900 mt-2 text-justify leading-relaxed text-sm">
            {{$laporan->rekomendasi_safety}}
        </p>
    </div> 

    <!-- Card Gambar Temuan -->
    <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
        <p class="text-gray-700 text-lg font-semibold">Gambar Temuan</p>
        <div class="grid grid-cols-5 gap-2 mt-2">
            @foreach ($bukti_temuan->take(5) as $gambar)
                <img src="{{ $gambar }}" 
                    class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:scale-110 transition-transform"
                    alt="Bukti Temuan"
                    onclick="openModal('{{ $gambar }}')">
            @endforeach
        </div>
    </div>

    <!-- Modal Preview -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 transition-opacity duration-300">
        <div class="relative bg-white p-1 rounded-lg shadow-lg">
            <!-- Tombol Close -->
            <button id="closeModalBtn"
                class="absolute -top-4 -right-4 bg-gray-800 text-white rounded-full w-10 h-10 flex items-center justify-center text-2xl font-bold shadow-md hover:bg-red-600 transition cursor-pointer"
                onclick="closeModal()">
                &times;
            </button>
            
            <!-- Gambar di Modal -->
            <img id="modalImage" class="w-[600px] h-[500px] object-cover rounded-lg">
        </div>
    </div>
</div>