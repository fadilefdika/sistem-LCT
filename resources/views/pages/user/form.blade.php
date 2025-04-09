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
                            <label class="block text-sm font-medium text-gray-700">
                            Unggah Foto <span class="text-red-500">*</span>
                            </label>
                            
                            <div class="flex flex-col items-center justify-center w-full mt-2 space-y-4">
                                <!-- Upload Options -->
                                <div class="flex space-x-4 w-full">
                                    <!-- Gallery Button -->
                                    <button type="button" id="gallery-btn" class="flex-1 h-12 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    Pilih dari Galeri
                                    </button>
                                    
                                    <!-- Camera Button - Now opens native camera app -->
                                    <button type="button" id="camera-btn" class="flex-1 h-12 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors flex items-center justify-center lg:hidden">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    Ambil Foto
                                    </button>
                                </div>
                            
                            <!-- Hidden file input for gallery -->
                            <input id="gallery-input" name="bukti_temuan[]" type="file" class="hidden" accept="image/*" multiple>
                            
                            <!-- Hidden file input for camera (with capture attribute) -->
                            <input id="camera-input" name="bukti_temuan[]" type="file" class="hidden" accept="image/*" capture="environment">
                            
                            <input type="file" name="bukti_temuan[]" id="final-upload" multiple class="hidden">
                            <!-- Image Previews -->
                            <div id="preview-container" class="w-full flex flex-wrap gap-3"></div>
                            </div>
                            
                            <p class="text-xs text-gray-500 mt-1">
                            Unggah hingga 5 foto yang berkaitan dengan temuan LCT. Pastikan file gambar tidak lebih dari 1MB dan dalam format PNG, JPG, atau GIF.
                            </p>
                        </div>
                        <!-- Modal untuk memperbesar gambar -->
                        <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-70 hidden flex justify-center items-center z-50">
                            <div class="relative p-4 max-w-2xl">
                                <button id="close-modal" class="absolute top-2 right-2 bg-white text-black rounded-full w-8 h-8 flex items-center justify-center text-lg hover:bg-gray-300 transition">×</button>
                                <img id="modal-image" class="max-w-full max-h-screen object-contain rounded-lg" />
                            </div>
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
                                    @foreach ($kategori as $item)
                                    <li @click="selected = '{{ $item->nama_kategori }}'; selectedId = '{{ $item->id }}'; open = false; error = false"
                                        class="px-4 py-2 cursor-pointer hover:bg-blue-100">
                                        {{ $item->nama_kategori }}
                                    </li>
                                    @endforeach
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

{{-- Non-Conformity Image --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const maxFiles = 5;
    let compressedFiles = [];

    const galleryBtn = document.getElementById("gallery-btn");
    const cameraBtn = document.getElementById("camera-btn");
    const galleryInput = document.getElementById("gallery-input");
    const cameraInput = document.getElementById("camera-input");
    const previewContainer = document.getElementById("preview-container");
    const finalUpload = document.getElementById("final-upload");
    
    const modal = document.getElementById("image-modal");
    const modalImage = document.getElementById("modal-image");
    const closeModal = document.getElementById("close-modal");

    galleryBtn.addEventListener("click", () => galleryInput.click());
    cameraBtn.addEventListener("click", () => cameraInput.click());

    galleryInput.addEventListener("change", (event) => handleFileSelection(event.target.files));
    cameraInput.addEventListener("change", (event) => handleFileSelection(event.target.files));

    async function handleFileSelection(files) {
        const validFiles = Array.from(files).filter(file => file.type.startsWith("image/"));

        if (compressedFiles.length + validFiles.length > maxFiles) {
            alert("Maximum 5 images allowed.");
            return;
        }

        let newCompressedFiles = [];

        for (const file of validFiles) {
            try {
                const compressedFile = await compressImage(file);
                newCompressedFiles.push(compressedFile);
            } catch (error) {
                console.error("Image compression failed:", error);
                newCompressedFiles.push(file);
            }
        }

        compressedFiles = [...compressedFiles, ...newCompressedFiles];

        updateFinalUpload();
        renderPreviews();

        galleryInput.value = "";
        cameraInput.value = "";
    }

    function renderPreviews() {
        previewContainer.innerHTML = "";

        compressedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.createElement("div");
                preview.className = "relative w-24 h-24 rounded-lg overflow-hidden group flex flex-col items-center cursor-pointer";

                const img = document.createElement("img");
                img.src = e.target.result;
                img.className = "w-full h-full object-cover rounded-lg";

                img.addEventListener("click", () => openModal(e.target.result));

                const fileSize = (file.size / 1024).toFixed(2) + " KB";
                const sizeText = document.createElement("p");
                sizeText.innerText = fileSize;
                sizeText.className = "text-xs text-gray-500 mt-1";

                const removeBtn = document.createElement("button");
                removeBtn.innerHTML = "&times;";
                removeBtn.className = "absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm hover:bg-red-600 transition-colors";
                removeBtn.addEventListener("click", (e) => {
                    e.preventDefault();
                    compressedFiles.splice(index, 1);
                    updateFinalUpload();
                    renderPreviews();
                });

                preview.appendChild(img);
                preview.appendChild(sizeText);
                preview.appendChild(removeBtn);
                previewContainer.appendChild(preview);
            };
            reader.readAsDataURL(file);
        });
    }

    function openModal(imageSrc) {
        modalImage.src = imageSrc;
        modal.classList.remove("hidden");
    }

    closeModal.addEventListener("click", () => {
        modal.classList.add("hidden");
    });

    modal.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.classList.add("hidden");
        }
    });

    function updateFinalUpload() {
        const dataTransfer = new DataTransfer();
        compressedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        finalUpload.files = dataTransfer.files;
    }

    async function compressImage(file) {
        return new Promise((resolve, reject) => {
            if (file.type === 'image/webp' || !file.type.startsWith('image/')) {
                resolve(file);
                return;
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                const img = new Image();
                img.src = event.target.result;
                img.onload = () => {
                    const canvas = document.createElement("canvas");
                    const ctx = canvas.getContext("2d");

                    const MAX_WIDTH = 1024;
                    const MAX_HEIGHT = 1024;
                    let width = img.width;
                    let height = img.height;

                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }

                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }

                    canvas.width = width;
                    canvas.height = height;
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        if (!blob) {
                            console.warn("Compression failed, using original file");
                            resolve(file);
                            return;
                        }

                        const newFileName = file.name.replace(/\.[^/.]+$/, "") + '.webp';
                        const compressedFile = new File([blob], newFileName, {
                            type: "image/webp",
                            lastModified: Date.now()
                        });

                        resolve(compressedFile);
                    }, "image/webp", 0.7);
                };
                img.onerror = () => resolve(file);
            };
            reader.onerror = () => resolve(file);
            reader.readAsDataURL(file);
        });
    }
});
</script>


<script>
     document.getElementById('tanggal_temuan').addEventListener('click', function() {
        this.showPicker();
    });
</script>

@endsection