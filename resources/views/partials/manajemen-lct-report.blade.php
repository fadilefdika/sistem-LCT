<div class="rounded-lg">

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
            <p class="text-gray-900 font-semibold text-sm">{{$laporan->temuan_ketidaksesuaian}}</p>
        </div>
    </div>

    <!-- Card Gabungan Informasi Temuan -->
    <div class="bg-white p-5 rounded-xl shadow-md mt-4 border-l-4 border-blue-500 space-y-5 w-full">
        <!-- Baris Atas: Tanggal, Area -->
        <div class="grid sm:grid-cols-2 gap-4">
            <!-- Tanggal Temuan -->
            <div x-data="{ 
                    rawTanggalTemuan: '{{ $laporan->tanggal_temuan }}',
                    formattedTanggalTemuan: ''
                }"
                x-init="
                    let date = new Date(rawTanggalTemuan);
                    formattedTanggalTemuan = !isNaN(date) 
                        ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(date)
                        : 'Tanggal tidak valid';
                ">
                <div class="text-xs text-gray-500 flex items-center gap-1">
                    <i class="fas fa-calendar-alt text-green-500"></i> Date of Finding
                </div>
                <p class="text-xs font-semibold text-gray-900 mt-1" x-text="formattedTanggalTemuan"></p>
            </div>

            <!-- Area Temuan -->
            <div>
                <div class="text-xs text-gray-500 flex items-center gap-1">
                    <i class="fas fa-map-marker-alt text-red-500"></i> Finding Area Details
                </div>
                <p class="text-xs font-semibold text-gray-900 mt-1 break-words">
                    @if($laporan->area && $laporan->area->nama_area && $laporan->detail_area)
                        {{ $laporan->area->nama_area }} - {{ $laporan->detail_area }}
                    @else
                        <span class="text-gray-400">No area details available</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Due Date dan Completion Date -->
        <div x-data="{ 
            rawDueDate: '{{$laporan->due_date}}', 
            rawCompletionDate: '{{$laporan->date_completion}}',
            today: new Date(), 
            formattedDueDate: '', 
            formattedCompletionDate: '',
            statusMsg: '', 
            statusColor: '',
            isApproved: ['approved', 'closed'].includes('{{ $laporan->status_lct }}')
            }" 
            x-init="
                let due = new Date(rawDueDate);
                let comp = new Date(rawCompletionDate);
                let now = today;

                formattedDueDate = !isNaN(due) ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(due) : 'Tanggal tidak valid';
                formattedCompletionDate = !isNaN(comp) ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(comp) : '-';

                now = !isNaN(comp) ? comp : today;

                let diff = due - now;
                let daysLeft = Math.ceil(diff / (1000 * 60 * 60 * 24));

                if (!isNaN(due) && !isApproved) {
                    if (daysLeft < 0) {
                        statusMsg = 'Overdue by ' + Math.abs(daysLeft) + ' day' + (Math.abs(daysLeft) > 1 ? 's' : '');
                        statusColor = 'text-red-500';
                    } else if (daysLeft === 0) {
                        statusMsg = 'Due today';
                        statusColor = 'text-yellow-500';
                    } else {
                        statusMsg = 'Due in ' + daysLeft + ' day' + (daysLeft > 1 ? 's' : '');
                        statusColor = 'text-green-500';
                    }
                }
            ">
            <div class="grid sm:grid-cols-2 gap-4">
                <!-- Due Date -->
                <div>
                    <div class="text-xs text-gray-500 flex items-center gap-1">
                        <i class="fas fa-calendar-alt" :class="statusColor || 'text-gray-400'"></i> Due Date
                    </div>
                    <p class="text-xs font-semibold mt-1" :class="statusColor || 'text-gray-900'" x-text="formattedDueDate"></p>
                    <p x-show="!isApproved && formattedDueDate !== 'Tanggal tidak valid'" class="text-[11px] font-medium mt-1" :class="statusColor" x-text="statusMsg"></p>
                </div>

                <!-- Completion Date -->
                <div>
                    <div class="text-xs text-gray-500 flex items-center gap-1">
                        <i class="fas fa-calendar-check text-blue-500"></i> 
                        {{ $laporan->tingkat_bahaya == 'Low' ? 'Completion Date' : 'Completion Date (temporary)' }}
                    </div>
                    <p class="text-xs font-semibold text-gray-900 mt-1" x-text="formattedCompletionDate"></p>
                </div>
            </div>
        </div>

        <!-- Baris Bawah: Kategori dan Tingkat Bahaya -->
        <div class="grid sm:grid-cols-2 gap-4">
            <!-- Kategori -->
            <div>
                <div class="text-xs text-gray-500 flex items-center gap-1">
                    <i class="fa-solid fa-flag text-yellow-500"></i> Finding Category
                </div>
                <p class="text-xs font-semibold mt-1 bg-yellow-100 hover:bg-yellow-200 p-2 rounded-lg transition">{{ $laporan->kategori->nama_kategori }}</p>
            </div>

            <!-- Tingkat Bahaya -->
            <div x-data="{ level: '{{ $laporan->tingkat_bahaya }}' }">
                <div class="text-xs text-gray-500 flex items-center gap-1">
                    <i class="fa-solid text-lg"
                        :class="{
                            'text-green-500 fa-check-circle': level === 'Low',
                            'text-yellow-500 fa-exclamation-triangle': level === 'Medium',
                            'text-red-500 fa-skull-crossbones': level === 'High'
                        }"></i> Hazard Level
                </div>
                <p class="text-xs font-semibold mt-1 p-2 rounded-lg transition"
                    :class="{
                        'bg-green-100 text-green-900 hover:bg-green-200': level === 'Low',
                        'bg-yellow-100 text-yellow-900 hover:bg-yellow-200': level === 'Medium',
                        'bg-red-100 text-red-900 hover:bg-red-200': level === 'High'
                    }">
                    <span x-text="level"></span>
                </p>
            </div>
        </div>
    </div>

        <!-- Card Rekomendasi Safety (Jika status_lct bukan revision) -->
        <div class="bg-white p-4 rounded-lg border-l-4 border-green-300 mt-3 shadow-md hover:shadow-xl transition-all duration-300 ease-in-out mb-4">
            <div class="flex items-center space-x-2 mb-2">
                <i class="fa-solid fa-shield-alt text-green-500 text-lg"></i>
                <p class="text-gray-500 text-xs font-medium">Safety Recommendation</p>
            </div>
            <p class="text-gray-900 mt-2 text-justify leading-relaxed text-xs">
                {{ $laporan->rekomendasi_safety ?? 'Tidak ada rekomendasi safety' }}
            </p>
        </div>


    <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3 mb-4">
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

    @php
        $revisions = $laporan->rejectLaporan->filter(fn($item) => !empty($item->alasan_reject));
        $hasRevisions = $revisions->isNotEmpty();
    @endphp

    @if ($laporan->status_lct === 'revision' || $laporan->tindakan_perbaikan)

        {{-- Corrective Action PERTAMA (selalu tampil) --}}
        @if (!empty($tindakan_perbaikan[0]))
            <div class="bg-white p-4 rounded-lg border border-green-300 mt-3 shadow-md">
                <div class="flex items-center space-x-2 mb-2">
                    <i class="fa-solid fa-wrench text-green-600 text-lg"></i>
                    <p class="text-gray-700 text-xs font-semibold">Initial Corrective Action</p>
                </div>

                <div class="mb-2">
                    <p class="text-sm font-medium text-gray-700 mb-1">Action:</p>
                    <p class="text-gray-900 text-sm">{{ $tindakan_perbaikan[0]['tindakan'] }}</p>
                </div>

                @if (!empty($tindakan_perbaikan[0]['bukti']))
                    <p class="text-sm font-medium text-gray-700 mb-1 mt-3">Images:</p>
                    <div class="flex overflow-x-auto gap-2">
                        @foreach ($tindakan_perbaikan[0]['bukti'] as $img)
                            <img src="{{ $img }}" class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:scale-110 transition" onclick="openModal('{{ $img }}')" alt="Proof">
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- Tampilkan tindakan_perbaikan berikutnya jika ada revisi --}}
        @if ($hasRevisions && count($tindakan_perbaikan) > 1)
            <div x-data="{ openIndex: null }" class="bg-white p-6 rounded-xl border border-red-200 mt-6 shadow-lg space-y-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-exclamation-circle text-red-500 text-xl"></i>
                    <p class="text-red-600 font-semibold text-sm">This report has been revised</p>
                </div>

                <table class="w-full text-sm text-left table-fixed border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="py-3 px-3 w-40">Date</th>
                            <th class="py-3 px-3">Revision Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $combined = $revisions->values()->map(function ($rev, $index) use ($tindakan_perbaikan) {
                                return [
                                    'rev' => $rev,
                                    'tindakan' => $tindakan_perbaikan[$index + 1] ?? null
                                ];
                            })->reverse()->values();
                        @endphp

                        @foreach ($combined as $i => $item)
                            @php
                                $rev = $item['rev'];
                                $tindakan = $item['tindakan'];
                            @endphp

                            <tr 
                                @click="openIndex === {{ $i }} ? openIndex = null : openIndex = {{ $i }}" 
                                class="cursor-pointer border-b hover:bg-gray-50 transition"
                            >
                                <td class="py-2 px-3 text-gray-500 text-xs whitespace-nowrap align-top">
                                    {{ $rev->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                </td>
                                <td class="py-2 px-3 align-top">
                                    <div class="flex flex-col sm:flex-row sm:justify-between items-start gap-2 w-full">
                                        <span class="text-gray-800 text-xs leading-snug break-words w-full">
                                            {{ $rev->alasan_reject }}
                                        </span>
                                        <svg :class="{ 'rotate-180': openIndex === {{ $i }} }"
                                            class="w-4 h-4 text-gray-400 transition-transform mt-1 sm:mt-0 self-end sm:self-center"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </td>                                                
                            </tr>

                            <tr x-show="openIndex === {{ $i }}" x-transition>
                                <td colspan="2" class="bg-gray-50 px-6 py-4">
                                    @if ($tindakan)
                                        <p class="text-gray-700 text-xs font-semibold mb-1">Corrective Action</p>
                                        <p class="text-gray-800 text-[11px] mb-3 leading-snug break-words text-justify">
                                            {{ $tindakan['tindakan'] }}
                                        </p>

                                        @if (!empty($tindakan['bukti']))
                                            <p class="text-gray-700 text-xs font-semibold mb-2">Images</p>
                                            <div class="flex flex-wrap gap-3">
                                                @foreach ($tindakan['bukti'] as $img)
                                                    <img src="{{ $img }}" 
                                                        onclick="openModal('{{ $img }}')" 
                                                        class="w-24 h-24 object-cover rounded-lg shadow-sm cursor-pointer hover:scale-105 transition-transform duration-150" 
                                                        alt="Proof Image">
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-red-500 text-xs font-medium">No corrective action submitted for this revision.</p>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    @endif


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

