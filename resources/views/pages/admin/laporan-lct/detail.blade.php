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

                                    {{-- Area Temuan --}}
                                    <div class="mb-4">
                                        <label for="area_temuan" class="block text-sm font-medium text-gray-700 mb-1">Area Temuan</label>
                                        <input type="text" class="flex justify-between w-full p-3 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="area_temuan" name="area_temuan" required>
                                    </div>

                                    {{-- Kategori Temuan --}}
                                    <div x-data="{ open: false, selected: '' }" class="relative mb-4">
                                        <label for="kategori_temuan" class="block text-sm font-medium text-gray-700 mb-1">
                                            Kategori Temuan
                                        </label>
                                        
                                        <!-- Tombol Dropdown -->
                                        <div @click="open = !open" class="flex justify-between w-full p-3 border border-gray-800 rounded-md cursor-pointer bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                                            <span x-text="selected || 'Pilih Kategori Temuan'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M6 9l6 6 6-6"></path>
                                            </svg>
                                        </div>
                                    
                                        <!-- Dropdown List -->
                                        <ul 
                                            x-show="open" 
                                            x-transition:enter="transition ease-out duration-200 transform"
                                            x-transition:enter-start="opacity-0 -translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-out duration-200"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                            class="absolute z-10 w-full mt-1 bg-white border rounded-md shadow-lg max-h-48 overflow-auto"
                                            x-cloak
                                        >
                                            <li @click="selected = 'Produksi'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Produksi</li>
                                            <li @click="selected = 'Keamanan'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Keamanan</li>
                                            <li @click="selected = 'Lingkungan'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Lingkungan</li>
                                        </ul>
                                    
                                        <!-- Input Hidden untuk Submit Form -->
                                        <input type="hidden" name="kategori_temuan" x-model="selected">
                                    </div>
                                    

                                    {{-- Tanggal Temuan --}}
                                    <div class="mb-4">
                                        <label for="tanggal_temuan" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Temuan</label>
                                        <input type="date" class="flex justify-between w-full p-3 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="tanggal_temuan" name="tanggal_temuan" required>
                                    </div>

                                    {{-- Departemen --}}
                                    <div x-data="{ open: false, selected: '' }" class="relative mb-4">
                                        <label for="departemen" class="block text-sm font-medium text-gray-700 mb-1">
                                            Departemen
                                        </label>
                                        
                                        <!-- Tombol Dropdown -->
                                        <div @click="open = !open" class="flex justify-between w-full p-3 border border-gray-800 rounded-md cursor-pointer bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                                            <span x-text="selected || 'Pilih Departemen'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M6 9l6 6 6-6"></path>
                                            </svg>
                                        </div>
                                    
                                        <!-- Dropdown List -->
                                        <ul 
                                            x-show="open" 
                                            x-transition:enter="transition ease-out duration-200 transform"
                                            x-transition:enter-start="opacity-0 -translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-out duration-200"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                            class="absolute z-10 w-full mt-1 bg-white border rounded-md shadow-lg max-h-48 overflow-auto"
                                            x-cloak
                                        >
                                            <li @click="selected = 'Manufacturing'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Manufacturing</li>
                                            <li @click="selected = 'AME'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">AME</li>
                                            <li @click="selected = 'Purchasing'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Purchasing</li>
                                            <li @click="selected = 'PPIC'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">PPIC</li>
                                            <li @click="selected = 'Quality'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Quality</li>
                                            <li @click="selected = 'Maintenance'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Maintenance</li>
                                            <li @click="selected = 'Product Mechanical Engineering'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Product Mechanical Engineering</li>
                                            <li @click="selected = 'Process Engineering'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Process Engineering</li>
                                            <li @click="selected = 'OPEX dan PDCA'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">OPEX dan PDCA</li>
                                            <li @click="selected = 'Accounting'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Accounting</li>
                                            <li @click="selected = 'HR'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">HR</li>
                                            <li @click="selected = 'GA EHS'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">GA EHS</li>
                                        </ul>
                                    
                                        <!-- Input Hidden untuk Submit Form -->
                                        <input type="hidden" name="departemen" x-model="selected">
                                    </div>
                                    

                                    {{-- Nama PIC --}}
                                    <div x-data="{ open: false, selected: '' }" class="relative mb-4">
                                        <label for="nama_pic" class="block text-sm font-medium text-gray-700 mb-1">
                                            Nama PIC
                                        </label>
                                        
                                        <!-- Tombol Dropdown -->
                                        <div @click="open = !open" class="flex justify-between w-full p-3 border border-gray-800 rounded-md cursor-pointer bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                                            <span x-text="selected || 'Pilih PIC'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M6 9l6 6 6-6"></path>
                                            </svg>
                                        </div>
                                    
                                        <!-- Dropdown List -->
                                        <ul 
                                            x-show="open" 
                                            x-transition:enter="transition ease-out duration-200 transform"
                                            x-transition:enter-start="opacity-0 -translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-out duration-200"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                            class="absolute z-10 w-full mt-1 bg-white border rounded-md shadow-lg max-h-48 overflow-auto"
                                            x-cloak
                                        >
                                            <li @click="selected = 'PIC 1'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">PIC 1</li>
                                            <li @click="selected = 'PIC 2'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">PIC 2</li>
                                            <li @click="selected = 'PIC 3'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">PIC 3</li>
                                        </ul>
                                    
                                        <!-- Input Hidden untuk Submit Form -->
                                        <input type="hidden" name="nama_pic" x-model="selected">
                                    </div>
                                    
                                    
                                    {{-- Temuan Ketidaksesuaian --}}
                                    <div class="mb-4">
                                        <label for="temuan_ketidaksesuaian" class="block text-sm font-medium text-gray-700 mb-1">Temuan Ketidaksesuaian</label>
                                        <input type="text" class="flex justify-between w-full p-3 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="temuan_ketidaksesuaian" name="temuan_ketidaksesuaian" required>
                                    </div>

                                    {{-- Tingkat Bahaya --}}
                                    <div x-data="{ open: false, selected: '' }" class="relative mb-4">
                                        <label for="tingkat_bahaya" class="block text-sm font-medium text-gray-700 mb-1">
                                            Tingkat Bahaya
                                        </label>
                                        
                                        <!-- Tombol Dropdown -->
                                        <div @click="open = !open" class="flex justify-between w-full p-3 border border-gray-800 rounded-md cursor-pointer bg-white focus:outline-none focus:ring-2 focus:ring-primary">
                                            <span x-text="selected || 'Pilih Tingkat Bahaya'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M6 9l6 6 6-6"></path>
                                            </svg>
                                        </div>
                                    
                                        <!-- Dropdown List -->
                                        <ul 
                                            x-show="open" 
                                            x-transition:enter="transition ease-out duration-200 transform"
                                            x-transition:enter-start="opacity-0 -translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-out duration-200"
                                            x-transition:leave-start="opacity-100"
                                            x-transition:leave-end="opacity-0"
                                            class="absolute z-10 w-full mt-1 bg-white border rounded-md shadow-lg max-h-48 overflow-auto"
                                            x-cloak
                                        >
                                            <li @click="selected = 'Low'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Low</li>
                                            <li @click="selected = 'Medium'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Medium</li>
                                            <li @click="selected = 'High'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">High</li>
                                        </ul>
                                    
                                        <!-- Input Hidden untuk Submit Form -->
                                        <input type="hidden" name="tingkat_bahaya" x-model="selected">
                                    </div>
                                    
                                     <!-- Rekomendasi -->
                                     <div class="mb-4">
                                        <label for="rekomendasi" class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi</label>
                                        <textarea class="flex justify-between w-full p-3 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="rekomendasi" name="rekomendasi" rows="4" required></textarea>
                                    </div>

                                    <!-- Batas Waktu Perbaikan -->
                                    <div class="mb-4">
                                        <label for="batas_waktu" class="block text-sm font-medium text-gray-700 mb-1">Batas Waktu Perbaikan</label>
                                        <input type="date" class="flex justify-between w-full p-3 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="batas_waktu" name="batas_waktu" required>
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
