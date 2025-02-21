<x-app-layout>

    <div x-data="{activeTab: 'laporan'}" class="px-5 pt-2 pb-8">
        <!-- Tabs -->
        <div class="flex space-x-4 border-b">
            <button @click="activeTab = 'laporan'" :class="activeTab === 'laporan' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                class="px-4 py-2 focus:outline-none">
                Laporan LCT
            </button>
            <button @click="activeTab = 'task-and-timeline'" :class="activeTab === 'task-and-timeline' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                class="px-4 py-2 focus:outline-none">
                Task & Timeline
            </button>
        </div>

        <!-- Tab Content -->
        <div class="mt-4">
            <!-- Laporan -->
            <div x-show="activeTab === 'laporan'">
                <div class="max-h-screen flex justify-center items-center">
                    <div class="grid md:grid-cols-2 justify-center w-full">
                        <!-- Card Laporan dari EHS -->
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
                                        📝 Laporan dari EHS
                                    </h5>
                                    
                                    <!-- Garis Pemisah -->
                                    <div class="w-full h-[2px] bg-gray-200 my-3"></div>
                
                                    <!-- Isi Laporan -->
                                    <div class="flex flex-col space-y-1 mt-4">
                                        <p class="text-gray-500 text-xs">Temuan Ketidaksesuaian</p>
                                        <p class="text-gray-900 font-semibold text-lg">Kerusakan Tempat Sampah</p>
                                    </div>
                                </div>
                
                
                                <!-- Card Informasi dari EHS -->
                                <div class="bg-white py-5 px-3 rounded-xl shadow-md mt-3 flex flex-row justify-around items-center">
                                    
                                    <!-- Nama PIC -->
                                    <div class="flex flex-col items-start">
                                        <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                                            <i class="fas fa-user text-blue-500"></i> <!-- Ikon User -->
                                            <p>Nama PIC</p>
                                        </div>
                                        <p class="text-gray-900 font-semibold text-sm mt-1">Asep</p>
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
                                            <p>Detail Area Temuan</p>
                                        </div>
                                        <p class="text-gray-900 font-semibold text-sm mt-1">FA Line 2 - CLuster Assy</p>
                                    </div>
                
                                </div>
                
                                <div x-data="{ dueDate: '2024-01-22', today: new Date().toISOString().split('T')[0] }"
                                    :class="new Date(dueDate) < new Date(today) ? 'border-l-4 border-red-500' : 'border-l-4 border-green-500'"
                                    class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3 flex flex-col items-start">
                
                                    <div class="flex items-center gap-2 text-gray-500 text-xs tracking-wide">
                                        <i class="fas fa-calendar-alt text-lg"
                                            :class="new Date(dueDate) < new Date(today) ? 'text-red-500' : 'text-green-500'"></i>
                                        <p class="font-medium">Due Date</p>
                                    </div>
                
                                    <p class="text-sm font-semibold mt-1"
                                        :class="new Date(dueDate) < new Date(today) ? 'text-red-600' : 'text-gray-900'">
                                        <span x-text="dueDate"></span>
                                    </p>
                                </div>
                
                                
                                <!-- Card Kategori Temuan -->
                                <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-flag text-yellow-500 text-lg"></i>
                                        <p class="text-gray-500 text-xs">Kategori Temuan</p>
                                    </div>
                                    <p class="text-gray-900 font-semibold mt-2 bg-yellow-100 p-2 rounded-lg hover:bg-yellow-200 transition-all duration-200 ease-in-out">Kondisi Tidak Aman</p>
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
                                        'bg-green-100 text-green-900 hover:bg-green-200': level === 'low',
                                        'bg-yellow-100 text-yellow-900 hover:bg-yellow-200': level === 'medium',
                                        'bg-red-100 text-red-900 hover:bg-red-200': level === 'high'
                                    }" class="text-gray-900 font-semibold mt-2 p-2 rounded-lg transition-all duration-200 ease-in-out">
                                        <span x-text="level === 'low' ? 'Rendah' : level === 'medium' ? 'Sedang' : 'Tinggi'"></span>
                                    </p>
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
                                        <h5 class="text-xl font-bold">Formulir Pengajuan Laporan Perbaikan ke EHS</h5>
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
                                                
                                                {{-- Nama PIC --}}
                                                <div class="mb-4">
                                                    <label for="nama_pic" class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
                                                    <input type="text" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="temuan_ketidaksesuaian" name="temuan_ketidaksesuaian" required>
                                                </div>
                
                                                {{-- Batas Waktu Perbaikan --}}
                                                <div class="mb-4">
                                                    <label for="batas_waktu" class="block text-sm font-medium text-gray-700 mb-1">Batas Waktu Perbaikan</label>
                                                    <input type="date" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="batas_waktu" name="batas_waktu" required>
                                                </div>
                
                                                {{-- Nama PIC --}}
                                                <div class="mb-4">
                                                    <label for="status_" class="block text-sm font-medium text-gray-700 mb-1">Status LCT</label>
                                                    <input type="text" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="temuan_ketidaksesuaian" name="temuan_ketidaksesuaian" required>
                                                </div>
                
                                                {{-- Date of Completion --}}
                                                <div class="mb-4">
                                                    <label for="batas_waktu" class="block text-sm font-medium text-gray-700 mb-1">Date of Completion</label>
                                                    <input type="date" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="batas_waktu" name="batas_waktu" required>
                                                </div>
                                                
                                                <!-- Unggah Foto Closed-->
                                                <div class="mb-4">
                                                    <label for="foto_temuan" class="block text-sm font-medium text-gray-700">
                                                        Unggah Bukti Foto Closed
                                                    </label>
                                                    <div class="flex items-center justify-center w-full sm:w-2/3 mt-2">
                                                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                                </svg>
                                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 1MB)</p>
                                                            </div>
                                                            <input id="dropzone-file" type="file" class="hidden" />
                                                        </label>
                                                    </div> 
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
            </div>

            <div x-show="activeTab === 'task-and-timeline'">
                <div class="max-w-full bg-[#F3F4F6]">
                    ini buat task 
                </div>
            </div>

        </div>
    </div>

</x-app-layout>



