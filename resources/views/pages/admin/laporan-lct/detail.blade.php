<x-app-layout>
    {{ Breadcrumbs::render('laporan-lct.detail') }}
    <div class="max-h-screen flex justify-center items-center">
        <div class="grid md:grid-cols-2 justify-center w-full">
            <!-- Card Laporan dari Pelapor -->
            <div class="max-w-full mx-auto bg-[#F3F4F6] overflow-hidden h-[487px] overflow-y-auto 
                        [&::-webkit-scrollbar]:w-1
                        [&::-webkit-scrollbar-track]:rounded-full
                        [&::-webkit-scrollbar-track]:bg-gray-100
                        [&::-webkit-scrollbar-thumb]:rounded-full
                        [&::-webkit-scrollbar-thumb]:bg-gray-300
                        dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                        dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                
                <div class="m-3 max-h-min rounded-lg">

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
                            <p class="text-gray-900 font-semibold text-lg">Kerusakan Tempat Sampah</p>
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
                            <p class="text-gray-900 font-semibold text-sm mt-1">Aziz</p>
                        </div>

                        <!-- Garis Pemisah -->
                        <div class="w-[2px] bg-gray-300 h-10 rounded-full"></div>

                        <!-- Tanggal Temuan -->
                        <div class="flex flex-col items-start">
                            <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                                <i class="fas fa-calendar-alt text-green-500"></i> <!-- Ikon Kalender -->
                                <p>Tanggal Temuan</p>
                            </div>
                            <p class="text-gray-900 font-semibold text-sm mt-1">22-01-2024</p>
                        </div>

                        <!-- Garis Pemisah -->
                        <div class="w-[2px] bg-gray-300 h-10 rounded-full"></div>

                        <!-- Area Temuan -->
                        <div class="flex flex-col items-start">
                            <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                                <i class="fas fa-map-marker-alt text-red-500"></i> <!-- Ikon Lokasi -->
                                <p>Area Temuan</p>
                            </div>
                            <p class="text-gray-900 font-semibold text-sm mt-1">Gudang A</p>
                        </div>

                    </div>

                    <!-- Card Kategori Temuan -->
                    <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-flag text-yellow-500 text-lg"></i>
                            <p class="text-gray-500 text-xs">Kategori Temuan</p>
                        </div>
                        <p class="text-gray-900 font-semibold mt-2 bg-yellow-100 p-2 rounded-lg hover:bg-yellow-200 transition-all duration-200 ease-in-out">Kondisi Tidak Aman</p>
                    </div>


                    <!-- Card Rekomendasi Safety -->
                    <div class="bg-white p-4 rounded-lg border-gray-300 mt-3 shadow-md hover:shadow-xl transition-all duration-300 ease-in-out">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fa-solid fa-shield-alt text-green-500 text-lg"></i>
                            <p class="text-gray-500 text-xs">Rekomendasi Safety</p>
                        </div>
                        <p class="text-gray-900 mt-2 text-justify leading-relaxed text-sm">
                            Lorem ipsum dolor sit amet consectetur, adipisicing elit. Expedita voluptates, maxime assumenda 
                            voluptatibus, iste quidem natus dignissimos nesciunt vero voluptate omnis officiis commodi fuga vel. 
                            Mollitia earum, ipsum deserunt tempore in reiciendis ipsa obcaecati expedita!
                        </p>
                    </div>

                    <!-- Card Gambar Temuan -->
                    <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
                        <p class="text-gray-700 text-lg font-semibold">Gambar Temuan</p>
                        <div class="relative mt-2">
                            <img src="{{ asset('images/user-36-05.jpg') }}" 
                                class="w-full h-auto rounded-md shadow-md object-cover" 
                                alt="Gambar Temuan">
                        </div>
                    </div>

                </div>
            </div>

            
        
            <!-- Form Laporan Temuan -->
            <div class="max-w-full bg-[#F3F4F6] overflow-hidden shadow-md p-3 h-[487px] overflow-y-auto [&::-webkit-scrollbar]:w-1
                [&::-webkit-scrollbar-track]:rounded-full
                [&::-webkit-scrollbar-track]:bg-gray-100
                [&::-webkit-scrollbar-thumb]:rounded-full
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                    <div class="bg-white p-5 max-h-min rounded-lg shadow-lg">
                        <div class="bg-primary text-black text-center py-4 px-7 rounded-t-lg">
                            <h5 class="text-xl font-bold">Formulir Pengajuan Laporan Ketidaksesuaian ke PIC</h5>
                        </div>

                        <div class="w-full h-[2px] bg-gray-200 px-3"></div>

                        <div class="p-6">
                            <form action="#{{-- route('laporan.store') --}}" method="POST">
                                @csrf
                                <div class="space-y-6">
                                    <!-- Form fields -->
                                    <div class="mb-4">
                                        <label for="temuan_ketidaksesuaian" class="block text-sm font-medium text-gray-700 mb-1">Temuan Ketidaksesuaian</label>
                                        <input type="text" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="temuan_ketidaksesuaian" name="temuan_ketidaksesuaian" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="area_temuan" class="block text-sm font-medium text-gray-700 mb-1">Area Temuan</label>
                                        <input type="text" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="area_temuan" name="area_temuan" required>
                                    </div>
                                    {{-- <div class="mb-4">
                                        <label for="departemen" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                                        <select class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="departemen" name="departemen" required>
                                            <option value="">Pilih Departemen</option>
                                            <option value="produksi">Produksi</option>
                                            <option value="keamanan">Keamanan</option>
                                            <option value="lingkungan">Lingkungan</option>
                                        </select>
                                    </div> --}}
                                    <div class="mb-4">
                                        <label for="tanggal_temuan" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Temuan</label>
                                        <input type="date" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="tanggal_temuan" name="tanggal_temuan" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="nama_pic" class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
                                        <select class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="nama_pic" name="nama_pic" required>
                                            <option value="">Pilih PIC</option>
                                            <option value="pic1">PIC 1</option>
                                            <option value="pic2">PIC 2</option>
                                            <option value="pic3">PIC 3</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="kategori_temuan" class="block text-sm font-medium text-gray-700 mb-1">Kategori Temuan</label>
                                        <select class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="kategori_temuan" name="kategori_temuan" required>
                                            <option value="">Pilih Kategori Temuan</option>
                                            <option value="produksi">Produksi</option>
                                            <option value="keamanan">Keamanan</option>
                                            <option value="lingkungan">Lingkungan</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="tingkat_bahaya" class="block text-sm font-medium text-gray-700 mb-1">Tingkat Bahaya</label>
                                        <select class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="tingkat_bahaya" name="tingkat_bahaya" required>
                                            <option value="">Pilih Tingkat Bahaya</option>
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label for="batas_waktu" class="block text-sm font-medium text-gray-700 mb-1">Batas Waktu Perbaikan</label>
                                        <input type="date" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="batas_waktu" name="batas_waktu" required>
                                    </div>

                                    <!-- Rekomendasi -->
                                    <div class="mb-4">
                                        <label for="rekomendasi" class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi</label>
                                        <textarea class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="rekomendasi" name="rekomendasi" rows="4" required></textarea>
                                    </div>

                                    <!-- Submit button -->
                                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 mt-4 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                        Kirim Laporan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
