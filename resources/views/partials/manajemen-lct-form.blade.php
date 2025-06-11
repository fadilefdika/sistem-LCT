
    @php
            // Cek apakah pengguna menggunakan guard 'ehs' atau 'web' (untuk pengguna biasa)
            if (Auth::guard('ehs')->check()) {
                $user = Auth::guard('ehs')->user();
                $roleName = 'ehs';
            } else {
                $user = Auth::guard('web')->user();
                // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
                $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
            }
        
            // Tentukan role yang tidak diizinkan
            $notAllowed = in_array($roleName, ['user', 'manajer']);
        @endphp
    <div class="bg-white p-5 max-h-min rounded-lg shadow-lg">
        <div class="bg-primary text-black text-center py-4 px-7 rounded-t-lg">
            @php
                if (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                    $formTitle = ($laporan->status_lct === 'revision') 
                        ? 'Temporary Corrective Action Revision Form to EHS' 
                        : 'Temporary Corrective Action Report Form to EHS';
                } elseif ($laporan->tingkat_bahaya === 'Low') {
                    $formTitle = 'Corrective Action Revision Form to EHS';
                } elseif ($laporan->status_lct === 'revision') {
                    $formTitle = 'Corrective Action Revision Form to EHS';
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
                        <label for="temuan_ketidaksesuaian" class="block text-sm font-medium text-gray-700 mb-1">Non-Conformity Finding</label>
                        <input type="text" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="temuan_ketidaksesuaian" name="temuan_ketidaksesuaian" value="{{ $laporan->temuan_ketidaksesuaian }}" required readonly>
                    </div>
            
                    <div class="mb-4">
                        <label for="nama_pic" class="block text-sm font-medium text-gray-700 mb-1">PIC</label>
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
                    
                    @php
                        use Carbon\Carbon;
                    @endphp

                    <div>
                        <label for="date_completion" class="block text-sm font-medium text-gray-700">
                            Completion Date <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="date_completion" 
                            name="date_completion" 
                            class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500"
                            value="{{ old('date_completion', Carbon::now()->toDateString()) }}" 
                            required
                        >
                        <p class="mt-2 text-gray-600">
                            Tanggal default: {{ Carbon::now()->format('d-m-Y') }}
                        </p>
                    </div>


                    <div class="mb-4">
                        <label for="tindakan_perbaikan" class="block text-sm font-medium text-gray-700">Tindakan Perbaikan</label>
                        <textarea name="tindakan_perbaikan" id="tindakan_perbaikan" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>{{ old('tindakan_perbaikan') }}</textarea>
                    </div>
            
                    <!-- Bukti Perbaikan Foto -->
                    <div class="order-2">
                        <label for="dropzone-file" class="block text-sm font-medium text-gray-700">
                            Upload Photo <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-col items-center justify-center w-full mt-2">
                            <!-- Opsi memilih gambar dari galeri -->
                            <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-500">SVG, PNG, JPG, atau GIF (MAX. 1MB)</p>
                                </div>
                                <input id="dropzone-file" name="bukti_perbaikan[]" type="file" class="hidden" accept="image/*" multiple />
                            </label>

                            <!-- Opsi akses kamera dengan Webcam.js -->
                            <button type="button" id="open-camera" class="mt-4 w-full h-12 bg-blue-500 text-white rounded-lg">Capture Photo</button>

                            <!-- Area kamera -->
                            <div id="my_camera" class="mt-2 w-64 h-48 border" style="display: none;"></div>

                            <!-- Tombol ambil foto -->
                            <button type="button" id="capture-photo" class="mt-2 w-full h-12 bg-green-500 text-white rounded-lg" style="display: none;">Capture</button>

                            <!-- Menampilkan foto yang diambil atau dipilih -->
                            <div id="preview-container" class="mt-4 flex flex-wrap gap-2"></div>
                        </div>

                        <p class="text-xs text-gray-500 mt-1">
                            Upload up to 5 photos related to the LCT finding. Ensure that the image file size does not exceed 1MB and is in PNG, JPG, or GIF format.
                        </p>
                    </div>
                    
                <!-- Submit button -->
                @php
                    $disabledStatuses = [
                        'waiting_approval',
                        'waiting_approval_temporary',
                        'approved',
                        'approved_temporary',
                        'waiting_approval_taskbudget',
                    ];
                    $isDisabled = in_array($laporan->status_lct, $disabledStatuses);
                @endphp
                
                <button 
                    type="submit"
                    class="w-full mt-4 px-6 py-3 rounded-xl font-semibold text-sm text-white transition-all duration-200 ease-in-out
                        {{ $isDisabled ? 'bg-gray-400 cursor-not-allowed opacity-60' : 'bg-blue-700 hover:bg-blue-800 hover:scale-[1.02] active:scale-[0.98] focus:ring-4 focus:ring-blue-300' }}
                        dark:{{ $isDisabled ? 'bg-gray-600 dark:opacity-60' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-800' }}
                    "
                    {{ $isDisabled ? 'disabled' : '' }}
                >
                    🚀 Submit Report
                </button>
            

                </div>
            </form>
        </div>
        @php
            if ($roleName === 'ehs') {
                $routeName = 'ehs.reporting.history';
            } elseif ($roleName === 'pic') {
                $routeName = 'admin.manajemen-lct.history';
            } else {
                $routeName = 'admin.reporting.history';
            }
        @endphp
        
        <a href="{{ route($routeName, $laporan->id_laporan_lct) }}" class="inline-block">
            <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                <i class="fas fa-history mr-2"></i>History
            </button>
        </a>
    
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

<!-- Script untuk menangani Kamera -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let maxFiles = 5;
        let uploadedFiles = [];
    
        const dropzone = document.getElementById("dropzone-file");
        const openCameraBtn = document.getElementById("open-camera");
        const captureBtn = document.getElementById("capture-photo");
        const myCamera = document.getElementById("my_camera");
        const previewContainer = document.getElementById("preview-container");
    
        if (!dropzone) {
            console.error("Elemen dropzone-file tidak ditemukan.");
            return;
        }
    
        // Inisialisasi Webcam.js
        Webcam.set({
            width: 200,
            height: 150,
            image_format: 'jpeg',
            jpeg_quality: 90
        });
    
        openCameraBtn.addEventListener("click", function () {
            if (uploadedFiles.length >= maxFiles) {
                alert("Maksimal 5 foto yang bisa diunggah.");
                return;
            }
            myCamera.style.display = "block";
            captureBtn.style.display = "block";
            Webcam.attach(myCamera);
        });
    
        captureBtn.addEventListener("click", function () {
            Webcam.snap(async function (data_uri) {
                // Konversi base64 menjadi file
                const res = await fetch(data_uri);
                const blob = await res.blob();
                let file = new File([blob], `kamera-${Date.now()}.jpg`, { type: "image/jpeg" });
    
                try {
                    file = await compressImage(file);
                } catch (err) {
                    console.warn("Kompresi gagal, pakai file asli", err);
                }
    
                uploadedFiles.push(file);
                updateInputFiles();
                renderPreview();
    
                // Sembunyikan kamera
                Webcam.reset();
                myCamera.style.display = "none";
                captureBtn.style.display = "none";
            });
        });
    
        dropzone.addEventListener("change", async function (event) {
            let files = Array.from(event.target.files);
    
            if (uploadedFiles.length + files.length > maxFiles) {
                alert("Maksimal 5 foto yang bisa diunggah.");
                return;
            }
    
            // Kompresi semua file secara async
            const compressedFiles = [];
            for (const file of files) {
                try {
                    const compressed = await compressImage(file);
                    compressedFiles.push(compressed);
                } catch (err) {
                    console.warn("Kompresi gagal, pakai file asli", err);
                    compressedFiles.push(file);
                }
            }
    
            uploadedFiles = uploadedFiles.concat(compressedFiles);
            updateInputFiles();
            renderPreview();
        });
    
        function updateInputFiles() {
            let dataTransfer = new DataTransfer();
            uploadedFiles.forEach(file => dataTransfer.items.add(file));
            dropzone.files = dataTransfer.files;
        }
    
        function renderPreview() {
            previewContainer.innerHTML = "";
            uploadedFiles.forEach((file, index) => {
                let wrapper = document.createElement("div");
                wrapper.classList.add("relative", "w-24", "h-24", "rounded-lg", "overflow-hidden", "group");
    
                let img = document.createElement("img");
                img.src = URL.createObjectURL(file);
                img.classList.add("w-full", "h-full", "object-cover", "rounded-lg");
    
                let removeBtn = document.createElement("button");
                removeBtn.innerHTML = "&times;";
                removeBtn.classList.add(
                    "absolute", "top-0", "right-0", "bg-red-600", "text-white",
                    "rounded-full", "w-5", "h-5", "flex", "items-center", "justify-center",
                    "text-xs", "opacity-75", "hover:opacity-100", "transition-opacity",
                    "transform", "translate-x-1", "-translate-y-1", "hover:scale-110"
                );
                removeBtn.addEventListener("click", function () {
                    uploadedFiles.splice(index, 1);
                    updateInputFiles();
                    renderPreview();
                });
    
                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                previewContainer.appendChild(wrapper);
            });
        }
    
        // Fungsi kompresi gambar seperti sebelumnya
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
