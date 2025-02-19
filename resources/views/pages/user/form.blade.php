@extends('layouts.user-layout')

@section('content')
<div class="container mx-auto px-4 sm:px-6 md:px-8 mt-3 mb-6">
    <div class="flex justify-center">
        <div class="w-full max-w-md md:max-w-lg lg:max-w-2xl">

            <!-- Card Header: Logo dan Deskripsi -->
            <div class="bg-white shadow-lg rounded-lg text-center mb-6 p-4 sm:p-6">
                <!-- Logo Perusahaan -->
                <div class="flex justify-center mt-4 mb-4">
                    <img src="{{ asset('images/LOGO-AVI-OFFICIAL.png') }}" alt="Logo Perusahaan" class="w-2/4 max-w-xs">
                </div>

                <!-- Deskripsi -->
                <div>
                    <h4 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-3">Laporkan Temuan Ketidaksesuaian Anda</h4>
                    <p class="text-gray-600 text-sm sm:text-base">
                        Temuan ketidaksesuaian atau LCT (Laporan Ketidaksesuaian Temuan) sangat penting untuk memastikan bahwa segala aspek operasional tetap berjalan sesuai dengan standar keselamatan dan prosedur yang telah ditetapkan.
                    </p>
                </div>
            </div>

            <!-- Card Form -->
            <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Nama -->
                    <div class="mb-4">
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" name="nama" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- NPK -->
                    <div class="mb-4">
                        <label for="no_npk" class="block text-sm font-medium text-gray-700">NPK <span class="text-red-500">*</span></label>
                        <input 
                            type="number" 
                            id="no_npk" 
                            name="no_npk" 
                            class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" 
                            required 
                            min="0" 
                            oninput="this.value = Math.max(this.value, 0)"  
                            style="-moz-appearance: textfield; -webkit-appearance: none;" 
                        >
                    </div>
                    

                    <!-- Tanggal Temuan -->
                    <div class="mb-4">
                        <label for="tanggal_temuan" class="block text-sm font-medium text-gray-700">Tanggal Temuan <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_temuan" name="tanggal_temuan" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required>
                        <p class="text-xs text-gray-500 mt-1">Masukkan tanggal LCT ditemukan</p> <!-- Deskripsi kecil -->
                    </div>


                    <!-- Area -->
                    <div class="mb-4" x-data="{ open: false, selected: '' }">
                        <label for="area" class="block text-sm font-medium text-gray-700">Area <span class="text-red-500">*</span></label>
                        
                        <!-- Dropdown Input with Icon and Text -->
                        <div class="relative mt-2">
                            <div class="flex justify-between items-center px-4 py-2 border border-black rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 cursor-pointer" @click="open = !open">
                                <span x-text="selected || 'Pilih Area'"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M6 9l6 6 6-6"></path>
                                </svg>
                            </div>
                            
                            <!-- Dropdown list -->
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
                                <li @click="selected = 'Area 1'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Area 1</li>
                                <li @click="selected = 'Area 2'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Area 2</li>
                                <li @click="selected = 'Area 3'; open = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">Area 3</li>
                            </ul>
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-1">Pilih area tempat temuan LCT ditemukan.</p> <!-- Deskripsi -->
                    </div>

                    <!-- Detail Area -->
                    <div class="mb-4 flex flex-col relative" x-data="{ open: false }">
                        <label for="detail-area" class="block text-sm font-medium text-gray-700">
                            Detail Area <span class="text-red-500">*</span>
                            <button type="button" @click.prevent="open = !open" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                <img src="{{ asset('images/question-mark-circle-svgrepo-com.svg') }}" alt="question-mark" class="w-4 h-4">
                            </button>
                        </label>

                        <input type="text" id="detail-area" name="detail-area" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required>
                        <p class="text-xs text-gray-500 mt-1">Masukkan detail lokasi atau area tempat temuan LCT. Untuk contoh: klik ikon tanda tanya.</p>    

                        <!-- Dropdown untuk contoh -->
                        <div
                            class="origin-top-right z-10 absolute left-28 min-w-44 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
                            x-show="open"
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                            x-transition:enter="transition ease-out duration-200 transform"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-out duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            x-cloak
                        >
                            <div class="p-2">
                                <p class="font-semibold mt-2">Contoh Pengisian:</p>
                                <p class="text-sm text-gray-600">Gedung A ruang B</p>
                            </div>
                            
                        </div>
                    </div>

                    <!-- Unggah Foto -->
                    <div class="mb-4">
                        <label for="foto_temuan" class="block text-sm font-medium text-gray-700">
                            Unggah Foto <span class="text-red-500">*</span>
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

                        <!-- Deskripsi kecil -->
                        <p class="text-xs text-gray-500 mt-1">Unggah foto yang berkaitan dengan temuan LCT. Pastikan file gambar tidak lebih dari 1MB dan dalam format PNG, JPG, atau GIF.</p>
                    </div>

                    <!-- Kategori -->
                    <div class="mb-4 flex flex-col">
                            <label for="kategori" class="block text-sm font-medium text-gray-700">
                                Kategori Temuan <span class="text-red-500">*</span>
                            </label>
                                <div class="relative inline-flex" x-data="{ open: false, selected: '' }">
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
                                            <li>
                                                <button 
                                                    type="button" 
                                                    class="block w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100 focus:outline-none"
                                                    @click="selected = 'Kondisi Tidak Aman (Unsafe Condition)'; open = false"
                                                >
                                                    Kondisi Tidak Aman (Unsafe Condition)
                                                </button>
                                            </li>
                                            <li>
                                                <button 
                                                    type="button" 
                                                    class="block w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100 focus:outline-none"
                                                    @click="selected = 'Tindakan Tidak Aman (Unsafe Act)'; open = false"
                                                >
                                                    Tindakan Tidak Aman (Unsafe Act)
                                                </button>
                                            </li>
                                            <li>
                                                <button 
                                                    type="button" 
                                                    class="block w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100 focus:outline-none"
                                                    @click="selected = '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)'; open = false"
                                                >
                                                    5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)
                                                </button>
                                            </li>
                                            <li>
                                                <button 
                                                    type="button" 
                                                    class="block w-full px-4 py-2 text-left text-gray-700 hover:bg-gray-100 focus:outline-none"
                                                    @click="selected = 'Near miss'; open = false"
                                                >
                                                    Near miss
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Pilih kategori yang sesuai dengan temuan LCT Anda. Misalnya, apakah ini berkaitan dengan kondisi atau tindakan yang tidak aman, atau masalah lainnya.</p>
                    </div>

                      <!-- Temuan Ketidaksesuaian -->
                      <div class="mb-4">
                        <label for="temuan" class="block text-sm font-medium text-gray-700">
                            Temuan Ketidaksesuaian <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            id="temuan" 
                            name="temuan" 
                            class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" 
                            rows="4" 
                            required
                        ></textarea>
                        
                        <!-- Deskripsi kecil -->
                        <p class="text-xs text-gray-500 mt-1">Deskripsikan temuan ketidaksesuaian yang ditemukan di area LCT. Jelaskan secara rinci agar dapat segera ditindaklanjuti.</p>
                    </div>

                    <!-- Rekomendasi Safety -->
                    <div class="mb-4">
                        <label for="rekomendasi" class="block text-sm font-medium text-gray-700">Rekomendasi Safety <span class="text-red-500">*</span></label>
                        <textarea id="rekomendasi" name="rekomendasi" rows="4" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required></textarea>
                        <p class="text-xs text-gray-500">Masukkan rekomendasi untuk memperbaiki kondisi atau tindakan yang tidak aman. Berikan saran yang dapat membantu meningkatkan keselamatan di area tersebut.</p>
                    </div>

                    <!-- Tombol Kirim -->
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md focus:ring-2 focus:ring-blue-500 mt-2">Kirim Laporan</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
