<div class="max-h-min rounded-lg">

    <!-- Card Laporan -->
    <div class="bg-white p-5 rounded-xl shadow-md border ">
        <!-- Header -->
        <h5 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
            📝 Finding Report
        </h5>
        
        <!-- Garis Pemisah -->
        <div class="w-full h-[2px] bg-gray-200 my-3"></div>

        <!-- Isi Laporan -->
        <div class="flex flex-col space-y-1 mt-4">
            <p class="text-gray-500 text-xs">Non-Conformity Findings</p>
            <p class="text-gray-900 font-semibold text-lg">{{$laporan->temuan_ketidaksesuaian}}</p>
        </div>
    </div>


    <!-- Informasi Pelapor & Temuan - 1 Card -->
    <div class="bg-white p-6 rounded-2xl shadow-md border-l-4 border-blue-500 hover:shadow-lg transition w-full mt-4">
        <h3 class="text-sm font-semibold text-blue-600 flex items-center gap-2 mb-6">
            <i class="fas fa-info-circle"></i> Report Details
        </h3>

        <!-- Baris 1: Finder Name & Finding Date -->
        <div class="flex flex-col md:flex-row justify-between gap-6 mb-6">
            <!-- Finder Name -->
            <div class="flex-1">
                <p class="text-gray-500 text-xs flex items-center gap-2 mb-1">
                    <i class="fas fa-user text-blue-500"></i> Finder Name
                </p>
                <p class="text-gray-900 font-medium text-xs">
                    {{ $laporan->user->fullname }}
                </p>
            </div>


            <!-- Finding Date -->
            <div class="flex-1">
                <p class="text-gray-500 text-xs flex items-center gap-2 mb-1">
                    <i class="fas fa-calendar-alt text-green-500"></i> Date of Finding
                </p>
                <p class="text-gray-900 font-medium text-xs">
                    {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->locale('en')->translatedFormat('l, d F Y') }}
                </p>
            </div>
        </div>


        <!-- Baris 2: Finding Area & Category -->
        <div class="flex flex-col md:flex-row justify-between gap-6">
            <!-- Finding Area -->
            <div class="flex-1">
                <p class="text-gray-500 text-xs flex items-center gap-2 mb-1">
                    <i class="fas fa-map-marker-alt text-red-500"></i> Finding Area
                </p>
                <p class="text-gray-900 font-medium text-xs break-words">
                    @if($laporan->area && $laporan->area->nama_area && $laporan->detail_area)
                        {{ $laporan->area->nama_area }} - {{ $laporan->detail_area }}
                    @else
                        <span class="text-gray-400">No area details available</span>
                    @endif
                </p>
            </div>


            <!-- Finding Category -->
            <div class="flex-1">
                <p class="text-gray-500 text-xs flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-flag text-yellow-500"></i> Finding Category
                </p>
                <p class="text-gray-900 font-medium text-xs bg-yellow-100 px-3 py-2 rounded-lg inline-block">
                    {{ $laporan->kategori->nama_kategori ?? '-' }}
                </p>
            </div>
        </div>
    </div>



    <!-- Card Rekomendasi Safety -->
    <div class="bg-white p-4 rounded-lg border-l-4 border-green-300 mt-3 shadow-md hover:shadow-xl transition-all duration-300 ease-in-out">
        <div class="flex items-center space-x-2 mb-2">
            <i class="fa-solid fa-shield-alt text-green-500 text-lg"></i>
            <p class="text-gray-500 text-xs">Safety Recommendation</p>
        </div>
        <p class="text-gray-900 mt-2 text-justify leading-relaxed text-sm">
            {{$laporan->rekomendasi_safety}}
        </p>
    </div> 

    <!-- Card Non-Conformity Image -->
    <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
        <p class="text-gray-700 text-lg font-semibold">Non-Conformity Image</p>
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