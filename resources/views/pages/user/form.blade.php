@extends('layouts.user-layout')

@php
    $user = Auth::user(); // Mengambil user yang sedang login
@endphp

@section('content')
<div class="max-w-6xl mx-auto pt-5 pb-4 sm:pb-6 px-4 sm:px-0 mb-6">
    <div class="flex justify-center">
        <div class="w-full">

            <!-- Card Header: Logo dan Deskripsi -->
            <div class="bg-white shadow-lg rounded-lg text-center mb-6 p-4 sm:p-6 w-full">
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
            <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6 w-full">
                <form action="{{ route('laporan-lct.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 gap-6">
                        <!-- NPK -->
                        <div>
                            <label for="no_npk" class="block text-sm font-medium text-gray-700">
                                NPK <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="no_npk" 
                                name="no_npk"
                                value="{{ $user->npk }}"
                                class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm bg-white text-gray-500 cursor-not-allowed"
                                required 
                                readonly
                            >
                        </div>
                    
                        <!-- Nama -->
                        <div>
                            <label for="nama" class="block text-sm font-medium text-gray-700">
                                Nama <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="nama" 
                                name="nama" 
                                value="{{ $user->fullname }}" 
                                class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm bg-white text-gray-500 cursor-not-allowed"
                                required 
                                readonly
                            >
                        </div>
                    </div>
                    
                    
                    <div class="grid grid-cols-1 gap-6 mt-4">
                        <!-- Tanggal Temuan -->
                        <div>
                            <label for="tanggal_temuan" class="block text-sm font-medium text-gray-700">
                                Tanggal Temuan <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="tanggal_temuan" 
                                name="tanggal_temuan" 
                                class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" 
                                required
                            >
                            <p class="text-xs text-gray-500 mt-1">Masukkan tanggal LCT ditemukan. Input maksimal 3 hari setelah temuan</p> <!-- Deskripsi kecil -->
                        </div>

                        <!-- Area -->
                        <div x-data="{ open: false, selected: '', error: false }">
                            <label for="area" class="block text-sm font-medium text-gray-700">
                                Area <span class="text-red-500">*</span>
                            </label>

                            <!-- Dropdown Input with Icon and Text -->
                            <div class="relative mt-2">
                                <div class="flex justify-between items-center px-4 py-2 border border-black rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                    @click="open = !open" :class="{ 'border-red-500': error }">
                                    <span x-text="selected || 'Pilih Area'" class="text-gray-700"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9l6 6 6-6"></path>
                                    </svg>
                                </div>

                                <!-- Dropdown list -->
                                <ul x-show="open" x-transition:enter="transition ease-out duration-200 transform"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-out duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="absolute z-10 w-full mt-1 bg-white border rounded-md shadow-lg max-h-48 overflow-auto"
                                    x-cloak>
                                    <template x-for="area in ['Office Lantai 1', 'Office Lantai 2', 'FA', 'SMT', 'Changing Room', 'Gudang B3', 'Gudang GA', 'Lab AVI', 'Ruang E-Comp', 'Ruang Panel', 'Ruang Server', 'Ruang Sparepart', 'Ruang Kompressor']">
                                        <li @click="selected = area; open = false; error = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">
                                            <span x-text="area"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            <!-- Input Tersembunyi untuk Validasi -->
                            <input type="text" name="area" x-model="selected" required class="absolute opacity-0 w-0 h-0">

                            <p class="text-xs text-gray-500 mt-1">Pilih area tempat temuan LCT ditemukan.</p> <!-- Deskripsi -->

                            <!-- Pesan error manual -->
                            <p x-show="error" class="text-red-500 text-xs mt-1">Silakan pilih area.</p>

                            <!-- Validasi saat submit -->
                            <script>
                                document.querySelector("form").addEventListener("submit", function (e) {
                                    let areaDropdown = document.querySelector("[x-data]");
                                    let selectedValue = areaDropdown.__x.$data.selected;

                                    if (!selectedValue) {
                                        e.preventDefault();
                                        areaDropdown.__x.$data.error = true;
                                    }
                                });
                            </script>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 gap-6 mt-4">
                        <!-- Detail Area -->
                        <div class="order-1 flex flex-col relative" x-data="{ open: false }">
                            <label for="detail_area" class="block text-sm font-medium text-gray-700">
                                Detail Area <span class="text-red-500">*</span>
                                <button type="button" @click.prevent="open = !open" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                    <img src="{{ asset('images/question-mark-circle-svgrepo-com.svg') }}" alt="question-mark" class="w-4 h-4">
                                </button>
                            </label>

                            <!-- Input utama -->
                            <input type="text" id="detail_area" name="detail_area" 
                                class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" 
                                required>

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
                                    <p class="text-sm text-gray-600">Mis: FA Line 2 - Cluster Assy</p>
                                </div>
                            </div>
                        </div>


                        <!-- Unggah Foto -->
                        <div class="order-2">
                            <label for="bukti_temuan" class="block text-sm font-medium text-gray-700">
                                Unggah Foto <span class="text-red-500">*</span>
                            </label>
                            <div class="flex flex-col items-center justify-center w-full mt-2">
                                <!-- Opsi memilih gambar dari galeri -->
                                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                        <p class="text-xs text-gray-500">SVG, PNG, JPG or GIF (MAX. 1MB)</p>
                                    </div>
                                    <input id="dropzone-file" name="bukti_temuan[]" type="file" class="hidden" accept="image/*" multiple />
                                </label>

                                <!-- Opsi akses kamera -->
                                <button type="button" id="open-camera" class="mt-4 w-full h-12 bg-blue-500 text-white rounded-lg">Ambil Foto</button>
                            </div>

                            <!-- Video untuk menampilkan kamera -->
                            <video id="video" autoplay playsinline class="mt-2 w-48 h-36 border" style="display: none;"></video>

                            <!-- Tombol ambil foto -->
                            <button type="button" id="capture-photo" class="mt-2 w-full h-12 bg-green-500 text-white rounded-lg" style="display: none;">Capture</button>

                            <!-- Menampilkan foto yang diambil atau dipilih -->
                            <div id="preview-container" class="mt-4 flex flex-wrap gap-2"></div>

                            <!-- Deskripsi kecil -->
                            <p class="text-xs text-gray-500 mt-1">Unggah hingga 5 foto yang berkaitan dengan temuan LCT. Pastikan file gambar tidak lebih dari 1MB dan dalam format PNG, JPG, atau GIF.</p>
                        </div>


                    
                        
                    </div>
                    
                    
                    <div class="flex flex-col gap-6 mt-4">
                        <!-- Kategori Temuan -->
                        <div x-data="{ open: false, selected: '', error: false }">
                            <label for="area" class="block text-sm font-medium text-gray-700">
                                Kategori Temuan <span class="text-red-500">*</span>
                            </label>

                            <!-- Dropdown Input with Icon and Text -->
                            <div class="relative mt-2">
                                <div class="flex justify-between items-center px-4 py-2 border border-black rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 cursor-pointer"
                                    @click="open = !open" :class="{ 'border-red-500': error }">
                                    <span x-text="selected || 'Pilih Kategori'" class="text-gray-700"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9l6 6 6-6"></path>
                                    </svg>
                                </div>

                                <!-- Dropdown list -->
                                <ul x-show="open" x-transition:enter="transition ease-out duration-200 transform"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-out duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="absolute z-10 w-full mt-1 bg-white border rounded-md shadow-lg max-h-48 overflow-auto"
                                    x-cloak>
                                    <template x-for="kategori in ['Kondisi Tidak Aman (Unsafe Condition)', 'Tindakan Tidak Aman (Unsafe Act)', '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)', 'Near miss']">
                                        <li @click="selected = kategori; open = false; error = false" class="px-4 py-2 cursor-pointer hover:bg-blue-100">
                                            <span x-text="kategori"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            <!-- Input Tersembunyi untuk Validasi -->
                            <input type="text" name="kategori_temuan" x-model="selected" required class="absolute opacity-0 w-0 h-0">

                            <p class="text-xs text-gray-500 mt-1">Pilih kategori yang sesuai dengan temuan LCT Anda. Misalnya, apakah ini berkaitan dengan kondisi atau tindakan yang tidak aman, atau masalah lainnya.</p> <!-- Deskripsi -->

                            <!-- Pesan error manual -->
                            <p x-show="error" class="text-red-500 text-xs mt-1">Silakan pilih kategori.</p>

                            <!-- Validasi saat submit -->
                            <script>
                                document.querySelector("form").addEventListener("submit", function (e) {
                                    let kategoriDropdown = document.querySelector("[x-data]");
                                    let selectedValue = kategoriDropdown.__x.$data.selected;

                                    if (!selectedValue) {
                                        e.preventDefault();
                                        kategoriDropdown.__x.$data.error = true;
                                    }
                                });
                            </script>
                        </div>

                    
                        <!-- Temuan Ketidaksesuaian -->
                        <div class="w-full">
                            <label for="temuan_ketidaksesuaian" class="block text-sm font-medium text-gray-700">
                                Temuan Ketidaksesuaian <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="temuan_ketidaksesuaian" 
                                name="temuan_ketidaksesuaian" 
                                class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" 
                                rows="4" 
                                required
                            ></textarea>
                            
                            <!-- Deskripsi kecil -->
                            <p class="text-xs text-gray-500 mt-1">Deskripsikan temuan ketidaksesuaian yang ditemukan di area LCT. Jelaskan secara rinci agar dapat segera ditindaklanjuti.</p>
                        </div>
                    </div>
                    

                    <!-- Rekomendasi Safety -->
                    <div class="mb-4">
                        <label for="rekomendasi_safety" class="block text-sm font-medium text-gray-700">Rekomendasi Safety <span class="text-red-500">*</span></label>
                        <textarea id="rekomendasi_safety" name="rekomendasi_safety" rows="4" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required></textarea>
                        <p class="text-xs text-gray-500">Masukkan rekomendasi untuk memperbaiki kondisi atau tindakan yang tidak aman. Berikan saran yang dapat membantu meningkatkan keselamatan di area tersebut.</p>
                    </div>

                    <!-- Tombol Kirim -->
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md focus:ring-2 focus:ring-blue-500 mt-2 cursor-pointer">Kirim Laporan</button>
                </form>
            </div>

        </div>
    </div>
