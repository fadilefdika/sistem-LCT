<div class="bg-white p-6 relative shadow-md rounded-xl overflow-x-auto">
     
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div x-data="{ openRow: null }">
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-300 shadow-sm border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr class="text-left text-sm font-semibold text-gray-600">
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Finding Date</th>
                        <th class="px-4 py-3">Due Date</th>
                        <th class="px-4 py-3">Due Date Permanent</th>
                        <th class="px-4 py-3">Hazard Level</th>
                        <th class="px-4 py-3">Progress Status</th>
                        <th class="px-4 py-3">Completion Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($laporans as $index => $laporan)
    
                    @php
                        $laporanId = $laporan->id;
                        $tindakan_perbaikan = collect(json_decode($laporan->tindakan_perbaikan, true))->map(function ($entry) {
                            return [
                                'tanggal' => $entry['tanggal'],
                                'tindakan' => $entry['tindakan'],
                                'bukti' => collect($entry['bukti'])->map(fn($path) => asset('storage/' . $path)),
                            ];
                        });
    
                        // Status dan warna
                        $statusMapping = [
                                    'open' => ['label' => 'Open', 'color' => 'bg-gray-500', 'tracking' => 'Report has been created'],
                                    'review' => ['label' => 'Review', 'color' => 'bg-purple-500', 'tracking' => 'Report is under review'],
                                    'in_progress' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'Report has been sent, but PIC has not viewed it'],
                                    'progress_work' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'PIC has viewed the report'],
                                    'work_permanent' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'PIC is working on a permanent LCT'],
                                    'waiting_approval' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for LCT Low approval from EHS'],
                                    'waiting_approval_temporary' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for temporary LCT approval from EHS'],
                                    'waiting_approval_permanent' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for permanent LCT approval from EHS'],
                                    'waiting_approval_taskbudget' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for task and budget approval from the manager'],
                                    'approved' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'LCT Low has been approved by EHS'],
                                    'approved_temporary' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Temporary LCT has been approved by EHS'],
                                    'approved_permanent' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Permanent LCT has been approved by EHS'],
                                    'approved_taskbudget' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Task and budget for permanent LCT has been approved by the manager'],
                                    'revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'LCT Low needs revision by PIC'],
                                    'temporary_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'Temporary LCT needs revision by PIC'],
                                    'permanent_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'Permanent LCT needs revision by PIC'],
                                    'taskbudget_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'The LCT task and budget require revision by PIC'],
                                    'closed' => ['label' => 'Closed', 'color' => 'bg-green-700', 'tracking' => 'Report has been closed by PIC'],
                                ]; // Potong agar pendek — gunakan definisi Anda sebelumnya
                        $status = $statusMapping[$laporan->status_lct] ?? [
                            'label' => 'Unknown',
                            'color' => 'bg-gray-400',
                            'tracking' => 'Status not found'
                        ];
                        $bahayaColors = [
                            'High' => 'bg-red-500',
                            'Medium' => 'bg-yellow-500',
                            'Low' => 'bg-green-500'
                        ];
                    @endphp
    
                    <!-- Baris utama -->
                    <tr @click="openRow === {{ $laporanId }} ? openRow = null : openRow = {{ $laporanId }}"
                        class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out cursor-pointer">
                        <td class="px-4 py-3 text-xs text-center font-semibold text-gray-800 w-12">{{ $laporans->firstItem() + $index }}</td>
                        <td class="px-4 py-3 text-xs text-gray-800 w-32 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('F d, Y') }}
                           
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-800 w-32 whitespace-nowrap">
                            @if($laporan->status_lct == 'open')
                                <p>-</p>
                            @elseif($laporan->tingkat_bahaya !== 'Low')
                                {{ \Carbon\Carbon::parse($laporan->due_date)->format('F d, Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($laporan->due_date_temp)->format('F d, Y') }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-800 w-32 whitespace-nowrap">
                            @if($laporan->status_lct == 'open')
                                <p>-</p>
                            @elseif($laporan->tingkat_bahaya !== 'Low')
                                {{ \Carbon\Carbon::parse($laporan->due_date_perm)->format('F d, Y') }}
                            @else
                                <p>-</p>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-xs text-gray-800 w-28">
                            <span class="px-3 py-1 text-xs text-gray-800 rounded-full">
                                {{ $laporan->tingkat_bahaya }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-800 w-36">
                            <span class="inline-flex items-center justify-center px-3 py-1 text-xs text-gray-800 rounded-full">
                                {{ $status['label'] }}
                            </span>
                        </td>
                        
                        <td class="px-4 py-3 text-xs text-gray-800 w-32 whitespace-nowrap">
                            @if ($laporan->date_completion)
                                {{ \Carbon\Carbon::parse($laporan->date_completion)->format('F d, Y') }}
                            @else
                                @php
                                    $dueDate = \Carbon\Carbon::parse($laporan->due_date)->startOfDay();
                                    $today = \Carbon\Carbon::now()->startOfDay();
                                    $overdueDays = $dueDate->diffInDays($today, false);
                                @endphp
    
                                @if ($overdueDays > 0)
                                    <span class="bg-red-100 text-red-600 text-sm font-semibold px-2 py-1 rounded">
                                        Overdue {{ $overdueDays }} days
                                    </span>
                                @elseif ($overdueDays === 0)
                                    <span class="bg-yellow-100 text-yellow-600 text-sm font-semibold px-2 py-1 rounded">
                                        Due Today
                                    </span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            @endif
                        </td>
                    </tr>
    
                    <!-- Detail Row -->
                    @php
                        $raw = $laporan->bukti_temuan;

                        // Kalau array, langsung gunakan. Kalau string, decode dulu.
                        $buktiArray = is_array($raw) ? $raw : json_decode($raw, true);

                        // Jika null karena gagal decode, fallback ke array kosong
                        $buktiArray = $buktiArray ?? [];

                        $bukti_temuan = collect($buktiArray)->map(fn($path) => asset('storage/' . $path));

                        $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
                        $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');           
                        if ($roleName === 'ehs') {
                            $routeName = $laporan->status_lct === 'open'
                                ? 'ehs.reporting.show.new'
                                : 'ehs.reporting.show';
                        } elseif ($roleName === 'pic') {
                            $routeName = 'admin.manajemen-lct.show';
                        } else {
                            $routeName = 'admin.reporting.show';
                        }

                    @endphp
    
                    <tr x-show="openRow === {{ $laporanId }}"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-y-90"
                        x-transition:enter-end="opacity-100 scale-y-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-y-100"
                        x-transition:leave-end="opacity-0 scale-y-90"
                        style="transform-origin: top">
                        <td colspan="8" class="w-full bg-gray-50 px-6 py-6">
                            <div class="w-full bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
                                <!-- Header -->
                                <div class="border-b pb-4 mb-4">
                                    <div class="flex justify-between items-center flex-wrap gap-4">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800 mb-1">📝 Finding Details</h3>
                                            <p class="text-xs text-gray-500">Report ID: <span class="font-medium text-gray-700">{{ $laporan->id_laporan_lct }}</span></p>
                                        </div>
                                        <div class="flex gap-4">
                                            <div>
                                                <p class="text-[10px] text-gray-500 uppercase font-semibold mb-1">Status</p>
                                                <span class="inline-flex px-3 py-1 text-[10px] font-semibold text-white rounded-full {{ $status['color'] }}">
                                                    {{ $status['label'] }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-gray-500 uppercase font-semibold mb-1">Hazard Level</p>
                                                <span class="inline-flex px-3 py-1 text-[10px] font-semibold text-white rounded-full {{ $bahayaColors[$laporan->tingkat_bahaya] ?? 'bg-gray-400' }}">
                                                    {{ $laporan->tingkat_bahaya }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                                <div x-data="{ activeTab: 'finder' }">
                                    <!-- Tabs -->
                                    <div class="flex border-b mb-4">
                                        <button @click="activeTab = 'finder'" 
                                                :class="activeTab === 'finder' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'" 
                                                class="px-4 py-2 text-sm font-semibold cursor-pointer">
                                            Finder Report
                                        </button>

                                        @if(!in_array($laporan->status_lct, ['open', 'in_progress', 'progress_work']))
                                            <button @click="activeTab = 'pic'" 
                                                    :class="activeTab === 'pic' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'" 
                                                    class="px-4 py-2 text-sm font-semibold cursor-pointer">
                                                PIC Improvement
                                            </button>
                                        @endif
                                    </div>

                                
                                    <!-- Finder Section -->
                                    <div x-show="activeTab === 'finder'">
                                        <div>
                                            <!-- Gambar dan Detail -->
                                            <div class="flex flex-col lg:flex-row gap-6 w-full">
                                                <div class="lg:w-1/3 w-full" x-data="{
                                                    images: @js($bukti_temuan),
                                                    current: 0,
                                                    interval: null,
                                                    startSlider() {
                                                        if (this.images.length > 1) {
                                                            this.interval = setInterval(() => {
                                                                this.next()
                                                            }, 3000)
                                                        }
                                                    },
                                                    stopSlider() {
                                                        clearInterval(this.interval)
                                                    },
                                                    next() {
                                                        this.current = (this.current + 1) % this.images.length
                                                    },
                                                    prev() {
                                                        this.current = (this.current - 1 + this.images.length) % this.images.length
                                                    }
                                                }" x-init="startSlider" @mouseenter="stopSlider" @mouseleave="startSlider">
                                                    <div class="relative aspect-video bg-gray-100 rounded-md overflow-hidden border border-gray-200">
                                                        <template x-for="(img, index) in images" :key="index">
                                                            <img
                                                                :src="img"
                                                                alt="Evidence"
                                                                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500"
                                                                :class="{ 'opacity-100': index === current, 'opacity-0': index !== current }"
                                                            >
                                                        </template>
                                                
                                                        <!-- Panah Navigasi (hanya jika gambar > 1) -->
                                                        <template x-if="images.length > 1">
                                                            <button @click="prev"
                                                                class="absolute cursor-pointer left-0 top-1/2 -translate-y-1/2 bg-white/60 hover:bg-white text-gray-800 px-2 py-1 rounded-r-md">
                                                                ‹
                                                            </button>
                                                        </template>
                                                
                                                        <template x-if="images.length > 1">
                                                            <button @click="next"
                                                                class="absolute cursor-pointer right-0 top-1/2 -translate-y-1/2 bg-white/60 hover:bg-white text-gray-800 px-2 py-1 rounded-l-md">
                                                                ›
                                                            </button>
                                                        </template>
                                                    </div>
                                                
                                                    <div class="mt-2 text-[10px] text-gray-500 text-center">
                                                        <span x-text="'This report contains ' + images.length + ' image' + (images.length > 1 ? 's' : '') + '.'"></span>
                                                    </div>
                                                </div>
                                                
                                    
                                                <div class="lg:w-2/3 w-full grid grid-cols-2 gap-4">
                                                    <div>
                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Finding Date</p>
                                                        <p class="text-xs text-gray-800">{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('d M Y') ?? '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Finder</p>
                                                        <p class="text-xs text-gray-800">{{ $laporan->user->fullname ?? '-' }}</p>
                                                    </div>
                                                    <div class="space-y-1">
                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Category</p>
                                                        <p class="text-xs text-gray-800">
                                                            {{ $laporan->kategori->nama_kategori === '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)' ? '5S' : $laporan->kategori->nama_kategori }}
                                                        </p>
                                                    </div>
                                                    <div class="space-y-1">
                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Area</p>
                                                        <p class="text-xs text-gray-800">
                                                            {{ optional($laporan->area)->nama_area ?? '-' }} - {{ $laporan->detail_area ?? '-' }}
                                                        </p>                                            
                                                    </div>
                                                    <div class="space-y-1 col-span-2">
                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Finding</p>
                                                        <p class="text-xs text-gray-800">{{ $laporan->temuan_ketidaksesuaian ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                    
                                            <!-- Safety Recommendation -->
                                            <div class="bg-blue-50/50 p-4 rounded-md border border-blue-100 w-full">
                                                <div class="flex items-start space-x-2">
                                                    <div class="mt-0.5">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-xs font-medium text-blue-800 mb-1">Safety Recommendation</h4>
                                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed">{{ $laporan->rekomendasi_safety }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                    
                                            <!-- Action Button -->
                                            <div class="flex justify-end pt-2">
                                                <a href="{{ route($routeName, $laporan->id_laporan_lct) }}"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                                    Details
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <!-- PIC Improvement Section -->
                                    @if(!in_array($laporan->status_lct, ['open', 'in_progress', 'progress_work']))
                                        <div x-show="activeTab === 'pic'" class="space-y-8">
                                            @php
                                                $tindakanPerbaikan = collect(json_decode($laporan->tindakan_perbaikan, true))->map(function ($entry) {
                                                    return [
                                                        'tanggal' => $entry['tanggal'],
                                                        'tindakan' => $entry['tindakan'],
                                                        'bukti' => collect($entry['bukti'])->map(fn($path) => asset('storage/' . $path)),
                                                    ];
                                                })->toArray(); // pastikan kembali ke array agar bisa digunakan di @foreach
                                            @endphp


                                            @if(is_array($tindakanPerbaikan) && count($tindakanPerbaikan))
                                                <div x-data="{
                                                    slides: @js($tindakanPerbaikan),
                                                    current: 0,
                                                    prev() {
                                                        this.current = (this.current - 1 + this.slides.length) % this.slides.length;
                                                    },
                                                    next() {
                                                        this.current = (this.current + 1) % this.slides.length;
                                                    }
                                                    }" class="relative overflow-hidden">

                                                    <!-- Wrapper untuk semua slide -->
                                                    <div class="flex transition-transform duration-500 ease-in-out"
                                                        :style="`transform: translateX(-${current * 100}%)`">

                                                        <template x-for="(tp, index) in slides" :key="index">
                                                            <div class="min-w-full p-4 space-y-4 border rounded-md shadow-md">
                                                                {{-- Konten sama seperti sebelumnya --}}
                                                                <div class="flex flex-col lg:flex-row gap-6">
                                                                    <!-- Image Slider -->
                                                                    <div class="lg:w-1/3 w-full" x-data="{
                                                                        images: tp.bukti,
                                                                        currentImg: 0,
                                                                        interval: null,
                                                                        startSlider() {
                                                                            if (this.images.length > 1) {
                                                                                this.interval = setInterval(() => this.next(), 3000);
                                                                            }
                                                                        },
                                                                        stopSlider() {
                                                                            clearInterval(this.interval);
                                                                        },
                                                                        next() {
                                                                            this.currentImg = (this.currentImg + 1) % this.images.length;
                                                                        },
                                                                        prev() {
                                                                            this.currentImg = (this.currentImg - 1 + this.images.length) % this.images.length;
                                                                        }
                                                                    }" x-init="startSlider" @mouseenter="stopSlider" @mouseleave="startSlider">
                                                                    <p class="text-center text-[10px] text-gray-500 mb-2" x-text="index === 0 ? '' : `Revision #${index}`"></p>

                                                                        <div class="relative aspect-video rounded-md overflow-hidden border border-gray-200 mb-2">
                                                                            <template x-for="(img, idx) in images" :key="idx">
                                                                                <img :src="img" alt="Evidence"
                                                                                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500"
                                                                                    :class="{ 'opacity-100': idx === currentImg, 'opacity-0': idx !== currentImg }">
                                                                            </template>

                                                                            <template x-if="images.length > 1">
                                                                                <button @click="prev"
                                                                                    class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white text-gray-800 px-2 py-1 cursor-pointer rounded-r-md">
                                                                                    ‹
                                                                                </button>
                                                                            </template>
                                                                            <template x-if="images.length > 1">
                                                                                <button @click="next"
                                                                                    class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white text-gray-800 px-2 py-1 cursor-pointer rounded-l-md">
                                                                                    ›
                                                                                </button>
                                                                            </template>
                                                                        </div>

                                                                        <p class="text-center text-[10px] text-gray-500 mt-2" x-text="'This correction contains ' + images.length + ' image' + (images.length > 1 ? 's' : '') + '.'"></p>
                                                                    </div>

                                                                    <!-- Detail Tindakan -->
                                                                    <div class="lg:w-2/3 w-full grid grid-cols-2 gap-6 text-sm bg-white p-4 rounded-md shadow-sm">
                                                                        <div class="border-b border-gray-200 pb-2">
                                                                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Completion Date</p>
                                                                            <p class="text-sm text-gray-900" x-text="new Date(tp.tanggal).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) ?? '-'"></p>
                                                                        </div>
                                                                        
                                                                        <div class="border-b border-gray-200 pb-2">
                                                                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">
                                                                                {{ strtolower($laporan->tingkat_bahaya) === 'low' ? 'Due Date' : 'Temporary Due Date' }}
                                                                            </p>
                                                                            <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($laporan->due_date)->format('d M Y') }}</p>
                                                                        </div>
                                                                        
                                                                        @if(strtolower($laporan->tingkat_bahaya) !== 'low')
                                                                        <div class="border-b border-gray-200 pb-2">
                                                                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Permanent Due Date</p>
                                                                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($laporan->due_date)->format('d M Y') }}</p>
                                                                        </div>
                                                                        @endif
                                                                    
                                                                        <div class="border-b border-gray-200 pb-2">
                                                                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">PIC</p>
                                                                        <p class="text-sm text-gray-900">{{ $laporan->picUser->fullname ?? '-' }}</p>
                                                                        </div>
                                                                    </div>                                                                              
                                                                </div>

                                                                <!-- Safety Recommendation -->
                                                                <div class="bg-blue-50/50 p-4 rounded-md border border-blue-100">
                                                                    <div class="flex items-start space-x-2">
                                                                        <div class="mt-0.5">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                                                                            </svg>
                                                                        </div>
                                                                        <div>
                                                                            <h4 class="text-xs font-medium text-blue-800 mb-1">Corrective Action</h4>
                                                                            <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed" x-text="tp.tindakan"></p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Action Button -->
                                                                <div class="flex justify-end">
                                                                    <a href="{{ route($routeName, $laporan->id_laporan_lct) }}"
                                                                        class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                                                        Details
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                                        </svg>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    <!-- Navigation: tampilkan hanya jika slide > 1 -->
                                                    @if(count($tindakanPerbaikan) > 1)
                                                        <div class="flex justify-between mt-4">
                                                            <button @click="prev()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 cursor-pointer">
                                                                ‹ Prev
                                                            </button>
                                                            <button @click="next()" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 cursor-pointer">
                                                                Next ›
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-500 italic">No corrective action provided.</p>
                                            @endif
                                        </div>
                                    @endif


                                </div>
                            </div>
                        </td>                        
                    </tr>
    
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-6 text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6">
                                    </path>
                                </svg>
                                <p class="text-sm">No reports are available.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    
    <div class="mt-6 flex flex-col sm:flex-row sm:justify-between sm:items-center border-t px-5 py-3 gap-3">
        <span class="text-sm text-gray-600 text-center sm:text-left">
            Showing {{ $laporans->firstItem() }} to {{ $laporans->lastItem() }} of {{ $laporans->total() }} entries
        </span>
        <div class="flex justify-center sm:justify-end">
            {{ $laporans->links('pagination::tailwind') }}
        </div>
    </div>
    
</div>