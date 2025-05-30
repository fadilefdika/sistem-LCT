<div class="m-3  rounded-lg">

    <!-- Card Laporan -->
    <div class="bg-white p-5 rounded-xl shadow-md border ">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white rounded-lg">
            <!-- Judul -->
            <h5 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                📝 Report from EHS
            </h5>
        
            <!-- Status Laporan -->
            @php
            $statusMapping = [
                'In Progress' => ['in_progress', 'progress_work', 'work_permanent'],
                'Waiting Approval' => ['waiting_approval', 'waiting_approval_temporary', 'waiting_approval_permanent', 'waiting_approval_taskbudget'],
                'Approved' => ['approved', 'approved_temporary', 'approved_permanent', 'approved_taskbudget'],
                'Revision' => ['revision', 'temporary_revision', 'permanent_revision', 'taskbudget_revision'],
                'Closed' => ['closed']
            ];

            $statusText = 'Unknown';
            $statusColor = 'gray';
            $statusIcon = 'fas fa-hourglass-half text-gray-500';

            foreach ($statusMapping as $label => $statuses) {
                if (in_array($laporan->status_lct, $statuses)) {
                    $statusText = $label;
                    switch ($label) {
                        case 'In Progress':
                            $statusColor = 'blue';
                            $statusIcon = 'fas fa-hourglass-half text-blue-500';
                            break;
                        case 'Waiting Approval':
                            $statusColor = 'yellow';
                            $statusIcon = 'fas fa-hourglass-start text-yellow-500';
                            break;
                        case 'Approved':
                            $statusColor = 'green';
                            $statusIcon = 'fas fa-check-circle text-green-500';
                            break;
                        case 'Revision':
                            $statusColor = 'red';
                            $statusIcon = 'fas fa-times-circle text-red-500';
                            break;
                        case 'Closed':
                            $statusColor = 'green';
                            $statusIcon = 'ffas fa-check-circle text-green-500';
                            break;
                    }
                    break;
                }
            }
            @endphp

            <div class="flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold 
            bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 border border-{{ $statusColor }}-400">

            <div class="flex items-center space-x-2 text-sm font-medium">
                <i class="{{ $statusIcon }} text-lg"></i>
                <span class="text-{{ $statusColor }}-800">{{ $statusText }}</span>
            </div>                                         
            </div>
        </div>                                   
        
        <!-- Garis Pemisah -->
        <div class="w-full h-[2px] bg-gray-200 my-3"></div>

        <!-- Isi Laporan -->
        <div class="flex flex-col space-y-1 mt-4">
            <p class="text-gray-500 text-xs">Non-Conformity Finding</p>
            <p class="text-gray-900 font-semibold text-lg">{{$laporan->temuan_ketidaksesuaian}}</p>
        </div>
    </div>


    <!-- Card Informasi dari EHS -->
    <div class="bg-white py-5 px-3 rounded-xl shadow-md mt-3 flex flex-wrap justify-evenly items-start w-full gap-4 border-l-4 border-blue-500">

        <!-- PIC Name -->
        <div class="flex flex-col items-start min-w-[120px] max-w-[200px]">
            <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                <i class="fas fa-user text-blue-500"></i> <!-- Ikon User -->
                <p>Team Department</p>
            </div>
            <p class="text-gray-900 font-semibold text-sm mt-1 truncate w-full">
                {{ $laporan->picUser->fullname ?? 'Tidak ada PIC' }}
            </p>
        </div>
    
        <!-- Garis Pemisah -->
        <div class="w-[2px] bg-gray-300 h-10 rounded-full hidden md:block"></div>
    
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
            class="flex flex-col items-start min-w-[120px] max-w-[200px]">
            <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                <i class="fas fa-calendar-alt text-green-500"></i> <!-- Ikon Kalender -->
                <p>Date of Finding</p>
            </div>
            <p class="text-gray-900 font-semibold text-sm mt-1 truncate w-full" x-text="formattedTanggalTemuan"></p>
        </div>
        
    </div>
    
    <!-- Area Temuan -->
    <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3 flex flex-col items-start border-l-4 border-gray-700">
        <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
            <i class="fas fa-map-marker-alt text-red-500"></i> <!-- Ikon Lokasi -->
            <p>Finding Area Details</p>
        </div>
        <p class="text-gray-900 font-semibold text-sm mt-1 whitespace-normal break-words overflow-hidden text-ellipsis max-h-[3rem]">
            @if($laporan->area && $laporan->area->nama_area && $laporan->detail_area)
                {{ $laporan->area->nama_area }} - {{ $laporan->detail_area }}
            @else
                <span class="text-gray-400">No area details available</span>
            @endif
        </p>
        
    </div>


    <div x-data="{ 
        rawDueDate: '{{$laporan->due_date}}', 
        rawCompletionDate: '{{$laporan->date_completion}}',
        today: new Date(), 
        formattedDueDate: '', 
        formattedCompletionDate: '',
        daysLeft: 0, 
        overdueDays: 0,
        isApproved: ['approved', 'closed'].includes('{{ $laporan->status_lct }}')
        }"
        x-init="
            let due = new Date(rawDueDate);
            let completion = new Date(rawCompletionDate);
            let now = today;
    
            if (!isNaN(due)) {
                formattedDueDate = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(due);
            } else {
                formattedDueDate = 'Tanggal tidak valid';
            }
    
            if (!isNaN(completion)) {
                formattedCompletionDate = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(completion);
                now = completion;
            } else {
                formattedCompletionDate = '-';
            }
    
            if (!isNaN(due)) {
                let diffTime = due - now;
                daysLeft = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
                if (daysLeft < 0) {
                    overdueDays = Math.abs(daysLeft);
                } else {
                    overdueDays = 0;
                }
            }
        "
        :class="overdueDays > 0 ? 'border-l-4 border-red-500' : 'border-l-4 border-green-500'"
        class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3 flex flex-col sm:flex-row items-stretch justify-between gap-4 sm:gap-2">
        
        <!-- Due Date Section -->
        <div class="flex-1 flex flex-col">
            <div class="flex items-center gap-2 text-gray-500 text-xs tracking-wide">
                <i class="fas fa-calendar-alt"
                    :class="overdueDays > 0 ? 'text-red-500' : 'text-green-500'"></i>
                <p class="font-medium">Due Date</p>
            </div>
            <p class="text-sm font-semibold mt-1"
                :class="overdueDays > 0 ? 'text-red-600' : 'text-gray-900'">
                <span x-text="formattedDueDate"></span>
            </p>
            <!-- Countdown Section -->
            <p x-show="!isApproved" class="text-xs font-medium mt-2"
                :class="overdueDays > 0 ? 'text-red-500' : 'text-green-500'">
                <template x-if="overdueDays > 0">
                    <span>Overdue by <span x-text="overdueDays"></span> days</span>
                </template>
                <template x-if="overdueDays === 0 && daysLeft >= 0">
                    <span><span x-text="daysLeft"></span> days left</span>
                </template>
                <template x-if="formattedDueDate === 'Tanggal tidak valid'">
                    <span class="text-gray-500">Invalid date</span>
                </template>
            </p>
        </div>
    
        <!-- Separator - Horizontal on mobile, Vertical on desktop -->
        <div class="w-full h-[1px] sm:w-[1px] sm:h-12 bg-gray-200 my-2 sm:my-0"></div>
    
        <!-- Completion Date Section -->
        <div class="flex-1 flex flex-col">
            <div class="flex items-center gap-2 text-gray-500 text-xs tracking-wide">
                <i class="fas fa-calendar-check text-blue-500"></i>
                <p class="font-medium">{{$laporan->tingkat_bahaya == 'Low'?'Completion Date':'Completion Date (temporary)'}}</p>
            </div>
            <p class="text-sm font-semibold mt-1 text-gray-900" x-text="formattedCompletionDate"></p>
        </div>
    </div>

                                
    <!-- Card Kategori Temuan -->
    <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-yellow-500 mt-3">
        <div class="flex items-center space-x-2">
            <i class="fa-solid fa-flag text-yellow-500 text-lg"></i>
            <p class="text-gray-500 text-xs">Finding Category</p>
        </div>
        <p class="text-gray-900 font-semibold mt-2 bg-yellow-100 p-2 rounded-lg hover:bg-yellow-200 transition-all duration-200 ease-in-out">{{$laporan->kategori->nama_kategori}}</p>
    </div>

   <!-- Card Tingkat Bahaya -->
    <div x-data="{ level: '{{ $laporan->tingkat_bahaya }}' }" class="bg-white p-4 rounded-lg shadow-md mt-3 border-l-4" :class="level === 'Low' ? 'border-green-500' : level === 'Medium' ? 'border-yellow-500' : 'border-red-500'">
        <div class="flex items-center space-x-2">
            <i :class="{
                'text-green-500 fa-check-circle': level === 'Low',
                'text-yellow-500 fa-exclamation-triangle': level === 'Medium',
                'text-red-500 fa-skull-crossbones': level === 'High'
            }" class="fa-solid text-lg"></i>
            <p class="text-gray-500 text-xs">Hazard Level</p>
        </div>
        <p :class="{
            'bg-green-100 text-green-900 hover:bg-green-200': level === 'Low',
            'bg-yellow-100 text-yellow-900 hover:bg-yellow-200': level === 'Medium',
            'bg-red-100 text-red-900 hover:bg-red-200': level === 'High'
        }" class="text-gray-900 font-semibold mt-2 p-2 rounded-lg transition-all duration-200 ease-in-out">
            <span x-text="level === 'Low' ? 'Low' : level === 'Medium' ? 'Medium' : 'High'"></span>
        </p>
    </div>

    @if($laporan->status_lct === 'revision' ) 
    <!-- Card Laporan Revisi -->
    <div class="bg-white p-4 rounded-lg border border-red-300 mt-3 shadow-md hover:shadow-xl transition-all duration-300 ease-in-out mb-4">
        <div class="flex items-center space-x-2 mb-2">
            <i class="fa-solid fa-exclamation-circle text-red-500 text-lg"></i>
            <p class="text-gray-500 text-xs font-semibold">Report needs revision</p>
        </div>

        @php
            $rejected = $laporan->rejectLaporan->filter(fn($item) => !empty($item->alasan_reject));
        @endphp

        @if ($rejected->isNotEmpty())
            @foreach ($rejected as $reject)
                <div class="bg-red-50 p-3 rounded-lg mb-2">
                    <p class="text-red-700 text-sm"><strong>Reason:</strong> {{ $reject->alasan_reject }}</p>
                    <p class="text-gray-500 text-xs">{{ $reject->created_at->format('d M Y') }}</p>
                </div>
            @endforeach
        @else
            <p class="text-gray-500 text-sm">No rejection reason has been recorded yet</p>
        @endif
    </div>

    <!-- Card Revisi PIC (Hanya tampil jika ada revisi) -->
    @if (!empty($laporan->revisi_pic))
        <div class="bg-white p-4 rounded-lg border border-blue-300 mt-3 shadow-md hover:shadow-xl transition-all duration-300 ease-in-out">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fa-solid fa-edit text-blue-500 text-lg"></i>
                <p class="text-gray-500 text-xs font-semibold">Revision Accepted by PIC</p>
            </div>
            <p class="text-gray-900 mt-2 text-justify leading-relaxed text-sm">
                {{ $laporan->revisi_pic }}
            </p>
        </div>
    @endif
    @else
        <!-- Card Rekomendasi Safety (Jika status_lct bukan revision) -->
        <div class="bg-white p-4 rounded-lg border-l-4 border-green-300 mt-3 shadow-md hover:shadow-xl transition-all duration-300 ease-in-out mb-4">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fa-solid fa-shield-alt text-green-500 text-lg"></i>
                <p class="text-gray-500 text-xs font-semibold">Safety Recommendation</p>
            </div>
            <p class="text-gray-900 mt-2 text-justify leading-relaxed text-sm">
                {{ $laporan->rekomendasi_safety ?? 'Tidak ada rekomendasi safety' }}
            </p>
        </div>
    @endif

    @if ($laporan->status_lct === 'revision' || $laporan->tindakan_perbaikan)
        @if ($tindakan_perbaikan->isNotEmpty())
            <div x-data="{ open: false }" class="mb-4">
                <!-- Card utama untuk perbaikan terbaru dan sebelumnya -->
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-gray-500 relative">
                    <div class="absolute top-2 right-3 text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($tindakan_perbaikan[0]['tanggal'])->format('d M Y') }}
                    </div>   
                    <!-- Tindakan Perbaikan Terbaru -->
                    <div class="mb-4">
                        <p class="text-gray-500 text-xs mb-1">
                            <span class="text-gray-700 text-lg font-semibold">Corrective Action</span> (Latest):
                        </p>
                        <div class="flex items-center gap-1">
                            <p class="text-gray-600 text-sm font-semibold">Action:</p>
                            <p class="text-gray-900 text-sm font-semibold">{{ $tindakan_perbaikan[0]['tindakan'] }}</p>
                        </div>
                    </div>

                    
                    <!-- Gambar perbaikan terbaru -->
                    @if (!empty($tindakan_perbaikan[0]['bukti']))
                        <div class="mt-4">
                            <p class="text-gray-600 text-sm font-semibold mb-2">Corrective Action Images</p>
                            <div class="flex overflow-x-auto gap-2">
                                @foreach ($tindakan_perbaikan[0]['bukti'] as $gambar)
                                    <img src="{{ $gambar }}" 
                                        class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:scale-110 transition-transform"
                                        alt="Bukti Perbaikan"
                                        onclick="openModal('{{ $gambar }}')">
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Tombol dropdown untuk menampilkan perbaikan sebelumnya -->
                    @if (count($tindakan_perbaikan) > 1)
                        <button @click="open = !open" class="mt-4 w-full flex justify-center items-center text-sm text-blue-500 hover:text-blue-700 transition">
                            <span x-text="open ? 'Hide Previous Actions' : 'Show Previous Actions'"></span>
                        </button>
                    @endif

                    <!-- Konten dropdown untuk perbaikan sebelumnya -->
                    <div x-show="open" x-transition class="mt-4 bg-white p-4 rounded-lg shadow-md border-l-4 border-gray-300">
                        @foreach ($tindakan_perbaikan->skip(1) as $index => $entry)
                            <div class="mb-4 relative">
                                <div class="absolute top-2 right-3 text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($entry['tanggal'])->format('d M Y') }}
                                </div>   

                                <!-- Tindakan Perbaikan Sebelumnya -->
                                <div class="mb-2">
                                    <p class="text-gray-500 text-xs mb-1">Corrective Action (Previous #{{ $index + 1 }}):</p>
                                    <div class="flex items-center gap-1">
                                        <p class="text-gray-600 text-sm font-semibold">Action:</p>
                                        <p class="text-gray-900 font-semibold text-sm">{{ $entry['tindakan'] }}</p>
                                    </div>
                                </div>

                                <!-- Gambar perbaikan sebelumnya -->
                                @if (!empty($entry['bukti']))
                                    <div class="mt-4">
                                        <p class="text-gray-700 text-sm font-semibold mb-2">Corrective Action Images</p>
                                        <div class="flex overflow-x-auto gap-2">
                                            @foreach ($entry['bukti'] as $gambar)
                                                <img src="{{ $gambar }}" 
                                                    class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:scale-110 transition-transform"
                                                    alt="Bukti Perbaikan"
                                                    onclick="openModal('{{ $gambar }}')">
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-gray-500">
                <p class="text-gray-600 text-sm font-semibold text-center">No corrective actions found.</p>
            </div>
        @endif
    @endif

     <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
        <p class="text-gray-700 text-lg font-semibold">Non-Conformity Image</p>
        <div class="grid grid-cols-5 gap-2 mt-2">
            @foreach ($bukti_temuan->take(5) as $gambar)
                <img src="{{ $gambar }}" 
                    class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:scale-110 transition-transform"
                    alt="Bukti Temuan"
                    onclick="openModal('{{ $gambar }}')">
            @endforeach
        </div>
    </div>

    <!-- Modal Preview -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black/50 bg-opacity-75 flex items-center justify-center z-60 transition-opacity duration-300">
        <div class="relative bg-white p-1 rounded-lg shadow-lg">
            <!-- Tombol Close -->
            <button id="closeModalBtn"
                class="absolute -top-4 -right-4 bg-gray-800 text-white rounded-full w-10 h-10 flex items-center justify-center text-2xl font-bold shadow-md hover:bg-red-600 transition cursor-pointer"
                onclick="closeModal()">
                &times;
            </button>
            
            <!-- Gambar di Modal -->
            <img id="modalImage" class="w-[600px] h-[500px] object-cover rounded-lg">
        </div>
    </div>
</div>