</div>

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    </script>
@endif

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const fileInput = document.getElementById("dropzone-file");
        const openCameraBtn = document.getElementById("open-camera");
        const video = document.getElementById("video");
        const captureBtn = document.getElementById("capture-photo");
        const previewContainer = document.getElementById("preview-container");
    
        let stream = null;
        let images = []; // Array untuk menyimpan foto (maks 5)
    
        // Fungsi untuk menampilkan preview gambar
        function updatePreview() {
            previewContainer.innerHTML = ""; // Kosongkan preview sebelumnya
    
            images.forEach((imgSrc, index) => {
                const imgWrapper = document.createElement("div");
                imgWrapper.className = "relative";
    
                const img = document.createElement("img");
                img.src = imgSrc;
                img.className = "w-24 h-24 object-cover border rounded-lg";
    
                // Tombol hapus
                const deleteBtn = document.createElement("button");
                deleteBtn.innerText = "×";
                deleteBtn.className = "absolute top-0 right-0 bg-red-500 text-white text-xs px-2 py-1 rounded-full";
                deleteBtn.onclick = () => {
                    images.splice(index, 1);
                    updatePreview();
                };
    
                imgWrapper.appendChild(img);
                imgWrapper.appendChild(deleteBtn);
                previewContainer.appendChild(imgWrapper);
            });
        }
    
        // Fungsi menangani file input (galeri)
        fileInput.addEventListener("change", (event) => {
            if (images.length >= 5) {
                alert("Maksimal 5 foto!");
                fileInput.value = ""; // Reset input
                return;
            }
    
            const files = Array.from(event.target.files);
            files.forEach((file) => {
                if (images.length < 5) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        images.push(e.target.result);
                        updatePreview();
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    
        // Fungsi mengakses kamera
        openCameraBtn.addEventListener("click", async () => {
            if (images.length >= 5) {
                alert("Maksimal 5 foto!");
                return;
            }
    
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                video.style.display = "block";
                captureBtn.style.display = "block";
            } catch (error) {
                console.error("Gagal mengakses kamera: ", error);
                alert("Tidak dapat mengakses kamera!");
            }
        });
    
        // Fungsi menangkap gambar dari kamera
        captureBtn.addEventListener("click", () => {
            if (images.length >= 5) {
                alert("Maksimal 5 foto!");
                return;
            }
    
            const canvas = document.createElement("canvas");
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const context = canvas.getContext("2d");
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
            const imageDataURL = canvas.toDataURL("image/png");
            images.push(imageDataURL);
            updatePreview();
    
            // Matikan kamera setelah ambil gambar
            stream.getTracks().forEach(track => track.stop());
            video.style.display = "none";
            captureBtn.style.display = "none";
        });
    });
    </script>
    


<script>
     document.getElementById('tanggal_temuan').addEventListener('click', function() {
        this.showPicker();
    });
</script>

@endsection