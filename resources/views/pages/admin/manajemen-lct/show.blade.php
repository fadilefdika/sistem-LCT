<x-app-layout>

    <div x-data="{activeTab: 'laporan'}" class="px-5 pt-2 pb-8">
        <!-- Tabs -->
        <div class="flex space-x-4 border-b">
            <button @click="activeTab = 'laporan'" :class="activeTab === 'laporan' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                class="px-4 py-2 focus:outline-none cursor-pointer">
                Laporan LCT
            </button>
            <!-- Menampilkan tombol Task & Timeline hanya jika status LCT adalah 'approved' dan tingkat bahaya Medium atau High -->
            @if(in_array($laporan->tingkat_bahaya, ['Medium', 'High']) && $laporan->status_lct === 'approved')
            <button @click="activeTab = 'task-and-timeline'" 
                    :class="activeTab === 'task-and-timeline' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                    class="px-4 py-2 focus:outline-none cursor-pointer">
                Task & Timeline
            </button>
            @endif
        </div>

        <!-- Tab Content -->
        <div class="mt-1">
            <!-- Laporan -->
            <div x-show="activeTab === 'laporan'" >
                <div class="max-h-screen flex justify-center items-center">
                    <div class="grid md:grid-cols-2 justify-center w-full">
                        <!-- Card Laporan dari EHS -->
                        <div class="w-full mx-auto bg-[#F3F4F6] overflow-hidden max-h-[calc(100vh)] pb-28 overflow-y-auto 
                                    [&::-webkit-scrollbar]:w-1
                                    [&::-webkit-scrollbar-track]:rounded-full
                                    [&::-webkit-scrollbar-track]:bg-gray-100
                                    [&::-webkit-scrollbar-thumb]:rounded-full
                                    [&::-webkit-scrollbar-thumb]:bg-gray-300
                                    dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                                    dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                            
                            <div class="m-3  rounded-lg">
                
                                <!-- Card Laporan -->
                                <div class="bg-white p-5 rounded-xl shadow-md border ">
                                    <!-- Header -->
                                    <div class="flex justify-between items-center bg-white rounded-lg">
                                        <!-- Judul -->
                                        <h5 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                            📝 Laporan dari EHS
                                        </h5>
                                    
                                        <!-- Status Laporan -->
                                        <div class="flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold 
                                            @if($laporan->status_lct === 'approved') bg-green-100 text-green-700 border border-green-400 
                                            @elseif($laporan->status_lct === 'rejected') bg-red-100 text-red-700 border border-red-400 
                                            @else bg-yellow-100 text-yellow-700 border border-yellow-400 @endif">
                                            
                                            <!-- Ikon Status -->
                                            @if($laporan->status_lct === 'approved')
                                                <i class="fas fa-check-circle text-green-500"></i>
                                                <span>Disetujui</span>
                                            @elseif($laporan->status_lct === 'rejected')
                                                <i class="fas fa-times-circle text-red-500"></i>
                                                <span>Ditolak</span>
                                            @else
                                                <i class="fas fa-hourglass-half text-yellow-500"></i>
                                                <span>Menunggu Persetujuan</span>
                                            @endif
                                        </div>
                                    </div>                                    
                                    
                                    <!-- Garis Pemisah -->
                                    <div class="w-full h-[2px] bg-gray-200 my-3"></div>
                
                                    <!-- Isi Laporan -->
                                    <div class="flex flex-col space-y-1 mt-4">
                                        <p class="text-gray-500 text-xs">Temuan Ketidaksesuaian</p>
                                        <p class="text-gray-900 font-semibold text-lg">{{$laporan->temuan_ketidaksesuaian}}</p>
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
                                        <p class="text-gray-900 font-semibold text-sm mt-1">{{ $laporan->picUser->fullname ?? 'Tidak ada PIC' }}</p>
                                    </div>
                
                                    <!-- Garis Pemisah -->
                                    <div class="w-[2px] bg-gray-300 h-10 rounded-full"></div>
                
                                   <!-- Tanggal Temuan -->
                                    <div x-data="{ 
                                            rawTanggalTemuan: '{{ $laporan->tanggal_temuan }}',
                                            formattedTanggalTemuan: ''
                                        }"
                                        x-init="
                                            let temuanDate = new Date(rawTanggalTemuan);
                                            if (!isNaN(temuanDate)) {
                                                formattedTanggalTemuan = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(temuanDate);
                                            } else {
                                                formattedTanggalTemuan = 'Tanggal tidak valid';
                                            }
                                        "
                                        class="flex flex-col items-start">

                                        <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                                            <i class="fas fa-calendar-alt text-green-500"></i> <!-- Ikon Kalender -->
                                            <p>Tanggal Temuan</p>
                                        </div>

                                        <p class="text-gray-900 font-semibold text-sm mt-1" x-text="formattedTanggalTemuan"></p>
                                    </div>

                
                                    <!-- Garis Pemisah -->
                                    <div class="w-[2px] bg-gray-300 h-10 rounded-full"></div>
                
                                    <!-- Area Temuan -->
                                    <div class="flex flex-col items-start">
                                        <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                                            <i class="fas fa-map-marker-alt text-red-500"></i> <!-- Ikon Lokasi -->
                                            <p>Detail Area Temuan</p>
                                        </div>
                                        <p class="text-gray-900 font-semibold text-sm mt-1">{{ $laporan->area}} - {{ $laporan->detail_area }}</p>
                                    </div>
                
                                </div>
                
                                <!-- Card Due Date -->
                                <div x-data="{ 
                                    rawDueDate: '{{$laporan->due_date}}', 
                                    today: new Date(), 
                                    formattedDueDate: '', 
                                    daysLeft: 0, 
                                    isApproved: ['approved', 'closed'].includes('{{ $laporan->status_lct }}')
                                }"
                                x-init="
                                    let due = new Date(rawDueDate);
                                    if (!isNaN(due)) {
                                        formattedDueDate = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(due);
                                        let diffTime = due - today;
                                        daysLeft = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                    } else {
                                        formattedDueDate = 'Tanggal tidak valid';
                                        daysLeft = null;
                                    }
                                "
                                :class="daysLeft !== null && daysLeft < 0 ? 'border-l-4 border-red-500' : 'border-l-4 border-green-500'"
                                class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3 flex flex-col items-start">
                                
                                    <div class="flex items-center gap-2 text-gray-500 text-xs tracking-wide">
                                        <i class="fas fa-calendar-alt text-lg"
                                            :class="daysLeft !== null && daysLeft < 0 ? 'text-red-500' : 'text-green-500'"></i>
                                        <p class="font-medium">Due Date</p>
                                    </div>
                                
                                    <p class="text-sm font-semibold mt-1"
                                        :class="daysLeft !== null && daysLeft < 0 ? 'text-red-600' : 'text-gray-900'">
                                        <span x-text="formattedDueDate"></span>
                                    </p>
                                
                                    <!-- Bagian Hitungan Mundur -->
                                    <p x-show="!isApproved" class="text-xs font-medium mt-2"
                                        :class="daysLeft !== null && daysLeft < 0 ? 'text-red-500' : 'text-green-500'">
                                        <template x-if="daysLeft !== null && daysLeft < 0">
                                            <span>Overdue sejak <span x-text="Math.abs(daysLeft)"></span> hari yang lalu</span>
                                        </template>
                                        <template x-if="daysLeft !== null && daysLeft >= 0">
                                            <span><span x-text="daysLeft"></span> hari lagi sebelum overdue</span>
                                        </template>
                                        <template x-if="daysLeft === null">
                                            <span class="text-gray-500">Tanggal tidak valid</span>
                                        </template>
                                    </p>
                                </div>
                                


                            
                                <!-- Card Kategori Temuan -->
                                <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
                                    <div class="flex items-center space-x-2">
                                        <i class="fa-solid fa-flag text-yellow-500 text-lg"></i>
                                        <p class="text-gray-500 text-xs">Kategori Temuan</p>
                                    </div>
                                    <p class="text-gray-900 font-semibold mt-2 bg-yellow-100 p-2 rounded-lg hover:bg-yellow-200 transition-all duration-200 ease-in-out">{{$laporan->kategori_temuan}}</p>
                                </div>
                
                               <!-- Card Tingkat Bahaya -->
                                <div x-data="{ level: '{{ $laporan->tingkat_bahaya }}' }" class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
                                    <div class="flex items-center space-x-2">
                                        <i :class="{
                                            'text-green-500 fa-check-circle': level === 'Low',
                                            'text-yellow-500 fa-exclamation-triangle': level === 'Medium',
                                            'text-red-500 fa-skull-crossbones': level === 'High'
                                        }" class="fa-solid text-lg"></i>
                                        <p class="text-gray-500 text-xs">Tingkat Bahaya</p>
                                    </div>
                                    <p :class="{
                                        'bg-green-100 text-green-900 hover:bg-green-200': level === 'Low',
                                        'bg-yellow-100 text-yellow-900 hover:bg-yellow-200': level === 'Medium',
                                        'bg-red-100 text-red-900 hover:bg-red-200': level === 'High'
                                    }" class="text-gray-900 font-semibold mt-2 p-2 rounded-lg transition-all duration-200 ease-in-out">
                                        <span x-text="level === 'Low' ? 'Low' : level === 'Medium' ? 'Medium' : 'High'"></span>
                                    </p>
                                </div>
                
                                @if($laporan->status_lct === 'rejected') 
                                <!-- Card Laporan Ditolak -->
                                <div class="bg-white p-4 rounded-lg border border-red-300 mt-3 shadow-md hover:shadow-xl transition-all duration-300 ease-in-out">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <i class="fa-solid fa-exclamation-circle text-red-500 text-lg"></i>
                                        <p class="text-gray-500 text-xs font-semibold">Laporan Ditolak</p>
                                    </div>

                                    @if ($laporan->rejectLaporan->isNotEmpty())
                                        @foreach ($laporan->rejectLaporan as $reject)
                                            <div class="bg-red-50 p-3 rounded-lg mb-2">
                                                <p class="text-red-700 text-sm"><strong>Alasan:</strong> {{ $reject->alasan_reject }}</p>
                                                <p class="text-gray-500 text-xs">{{ $reject->created_at->format('d M Y H:i') }}</p>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500 text-sm">Belum ada alasan penolakan yang dicatat.</p>
                                    @endif
                                </div>

                                <!-- Card Revisi PIC (Hanya tampil jika ada revisi) -->
                                @if (!empty($laporan->revisi_pic))
                                    <div class="bg-white p-4 rounded-lg border border-blue-300 mt-3 shadow-md hover:shadow-xl transition-all duration-300 ease-in-out">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <i class="fa-solid fa-edit text-blue-500 text-lg"></i>
                                            <p class="text-gray-500 text-xs font-semibold">Revisi Diterima oleh PIC</p>
                                        </div>
                                        <p class="text-gray-900 mt-2 text-justify leading-relaxed text-sm">
                                            {{ $laporan->revisi_pic }}
                                        </p>
                                    </div>
                                @endif
                            @else
                                <!-- Card Rekomendasi Safety (Jika status_lct bukan rejected) -->
                                <div class="bg-white p-4 rounded-lg border border-green-300 mt-3 shadow-md hover:shadow-xl transition-all duration-300 ease-in-out">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <i class="fa-solid fa-shield-alt text-green-500 text-lg"></i>
                                        <p class="text-gray-500 text-xs font-semibold">Rekomendasi Safety</p>
                                    </div>
                                    <p class="text-gray-900 mt-2 text-justify leading-relaxed text-sm">
                                        {{ $laporan->rekomendasi_safety ?? 'Tidak ada rekomendasi safety' }}
                                    </p>
                                </div>
                            @endif

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
                        <div class="max-w-full bg-[#F3F4F6] overflow-hidden shadow-md px-3 pt-3 pb-32 max-h-[calc(100vh)] overflow-y-auto [&::-webkit-scrollbar]:w-1
                            [&::-webkit-scrollbar-track]:rounded-full
                            [&::-webkit-scrollbar-track]:bg-gray-100
                            [&::-webkit-scrollbar-thumb]:rounded-full
                            [&::-webkit-scrollbar-thumb]:bg-gray-300
                            dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                            dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                                <div class="bg-white p-5 max-h-min rounded-lg shadow-lg">
                                    <div class="bg-primary text-black text-center py-4 px-7 rounded-t-lg">
                                        @php
                                            $formTitle = '';

                                            if (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                                                if ($laporan->status_lct === 'rejected') {
                                                    $formTitle = 'Formulir Revisi Perbaikan Sementara ke EHS';
                                                } else {
                                                    $formTitle = 'Formulir Laporan Perbaikan Sementara ke EHS';
                                                }
                                            } elseif ($laporan->status_lct === 'rejected' && $laporan->tingkat_bahaya === 'Low') {
                                                $formTitle = 'Formulir Revisi Perbaikan ke EHS';
                                            }
                                        @endphp

                                        <h5 class="text-xl font-bold">{{ $formTitle }}</h5>
                                    </div>
                
                                    <div class="w-full h-[2px] bg-gray-200 px-3"></div>
                
                                    <div class="p-6">
                                        <form action="{{ route('admin.manajemen-lct.store', ['id_laporan_lct' => $laporan->id_laporan_lct]) }}" method="POST">
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
                                        
                                                <div class="mb-4">
                                                    <label for="batas_waktu" class="block text-sm font-medium text-gray-700 mb-1">Batas Waktu Perbaikan</label>
                                                    <input type="date" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="batas_waktu" name="batas_waktu" value="{{ \Carbon\Carbon::parse($laporan->due_date)->format('Y-m-d') }}" required readonly>
                                                </div>
                                        
                                                <div class="mb-4">
                                                    <label for="date_completion" class="block text-sm font-medium text-gray-700 mb-1">Date of Completion</label>
                                                    <input type="date" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="date_completion" name="date_completion" required>
                                                </div>
                                        
                                                <!-- Unggah Foto -->
                                                <div class="order-2 md:order-1">
                                                    <label for="foto_temuan" class="block text-sm font-medium text-gray-700">
                                                        Unggah Foto <span class="text-red-500">*</span>
                                                    </label>
                                                    <div class="flex items-center justify-center w-full mt-2">
                                                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                                </svg>
                                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF (MAX. 1MB)</p>
                                                            </div>
                                                            <input id="dropzone-file" type="file" class="hidden" accept="image/*"/>
                                                        </label>
                                                    </div> 
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
            </div>

            <div x-show="activeTab === 'task-and-timeline'">
                <div class="w-full mx-auto bg-[#F3F4F6] overflow-hidden max-h-[calc(100vh)] pb-36 pt-3">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold mb-4">Budget Submission for LCT Repairs</h2>
                        <form action="{{ route('admin.manajemen-lct.submitBudget', ['id_laporan_lct' => $laporan->id_laporan_lct]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-6">
                                <div x-data="{
                                    formattedAmount: '',
                                    formatAmount(value) {
                                        value = value.replace(/\D/g, '');
                                        this.formattedAmount = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                    }
                                }">
                                    <label for="budget_amount" class="block text-sm font-medium text-gray-700">Budget Amount <span class="text-red-500">*</span></label>
                                    <input 
                                        type="text" 
                                        name="budget_amount" 
                                        id="budget_amount" 
                                        x-model="formattedAmount"
                                        x-on:input="formatAmount($event.target.value)" 
                                        required 
                                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md" 
                                        placeholder="Enter amount"
                                    >
                                </div>
                                <p class="text-xs text-gray-500">Enter the amount in Indonesian Rupiah without symbols (e.g., 1.500.000)</p>
                            </div>
            
                            <div class="mb-6">
                                <label for="budget_description" class="block text-sm font-medium text-gray-700">Budget Description <span class="text-red-500">*</span></label>
                                <textarea name="budget_description" id="budget_description" rows="4" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md"></textarea>
                            </div>
            
                            <!-- Payment Proof Attachment -->
                            <div class="mb-6">
                                <label for="payment_proof" class="block text-sm font-medium text-gray-700">Payment Proof Attachment <span class="text-red-500">*</span></label>
                                
                                <input 
                                    type="file" 
                                    name="payment_proof" 
                                    id="payment_proof" 
                                    accept="image/*,application/pdf" 
                                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                >
                                <p class="text-sm text-gray-500 mt-2">Upload an image or PDF file as proof of payment.</p>
                            </div>
            
                            <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200 cursor-pointer">
                                Submit Budget Request
                            </button>
                        </form>
            
                        <!-- After Approval -->
                        @if($laporan->budget_approval == 'approved')
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-4">Tasks and Timeline</h3>
            
                            <!-- Task Creation Form -->
                            <form action="{{-- route('submit-task') --}}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="task_name" class="block text-sm font-medium text-gray-700">Task Name</label>
                                    <input type="text" name="task_name" id="task_name" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                                </div>
            
                                <div class="mb-4">
                                    <label for="task_description" class="block text-sm font-medium text-gray-700">Task Description</label>
                                    <textarea name="task_description" id="task_description" rows="3" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md"></textarea>
                                </div>
            
                                <div class="mb-4">
                                    <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                                    <input type="date" name="due_date" id="due_date" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                                </div>
            
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                    Create Task
                                </button>
                            </form>
            
                            <!-- Task Timeline -->
                            <div class="mt-6">
                                <h4 class="text-lg font-semibold mb-4">Task Progress</h4>
            
                                <!-- Task Status Table -->
                                <table class="min-w-full table-auto">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 border-b">Task Name</th>
                                            <th class="px-4 py-2 border-b">Status</th>
                                            <th class="px-4 py-2 border-b">Due Date</th>
                                            <th class="px-4 py-2 border-b">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tasks as $task)
                                        <tr>
                                            <td class="px-4 py-2 border-b">{{ $task->task_name }}</td>
                                            <td class="px-4 py-2 border-b">
                                                <span :class="{
                                                    'text-green-500': '{{ $task->status }}' === 'completed',
                                                    'text-yellow-500': '{{ $task->status }}' === 'in_progress',
                                                    'text-red-500': '{{ $task->status }}' === 'pending'
                                                }">{{ $task->status }}</span>
                                            </td>
                                            <td class="px-4 py-2 border-b">{{ $task->due_date }}</td>
                                            <td class="px-4 py-2 border-b">
                                                <!-- Change Status Button -->
                                                <form action="{{ route('update-task-status', $task->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <select name="status" onchange="this.form.submit()" class="px-2 py-1 border border-gray-300 rounded-md">
                                                        <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            </div>
        </div>
    </div>

</x-app-layout>



