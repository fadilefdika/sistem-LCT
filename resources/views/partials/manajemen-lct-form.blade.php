<div class="bg-white p-5 max-h-min rounded-lg shadow-lg">
    <div class="bg-primary text-black text-center py-4 px-7 rounded-t-lg">
        @php
            $formTitle = 'cek';

            if (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                if ($laporan->status_lct === 'rejected') {
                    $formTitle = 'Formulir Revisi Perbaikan Sementara ke EHS';
                } else {
                    $formTitle = 'Formulir Laporan Perbaikan Sementara ke EHS';
                }
            } elseif ($laporan->status_lct === 'rejected' || $laporan->tingkat_bahaya === 'Low') {
                $formTitle = 'Formulir Revisi Perbaikan ke EHS';
            }
        @endphp

        <h5 class="text-xl font-bold">{{ $formTitle }}</h5>
    </div>

    <div class="w-full h-[2px] bg-gray-200 px-3"></div>

    <div class="p-6">
        <form action="{{ route('admin.manajemen-lct.store', ['id_laporan_lct' => $laporan->id_laporan_lct]) }}" method="POST" enctype="multipart/form-data">
            @csrf
         
            <div class="space-y-6">
                <div class="mb-4">
                    <label for="temuan_ketidaksesuaian" class="block text-sm font-medium text-gray-700 mb-1">Temuan Ketidaksesuaian</label>
                    <input type="text" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="temuan_ketidaksesuaian" name="temuan_ketidaksesuaian" value="{{ $laporan->temuan_ketidaksesuaian }}" required readonly>
                </div>
        
                <div class="mb-4">
                    <label for="nama_pic" class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
                    <input type="text" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="nama_pic" name="nama_pic" value="{{ $laporan->picUser->fullname ?? '' }}" required readonly>
                </div>
        
                <!-- Repair Deadline -->
                <div class="mb-4">
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Repair Deadline</label>
                    <input 
                        type="text" 
                        class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-not-allowed" 
                        id="due_date" 
                        name="due_date" 
                        value="{{ $laporan->due_date ? \Carbon\Carbon::parse($laporan->due_date)->format('d F Y') : 'No due date set' }}" 
                        required 
                        readonly
                    >
                </div>
                
                <div>
                    <label for="date_completion" class="block text-sm font-medium text-gray-700">
                        Date of Completion <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="date_completion" 
                        name="date_completion" 
                        class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" 
                        required
                    >
                    <!-- Tempat untuk menampilkan tanggal dalam format yang lebih mudah dibaca -->
                    <p id="formatted_date" class="mt-2 text-gray-600"></p>
                </div>
        
                 <!-- Bukti Perbaikan Foto -->
                 <div class="order-2">
                    <label for="bukti_perbaikan" class="block text-sm font-medium text-gray-700">
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
                            <input id="dropzone-file" name="bukti_perbaikan[]" type="file" class="hidden" accept="image/*" multiple />
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
                    <p class="text-xs text-gray-500 mt-1">
                        Upload up to 5 images (PNG, JPG, GIF, max 1MB) as proof of LCT resolution.
                    </p>
                </div>
                
                <!-- Submit button -->
                <button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-3 mt-4 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 cursor-pointer">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Date Picker  --}}
<script>
    document.getElementById('date_completion').addEventListener('click', function() {
       this.showPicker();
   });
    document.getElementById('date_completion').addEventListener('change', function() {
        let dateValue = this.value;
        if (dateValue) {
            let options = { year: 'numeric', month: 'long', day: 'numeric' };
            let formattedDate = new Date(dateValue).toLocaleDateString('en-US', options);
            document.getElementById('formatted_date').textContent = `Selected Date: ${formattedDate}`;
        } else {
            document.getElementById('formatted_date').textContent = "";
        }
    });
</script>

{{-- Gambar Perbaikan --}}
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