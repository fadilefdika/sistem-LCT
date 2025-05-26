@forelse($laporans as $index => $laporan)
                            @php
                                $laporanId = $laporan->id;
                            @endphp
                            <tr @click="openRow === {{ $laporanId }} ? openRow = null : openRow = {{ $laporanId }}" class="hover:bg-gray-100 cursor-pointer text-sm transition duration-200 ease-in-out">
                                <td class="px-4 py-3 text-center font-semibold text-gray-800 w-12">
                                    {{ $laporans->firstItem() + $index }}
                                </td>
                                <!-- Tanggal Temuan -->
                                <td class="px-4 py-3 text-xs text-gray-800 w-32 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('F d, Y') }}
                                </td>
                                <!-- Tenggat Waktu -->
                                @php
                                    $today = now();
                                    $isOverdue = false;
                                    $dueDateToShow = null;
                                
                                    if ($laporan->status_lct !== 'closed') {
                                        if ($laporan->tingkat_bahaya === 'Low') {
                                            // Untuk tingkat bahaya Low, pakai due_date biasa
                                            $dueDateToShow = $laporan->due_date;
                                
                                            // Overdue jika due_date ada, belum selesai (date_completion null), dan hari ini sudah lewat due_date
                                            $isOverdue = $dueDateToShow && !$laporan->date_completion && $today->gt($dueDateToShow);
                                
                                        } elseif (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                                            // Untuk Medium dan High, seharusnya pakai due_date_temp dan due_date_perm
                                            // Tapi karena belum ada data, sementara kita komentar dulu
                                
                                            // if (!$laporan->date_completion_temp) {
                                            //     $dueDateToShow = $laporan->due_date_temp;
                                            //     $isOverdue = $dueDateToShow && !$laporan->date_completion_temp && $today->gt($dueDateToShow);
                                            // } else {
                                            //     $dueDateToShow = $laporan->due_date_perm;
                                            //     $isOverdue = $dueDateToShow && !$laporan->date_completion && $today->gt($dueDateToShow);
                                            // }
                                
                                            // Sementara pakai due_date saja sebagai pengganti
                                            $dueDateToShow = $laporan->due_date;
                                            $isOverdue = $dueDateToShow && !$laporan->date_completion && $today->gt($dueDateToShow);
                                        }
                                    } else {
                                        // Kalau status sudah closed, tetap tampilkan due_date sesuai tingkat bahaya
                                
                                        if ($laporan->tingkat_bahaya === 'Low') {
                                            $dueDateToShow = $laporan->due_date;
                                
                                        } elseif (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                                            // Normalnya ini pakai due_date_temp dan due_date_perm,
                                            // tapi karena belum ada, kita pakai due_date dulu
                                            // $dueDateToShow = $laporan->date_completion_temp ? $laporan->due_date_perm : $laporan->due_date_temp;
                                            
                                            $dueDateToShow = $laporan->due_date;
                                        }
                                    }
                                @endphp
                                
                                <td class="px-4 py-3 w-32 text-xs whitespace-nowrap {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                                    {{-- Tampilkan tanggal due date, format "Month day, Year" --}}
                                    {{ $dueDateToShow ? \Carbon\Carbon::parse($dueDateToShow)->format('F d, Y') : '-' }}
                                
                                    {{-- Jika overdue, tampilkan label "Overdue" --}}
                                    @if($isOverdue)
                                        <span class="ml-1 text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Overdue</span>
                                    @endif
                                </td>
                            

                            
                                @php
                                    $fullname = $laporan->picUser->fullname ?? '-';
                                    $words = explode(' ', $fullname);
                                    $formatted = '';
                                
                                    if (count($words) > 0) {
                                        $formatted .= $words[0];
                                    }
                                
                                    if (count($words) > 1) {
                                        $formatted .= ' ' . $words[1];
                                    }
                                
                                    if (count($words) > 2) {
                                        for ($i = 2; $i < count($words); $i++) {
                                            $formatted .= ' ' . strtoupper(substr($words[$i], 0, 1)) . '.';
                                        }
                                    }
                                @endphp
                                
                                <td class="px-4 py-3 text-gray-800 w-40 text-[10px] whitespace-nowrap">{{ $formatted }}</td>
                                
                                
                                <!-- Tingkat Bahaya -->
                                <td class="px-4 py-3 text-gray-800 w-28">
                                    @php
                                        $bahayaColors = [
                                            'High' => 'bg-red-500',
                                            'Medium' => 'bg-yellow-500',
                                            'Low' => 'bg-green-500'
                                        ];
                                    @endphp
                                    <span class=" text-xs font-semibold text-gray-800">
                                        {{ $laporan->tingkat_bahaya }}
                                    </span>
                                </td>

                                <!-- Status Progress -->
                                <td class="px-4 py-3 text-gray-800 w-36">
                                    @php
                                        // List of status with labels, colors, and tracking descriptions
                                        $statusMapping = [
                                            'open' => ['label' => 'Open', 'color' => 'bg-gray-500', 'tracking' => 'Report has been created'],
                                            'review' => ['label' => 'Review', 'color' => 'bg-purple-500', 'tracking' => 'Report is under review'],
                                            'in_progress' => ['label' => 'Not Yet', 'color' => 'bg-red-500', 'tracking' => 'Report has been sent, but PIC has not viewed it'],
                                            'progress_work' => ['label' => 'Not Yet', 'color' => 'bg-red-500', 'tracking' => 'PIC has viewed the report'],
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
                                        ];

                                        // If danger level is Medium or High, adjust specific status colors
                                        if (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                                            foreach (['waiting_approval_temporary', 'approved_temporary', 'temporary_revision', 
                                                    'work_permanent', 'waiting_approval_permanent', 'approved_permanent', 
                                                    'permanent_revision'] as $key) {
                                                if (isset($statusMapping[$key])) {
                                                    $statusMapping[$key]['color'] = match ($key) {
                                                        'approved_temporary', 'approved_permanent' => 'bg-green-500',
                                                        'temporary_revision', 'permanent_revision' => 'bg-red-500',
                                                        'waiting_approval_temporary', 'waiting_approval_permanent' => 'bg-blue-500',
                                                        'work_permanent' => 'bg-yellow-500',
                                                        default => $statusMapping[$key]['color'],
                                                    };
                                                }
                                            }
                                        }

                                        // Get status from the report data
                                        $status = $statusMapping[$laporan->status_lct] ?? [
                                            'label' => 'Unknown',
                                            'color' => 'bg-gray-400',
                                            'tracking' => 'Status not found'
                                        ];
                                    @endphp

                                    <!-- Status Column -->
                                    <span class="inline-flex items-center justify-center px-3 py-1 text-[10px] font-semibold text-gray-800 whitespace-nowrap">
                                        {{ $status['label'] }}
                                    </span>
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
                                                    <div class="flex justify-end pt-2 space-x-4">
                                                        <a href="{{ route($routeName, $laporan->id_laporan_lct) }}"
                                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-blue-600 text-white text-xs font-semibold shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-150 ease-in-out">
                                                            Details    
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                            </svg>
                                                        </a>

                                                        @php
                                                            // Cek apakah pengguna adalah EHS atau bukan
                                                            if (Auth::guard('ehs')->check()) {
                                                                // Jika pengguna adalah EHS, ambil role dari relasi 'roles' di model EhsUser
                                                                $userRole = optional(Auth::guard('ehs')->user()->roles->first())->name;
                                                            } else {
                                                                // Jika pengguna bukan EHS, ambil role dari model User dengan roleLct
                                                                $userRole = optional(auth()->user()->roleLct->first())->name;
                                                            }
                                                        @endphp
                                                        @if ($userRole == 'ehs' && $laporan->status_lct == 'open')
                                                            <form id="form-close-{{ $laporan->id_laporan_lct }}"
                                                                action="{{ route('ehs.laporan-lct.closed', $laporan->id_laporan_lct) }}"
                                                                method="POST" class="inline-block">
                                                                @csrf
                                                                <button type="button"
                                                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-green-600 text-white text-xs font-semibold shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 transition duration-150 ease-in-out"
                                                                        onclick="confirmClose('{{ $laporan->id_laporan_lct }}')">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M6 18L18 6M6 6l12 12"/>
                                                                    </svg>
                                                                    Closed
                                                                </button>
                                                            </form>
                                                        @endif                                                    
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
                                                        @foreach($tindakanPerbaikan as $index => $tp)
                                                            <div class="space-y-4">
                                                                {{-- <p class="text-sm font-semibold text-gray-700">Revision {{ $index + 1 }}</p> --}}

                                                                <div class="flex flex-col lg:flex-row gap-6">
                                                                    <!-- Image Slider -->
                                                                    <div class="lg:w-1/3 w-full" x-data="{
                                                                        images: @js($tp['bukti']),
                                                                        current: 0,
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
                                                                            this.current = (this.current + 1) % this.images.length;
                                                                        },
                                                                        prev() {
                                                                            this.current = (this.current - 1 + this.images.length) % this.images.length;
                                                                        }
                                                                        }" x-init="startSlider" @mouseenter="stopSlider" @mouseleave="startSlider">
                                                                        <div class="relative aspect-video rounded-md overflow-hidden border border-gray-200">
                                                                            <template x-for="(img, idx) in images" :key="idx">
                                                                                <img :src="img" alt="Evidence"
                                                                                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500"
                                                                                    :class="{ 'opacity-100': idx === current, 'opacity-0': idx !== current }">
                                                                            </template>

                                                                            <template x-if="images.length > 1">
                                                                                <button @click="prev"
                                                                                    class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white text-gray-800 px-2 py-1 rounded-r-md">
                                                                                    ‹
                                                                                </button>
                                                                            </template>
                                                                            <template x-if="images.length > 1">
                                                                                <button @click="next"
                                                                                    class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white text-gray-800 px-2 py-1 rounded-l-md">
                                                                                    ›
                                                                                </button>
                                                                            </template>
                                                                        </div>

                                                                        <p class="text-center text-[10px] text-gray-500 mt-2">
                                                                            <span x-text="'This correction contains ' + images.length + ' image' + (images.length > 1 ? 's' : '') + '.'"></span>
                                                                        </p>
                                                                    </div>

                                                                    <!-- Detail Tindakan -->
                                                                    <div class="lg:w-2/3 w-full grid grid-cols-2 gap-4 text-sm">
                                                                        <div>
                                                                            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Completion Date</p>
                                                                            <p class="text-xs text-gray-800">{{ \Carbon\Carbon::parse($tp['tanggal'])->format('d M Y') ?? '-' }}</p>
                                                                        </div>
                                                                        <div>
                                                                            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">
                                                                                {{ strtolower($laporan->tingkat_bahaya) === 'low' ? 'Due Date' : 'Temporary Due Date' }}
                                                                            </p>
                                                                            <p class="text-xs text-gray-800">
                                                                                {{ \Carbon\Carbon::parse($laporan->due_date)->format('d M Y') }}
                                                                            </p>
                                                                        </div>
                                                                        @if(strtolower($laporan->tingkat_bahaya) !== 'low')
                                                                            <div>
                                                                                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Permanent Due Date</p>
                                                                                <p class="text-xs text-gray-800">
                                                                                    {{ \Carbon\Carbon::parse($laporan->due_date)->format('d M Y') }}
                                                                                </p>
                                                                            </div>
                                                                        @endif

                                                                        <div>
                                                                            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">PIC</p>
                                                                            <p class="text-xs text-gray-800">{{ $laporan->picUser->fullname ?? '-' }}</p>
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
                                                                            <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed">{{ $tp['tindakan'] }}</p>
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
                                                        @endforeach
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