<x-app-layout class="h-screen overflow-hidden">
    <div class="h-full">
        <div class="grid md:grid-cols-2 justify-center w-full h-full">
            <!-- Card Laporan dari Pelapor -->
            <div class="relative max-w-full bg-[#F3F4F6] overflow-hidden shadow-md p-3 h-full pb-20 max-h-[calc(100vh)] overflow-y-auto [&::-webkit-scrollbar]:w-1
                [&::-webkit-scrollbar-track]:rounded-full
                [&::-webkit-scrollbar-track]:bg-gray-100
                [&::-webkit-scrollbar-thumb]:rounded-full
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">

                
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
                        <div class="relative mt-2">
                            <img src="{{ asset('images/user-36-05.jpg') }}" 
                                class="w-full h-auto rounded-md shadow-md object-cover" 
                                alt="Gambar Temuan">
                        </div>
                    </div>

                </div>
            </div>
            
        
            <!-- Form Laporan Temuan -->
            <div class="relative max-w-full bg-[#F3F4F6] overflow-hidden shadow-md p-3 pb-20 max-h-[calc(100vh)] overflow-y-auto [&::-webkit-scrollbar]:w-1
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
                            <form action="{{ route('admin.laporan-lct.assignToPic', ['id_laporan_lct' => $laporan->id_laporan_lct]); }}" method="POST">
                                @csrf
                                <div class="space-y-6">
                                    <!-- Form fields -->

                                    {{-- Area Temuan --}}
                                    <div class="mb-4">
                                        <label for="area_temuan" class="block text-sm font-medium text-gray-700 mb-1">Area Temuan</label>
                                        <input type="text" class="flex justify-between w-full p-3 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="area_temuan" name="area_temuan" value="{{$laporan->area}} - {{$laporan->detail_area}}" readonly required>
                                    </div>

                                    <!-- Kategori Temuan -->
                                    <div class="mb-4 flex flex-col">
                                        <label for="kategori" class="block text-sm font-medium text-gray-700">
                                            Kategori Temuan
                                        </label>
                                        <div class="relative inline-flex" x-data="{ open: false, selected: @js($laporan->kategori_temuan ?? '') }">
                                            <button 
                                                type="button" 
                                                class="mt-2 w-full px-4 py-2 border border-black rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 text-left bg-white"
                                                @click.prevent="open = !open" 
                                                :aria-expanded="open" 
                                                aria-haspopup="true"
                                            >
                                                <div class="flex justify-between items-center">
                                                    <span x-text="selected || 'Pilih Kategori'"></span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M6 9l6 6 6-6"></path>
                                                    </svg>
                                                </div>
                                            </button>
                                    
                                            <!-- Hidden Input -->
                                            <input type="hidden" name="kategori_temuan" x-model="selected" required>
                                    
                                            <!-- Dropdown Menu -->
                                            <div 
                                                class="origin-top-right z-10 absolute top-full left-0 min-w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-2 overflow-hidden"
                                                x-show="open"
                                                x-transition:enter="transition ease-out duration-200 transform"
                                                x-transition:enter-start="opacity-0 -translate-y-2"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                x-transition:leave="transition ease-out duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                x-cloak
                                            >
                                                <ul class="text-sm">
                                                    @foreach(['Kondisi Tidak Aman (Unsafe Condition)', 'Tindakan Tidak Aman (Unsafe Act)', '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)', 'Near miss'] as $kategori)
                                                    <li>
                                                        <button 
                                                            type="button" 
                                                            class="block w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100 focus:outline-none"
                                                            @click="selected = '{{ $kategori }}'; open = false"
                                                        >
                                                            {{ $kategori }}
                                                        </button>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    

                                    {{-- Tanggal Temuan --}}
                                    <div class="mb-4">
                                        <label for="tanggal_temuan" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Temuan</label>
                                        <input type="text" 
                                            value="{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->translatedFormat('d F Y') }}" 
                                            id="tanggal_temuan" 
                                            name="tanggal_temuan" 
                                            class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" 
                                            readonly>
                                    </div>     
                                
                                        <!-- Dropdown Departemen -->
                                        <div x-data="departemenPic" x-init='initData(@json($departemen), @json($picDepartemen))'>
                                            <!-- Dropdown Departemen -->
                                        <div class="relative mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                                            <button 
                                                type="button"
                                                @click="open = !open" 
                                                class="flex justify-between items-center w-full px-4 py-2 border border-gray-800 rounded-md bg-white shadow-sm focus:ring-2 focus:ring-blue-500 text-left"
                                            >
                                                <span x-text="selectedDept ? departemen.find(d => d.id == selectedDept).nama_departemen : 'Pilih Departemen'"></span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    :class="{'rotate-180': open}">
                                                    <path d="M6 9l6 6 6-6"></path>
                                                </svg>
                                            </button>
                                            
                                            <ul 
                                                x-show="open" 
                                                @click.away="open = false"
                                                class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-auto"
                                            >
                                                <template x-for="dept in departemen" :key="dept.id">
                                                    <li @click="selectedDept = dept.id; open = false; updatePIC();" 
                                                        class="px-4 py-2 cursor-pointer hover:bg-blue-100">
                                                        <span x-text="dept.nama_departemen"></span>
                                                    </li>
                                                </template>
                                            </ul>
                                            
                                            <input type="hidden" name="departemen_id" x-model="selectedDept" required>
                                        </div>
                                        
                                        <!-- Dropdown PIC -->
                                        <div class="relative mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
                                            <button 
                                                type="button"
                                                @click="openPic = !openPic" 
                                                class="flex justify-between items-center w-full px-4 py-2 border border-gray-800 rounded-md bg-white shadow-sm focus:ring-2 focus:ring-blue-500 text-left"
                                                :disabled="filteredPics.length === 0"
                                            >
                                                <span x-text="selectedPic ? filteredPics.find(pic => pic.id == selectedPic).user.fullname : 'Pilih PIC'"></span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    :class="{'rotate-180': openPic}">
                                                    <path d="M6 9l6 6 6-6"></path>
                                                </svg>
                                            </button>
                                            
                                            <ul 
                                                x-show="openPic" 
                                                @click.away="openPic = false"
                                                class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-md shadow-lg max-h-48 overflow-auto"
                                            >
                                                <template x-for="pic in filteredPics" :key="pic.id">
                                                    <li @click="selectedPic = pic.id; openPic = false" 
                                                        class="px-4 py-2 cursor-pointer hover:bg-blue-100">
                                                        <span x-text="pic.user.fullname"></span>
                                                    </li>
                                                </template>
                                            </ul>
                                            
                                            <input type="hidden" name="pic_id" x-model="selectedPic" required>
                                        </div>
                                        
                                    
                                    {{-- Temuan Ketidaksesuaian --}}
                                    <div class="mb-4">
                                        <label for="temuan_ketidaksesuaian" class="block text-sm font-medium text-gray-700 mb-1">Temuan Ketidaksesuaian</label>
                                        <input type="text" value="{{$laporan->temuan_ketidaksesuaian}}" class="flex justify-between w-full p-3 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="temuan_ketidaksesuaian" name="temuan_ketidaksesuaian" readonly>
                                    </div>

                                    {{-- Tingkat Bahaya --}}
                                    <div x-data="{ open: false, selected: '' }" class="relative mb-4">
                                        <label for="tingkat_bahaya" class="block text-sm font-medium text-gray-700 mb-1">
                                            Tingkat Bahaya
                                        </label>
                                        
                                        <!-- Tombol Dropdown -->
                                        <button 
                                            type="button" 
                                            class="mt-2 w-full px-4 py-2 border border-black rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 text-left bg-white"
                                            @click.prevent="open = !open" 
                                            :aria-expanded="open" 
                                            aria-haspopup="true"
                                        >
                                        <div class="flex justify-between items-center">

                                            <span x-text="selected || 'Pilih Tingkat Bahaya'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M6 9l6 6 6-6"></path>
                                            </svg>
                                        </div>
                                        </button>
                                    
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
                                        <input type="hidden" name="tingkat_bahaya" x-model="selected" required>
                                    </div>
                                    
                                     <!-- Rekomendasi -->
                                     <div class="mb-4">
                                        <label for="rekomendasi" class="block text-sm font-medium text-gray-700 mb-1">Rekomendasi</label>
                                        <textarea class="flex justify-between w-full p-3 border border-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" id="rekomendasi" name="rekomendasi" rows="4" required></textarea>
                                    </div>

                                    <!-- Batas Waktu Perbaikan -->
                                    <div class="mb-4">
                                        <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Batas Waktu Perbaikan</label>
                                        <input type="date" id="due_date" name="due_date" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required>
                                    </div>

                                    <!-- Submit button -->
                                    <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 mt-4 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 cursor-pointer">
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

    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("departemenPic", () => ({
                departemen: [],       // Daftar departemen
                filteredPics: [],     // PIC sesuai departemen
                selectedDept: "",     // Departemen yang dipilih
                selectedPic: "",      // PIC yang dipilih
                open: false,          // Dropdown Departemen
                openPic: false,       // Dropdown PIC
        
                initData(departemenData, picDepartemenData) {
                    this.departemen = departemenData;
                    this.picDepartemen = picDepartemenData;
                },
        
                updatePIC() {
                    if (!this.selectedDept) return;
        
                    this.filteredPics = this.picDepartemen
                        .filter(item => item.departemen_id == this.selectedDept)
                        .map(item => item.pic);
                }
            }));
        });
        </script>
        
        {{-- @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            </script>
        @endif --}}
</x-app-layout>
