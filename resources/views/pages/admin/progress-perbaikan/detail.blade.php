<x-app-layout>
    {{ Breadcrumbs::render('progress-perbaikan.detail') }}

    <div x-data="{ activeTab: 'user' }" class="px-5 pt-2 pb-8">
        <!-- Tabs -->
        <div class="flex space-x-4 border-b">
            <button @click="activeTab = 'user'" :class="activeTab === 'user' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                class="px-4 py-2 focus:outline-none">
                User
            </button>
            <button @click="activeTab = 'pic'" :class="activeTab === 'pic' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                class="px-4 py-2 focus:outline-none">
                PIC
            </button>
        </div>

        <!-- Tab Content -->
        <div class="mt-4">
            {{-- Laporan dari User --}}
            <div x-show="activeTab === 'user'">
                <h2 class="text-lg font-semibold">Laporan dari User</h2>
                <div class="my-3 max-h-min rounded-lg">

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
                                class="w-20 h-20 rounded-md shadow-md object-cover" 
                                alt="Gambar Temuan">
                        </div>
                    </div>

                </div>
            </div>


            {{-- Laporan dari PIC --}}
            <div x-show="activeTab === 'pic'">
                <h2 class="text-lg font-semibold">Laporan dari PIC</h2>
                <div class="grid md:grid-cols-2 justify-center w-full">
                    <!-- Card Laporan dari EHS -->
                    <div class="w-full bg-[#F3F4F6] overflow-hidden h-[487px] overflow-y-auto 
                                [&::-webkit-scrollbar]:w-1
                                [&::-webkit-scrollbar-track]:rounded-full
                                [&::-webkit-scrollbar-track]:bg-gray-100
                                [&::-webkit-scrollbar-thumb]:rounded-full
                                [&::-webkit-scrollbar-thumb]:bg-gray-300
                                dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                                dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                        
                        <div class="my-3 px-2 max-h-min rounded-lg">

                            <!-- Card Laporan -->
                            <div class="bg-white p-5 rounded-xl shadow-md border ">
                                <h5 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                    📝 Laporan dari PIC
                                </h5>
                                <div class="w-full h-[2px] bg-gray-200 my-3"></div>

                                <div class="flex flex-col space-y-1 mt-4">
                                    <p class="text-gray-500 text-xs">Temuan Ketidaksesuaian</p>
                                    <p class="text-gray-900 font-semibold text-lg">Kerusakan Tempat Sampah</p>
                                </div>
                            </div>

                            <!-- Card Informasi PIC -->
                            <div class="bg-white p-5 rounded-xl shadow-md mt-3 flex flex-row justify-around items-center">
                                <div class="flex flex-col items-start">
                                    <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                                        <i class="fas fa-user text-blue-500"></i>
                                        <p>Nama PIC</p>
                                    </div>
                                    <p class="text-gray-900 font-semibold text-sm mt-1">Aziz</p>
                                </div>
                                <div class="w-[2px] bg-gray-300 h-10 rounded-full"></div>
                                <div class="flex flex-col items-start">
                                    <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                                        <i class="fas fa-calendar-alt text-green-500"></i>
                                        <p>Batas Waktu Perbaikan</p>
                                    </div>
                                    <p class="text-gray-900 font-semibold text-sm mt-1">22-01-2024</p>
                                </div>
                                <div class="w-[2px] bg-gray-300 h-10 rounded-full"></div>
                                <div class="flex flex-col items-start">
                                    <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                                        <i class="fas fa-map-marker-alt text-red-500"></i>
                                        <p>Detail Area Temuan</p>
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
                                <p class="text-gray-900 font-semibold mt-2 bg-yellow-100 p-2 rounded-lg">Kondisi Tidak Aman</p>
                            </div>

                            <!-- Card Tingkat Bahaya -->
                            <div x-data="{ level: 'high' }" class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
                                <div class="flex items-center space-x-2">
                                    <i :class="{
                                        'text-green-500 fa-check-circle': level === 'low',
                                        'text-yellow-500 fa-exclamation-triangle': level === 'medium',
                                        'text-red-500 fa-skull-crossbones': level === 'high'
                                    }" class="fa-solid text-lg"></i>
                                    <p class="text-gray-500 text-xs">Tingkat Bahaya</p>
                                </div>
                                <p :class="{
                                    'bg-green-100 text-green-900': level === 'low',
                                    'bg-yellow-100 text-yellow-900': level === 'medium',
                                    'bg-red-100 text-red-900': level === 'high'
                                }" class="text-gray-900 font-semibold mt-2 p-2 rounded-lg">
                                    <span x-text="level === 'low' ? 'Rendah' : level === 'medium' ? 'Sedang' : 'Tinggi'"></span>
                                </p>
                            </div>

                            <!-- Card Gambar Temuan -->
                            <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
                                <p class="text-gray-700 text-lg font-semibold">Gambar Bukti Perbaikan</p>
                                <div class="relative mt-2">
                                    <img src="{{ asset('images/user-36-05.jpg') }}" 
                                        class="w-20 h-20 rounded-md shadow-md object-cover" 
                                        alt="Gambar bukti perbaikan">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Approval -->
                    <div x-data="{ approve() { alert('✅ Laporan telah disetujui!'); }, reject() { alert('❌ Laporan telah ditolak!'); } }" 
                        class="bg-white shadow-lg rounded-lg p-4 mx-2 w-full my-3 max-h-max">

                        <!-- Header -->
                        <div class="text-center">
                            <h5 class="text-2xl font-semibold text-gray-800">Form Persetujuan</h5>
                        </div>

                        <!-- Garis Pembatas -->
                        <div class="w-full h-[2px] bg-gray-200 my-4"></div>

                        <!-- Input Catatan -->
                        <div class="mb-4">
                            <label class="text-gray-700 font-medium block mb-2">Catatan</label>
                            <textarea placeholder="Masukkan catatan..." 
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>

                        <!-- Tombol Approval -->
                        <div class="flex flex-row justify-center space-x-4">
                            <button @click="approve()" 
                                class="bg-green-600 text-white font-bold px-6 py-3 rounded-lg shadow-md hover:bg-green-700 transition-all">
                                ✅ Setujui Laporan
                            </button>

                            <button @click="reject()" 
                                class="bg-red-500 text-white font-bold px-6 py-3 rounded-lg shadow-md border border-red-700 hover:bg-red-600 transition-all">
                                ❌ Tolak Laporan
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
