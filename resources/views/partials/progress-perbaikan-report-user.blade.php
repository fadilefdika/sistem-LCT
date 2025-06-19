<div class="w-full bg-[#F3F4F6] max-h-[calc(100vh)] pb-28 overflow-y-auto 
    [&::-webkit-scrollbar]:w-1
    [&::-webkit-scrollbar-track]:rounded-full
    [&::-webkit-scrollbar-track]:bg-gray-100
    [&::-webkit-scrollbar-thumb]:rounded-full
    [&::-webkit-scrollbar-thumb]:bg-gray-300
    dark:[&::-webkit-scrollbar-track]:bg-neutral-700
    dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">

    <div class="container mx-auto px-3 pt-3 pb-10">
        <div class="flex flex-col gap-3">
            @php
                        // Warna dan ikon berdasarkan tingkat bahaya
                        $dangerLevels = [
                            'Low' => ['color' => 'bg-green-100 text-green-700', 'icon' => '🟢'],
                            'Medium' => ['color' => 'bg-orange-100 text-orange-700', 'icon' => '🟠'],
                            'High' => ['color' => 'bg-red-100 text-red-700', 'icon' => '🔴'],
                        ];
                        $level = $laporan->tingkat_bahaya;
                        $badge = $dangerLevels[$level] ?? $dangerLevels['Low']; // Default ke "Low" jika data tidak valid
                    @endphp
            <!-- Report from Reporter -->
            <div class="md:col-span-2 bg-white p-4 md:p-6 rounded-xl shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <!-- Header -->
                    <h5 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                        📝 Finding Report
                    </h5>

                    <!-- Badge Tingkat Bahaya -->
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $badge['color'] }} flex items-center gap-1">
                        {!! $badge['icon'] !!} {{ $level }}
                    </span>
                </div>
                <div class="w-full h-[1.5px] bg-gray-200 my-3"></div>
                <p class="text-gray-500 text-xs md:text-sm">Non-Conformity Finding</p>
                <p class="text-gray-900 font-semibold text-sm md:text-base mt-1 leading-snug">
                    {{ $laporan->temuan_ketidaksesuaian }}
                </p>
            </div>
        
            <div class="bg-white p-6 rounded-2xl shadow-md border-l-4 border-blue-500 hover:shadow-lg transition w-full">
                <h3 class="text-sm font-semibold text-blue-600 flex items-center gap-2 mb-6">
                    <i class="fas fa-info-circle text-base"></i> Report From Finder
                </h3>
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    {{-- Informasi Teks --}}
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                            <!-- Finder Name -->
                            <div>
                                <p class="text-gray-500 text-xs flex items-center gap-1 mb-1">
                                    <i class="fas fa-user text-blue-500"></i> Finding Owner
                                </p>
                                <p class="text-gray-900 font-medium text-xs">{{ $laporan->user->fullname }}</p>
                            </div>
            
                            <!-- Finding Date -->
                            <div>
                                <p class="text-gray-500 text-xs flex items-center gap-1 mb-1">
                                    <i class="fas fa-calendar-alt text-green-500"></i> Finding Date
                                </p>
                                <p class="text-gray-900 font-medium text-xs">
                                    {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->locale('en')->translatedFormat('l, d F Y') }}
                                </p>
                            </div>
            
                            <!-- Finding Area -->
                            <div>
                                <p class="text-gray-500 text-xs flex items-center gap-1 mb-1">
                                    <i class="fas fa-map-marker-alt text-red-500"></i> Finding Area
                                </p>
                                <p class="text-gray-900 font-medium text-xs break-words">
                                    @if($laporan->area && $laporan->area->nama_area && $laporan->detail_area)
                                        {{ $laporan->area->nama_area }} - {{ $laporan->detail_area }}
                                    @else
                                        <span class="text-gray-400">No area details available</span>
                                    @endif
                                </p>
                            </div>
            
                            <!-- Finding Category -->
                            <div>
                                <p class="text-gray-500 text-xs flex items-center gap-1 mb-1">
                                    <i class="fa-solid fa-flag text-yellow-500"></i> Finding Category
                                </p>
                                <p class="text-gray-900 font-medium text-xs">
                                    {{ $laporan->kategori->nama_kategori ?? '-' }}
                                </p>
                            </div>
                        </div>
            
                        <!-- Safety Recommendation -->
                        <div>
                            <p class="text-gray-500 text-xs flex items-center gap-1">
                                <i class="fa-solid fa-shield-alt text-green-500"></i> Safety Recommendation
                            </p>
                            <p class="text-gray-900 mt-2 text-justify text-sm leading-relaxed">
                                {{ $laporan->rekomendasi_safety }}
                            </p>
                        </div>
                    </div>
            
                    {{-- Gambar Temuan --}}
                    <div>
                        <p class="text-gray-700 text-base font-semibold mb-4 text-center md:text-left">
                            Non-Conformity Image
                        </p>
                        <div class="flex flex-wrap md:justify-start justify-center gap-3">
                            @foreach ($bukti_temuan->take(5) as $gambar)
                                <img src="{{ $gambar }}" 
                                    loading="lazy"
                                    alt="Finding Image"
                                    onclick="openModal('{{ $gambar }}')"
                                    class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-lg cursor-pointer hover:scale-110 transition-transform duration-200 border border-gray-200 shadow-sm" />
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            
            <div class="bg-white p-6 rounded-2xl shadow-md border-l-4 border-blue-500 hover:shadow-lg transition-all duration-300 w-full">
                <h3 class="text-sm font-semibold text-blue-600 flex items-center gap-2 mb-6">
                    <i class="fas fa-info-circle text-base"></i> Report From PIC
                </h3>
            
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @php
                        $dueDate = $laporan->due_date ? \Carbon\Carbon::parse($laporan->due_date) : null;
                        $now = \Carbon\Carbon::now();
                        $diffInHours = $dueDate ? $now->diffInHours($dueDate, false) : 0;
                        $diffInDays = $dueDate ? floor($diffInHours / 24) : 0;
                        $remainingHours = $dueDate ? $diffInHours % 24 : 0;
                    @endphp
                    {{-- Informasi Kiri --}}
                    <div class="space-y-6">
                        {{-- Due Date --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                        
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                                    <i class="fas fa-hourglass-half text-blue-500"></i> Due Date
                                </div>
                                <div class="text-xs font-semibold text-gray-900">
                                    {{ $dueDate ? $dueDate->translatedFormat('d F Y') : '-' }}
                                </div>
                                <div class="text-xs font-semibold mt-1
                                    {{ in_array($laporan->status_lct, ['approved', 'closed']) ? 'text-green-500' :
                                        ($dueDate && $diffInDays < 0 ? 'text-red-500' :
                                        ($dueDate && $diffInDays === 0 && $remainingHours < 24 ? 'text-yellow-500' : 'text-green-500')) }}">
                                    @if ($laporan->status_lct == 'approved')
                                        ✅ Completed
                                    @elseif ($dueDate && $diffInDays < 0)
                                        ⚠️ Overdue
                                    @elseif ($dueDate && $diffInDays === 0 && $remainingHours < 24)
                                        ⏳ Deadline Approaching
                                    @elseif ($dueDate)
                                        ✅ Within Deadline
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                
                            {{-- Completion Date --}}
                            <div class="">
                                <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                                    <i class="fas fa-calendar-check {{ $laporan->date_completion == null ? 'text-red-500' : 'text-green-500' }}"></i> Completion Date
                                </div>
                                @if($laporan->date_completion == null)
                                    <div class="text-xs font-semibold text-red-500">Not Completed Yet</div>
                                @else
                                    @php
                                        $completionDate = \Carbon\Carbon::parse($laporan->date_completion);
                                        $isLate = $dueDate && $completionDate->greaterThan($dueDate);
                                    @endphp
                                    <div class="text-xs font-semibold text-gray-900">
                                        {{ $completionDate->translatedFormat('d F Y') }}
                                    </div>
                                    @if($isLate)
                                        <div class="text-xs text-red-500 font-medium">
                                            ⚠️ Overdue by {{ $completionDate->diffInDays($dueDate) }} day(s)
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- PIC --}}
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                                <i class="fas fa-user text-blue-500"></i> PIC
                            </div>
                            <div class="text-xs font-semibold text-gray-900">
                                {{ $laporan->picUser->fullname ?? 'No PIC available' }}
                            </div>
                        </div>
                    </div>
            
                    {{-- Initial Corrective Action --}}
                    @if (!empty($tindakan_perbaikan[0]))
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-xs font-semibold text-green-600">
                            <i class="fas fa-wrench text-lg"></i> Initial Corrective Action
                        </div>
                        <div>
                            <div class="text-xs text-gray-700 font-medium mb-1">Action:</div>
                            <p class="text-sm text-gray-900 font-medium">{{ $tindakan_perbaikan[0]['tindakan'] }}</p>
                        </div>
                        @if (!empty($tindakan_perbaikan[0]['bukti']))
                        <div>
                            <div class="text-xs text-gray-700 font-medium mb-1">Images:</div>
                            <div class="flex flex-wrap md:justify-start justify-center gap-3">
                                @foreach ($tindakan_perbaikan[0]['bukti'] as $img)
                                    <img src="{{ $img }}" loading="lazy" class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-lg cursor-pointer hover:scale-110 transition-transform duration-200 border border-gray-200 shadow-sm" onclick="openModal('{{ $img }}')" alt="Proof">
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            
                {{-- Revised Reports --}}
                @if ($laporan->status_lct === 'revision' || $laporan->tindakan_perbaikan)
                    @php
                        $revisions = $laporan->rejectLaporan->filter(fn($item) => !empty($item->alasan_reject));
                        $hasRevisions = $revisions->isNotEmpty();
                    @endphp
                    @if ($hasRevisions)
                    <div x-data="{ openIndex: null }" class="mt-8 bg-gray-50 p-5 rounded-xl border border-gray-200 space-y-4">
                        <div class="flex items-center gap-2 text-sm font-semibold text-red-600">
                            <i class="fas fa-exclamation-circle text-xl"></i> Revised Report
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left table-auto">
                                <thead class="bg-gray-100 text-gray-600 text-[11px] uppercase">
                                    <tr>
                                        <th class="py-2 px-3 min-w-[140px] sm:w-40 text-xs">Date</th>
                                        <th class="py-2 px-3 text-xs">Revision Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $combined = $revisions->values()->map(function ($rev, $index) use ($tindakan_perbaikan) {
                                            return ['rev' => $rev, 'tindakan' => $tindakan_perbaikan[$index + 1] ?? null];
                                        })->reverse()->values();
                                    @endphp

                                    @foreach ($combined as $i => $item)
                                        @php
                                            $rev = $item['rev'];
                                            $tindakan = $item['tindakan'];
                                        @endphp

                                        <tr 
                                            @if ($tindakan) 
                                                @click="openIndex === {{ $i }} ? openIndex = null : openIndex = {{ $i }}" 
                                                class="cursor-pointer hover:bg-gray-100 transition-all border-b"
                                            @else 
                                                class="bg-gray-50 border-b cursor-not-allowed opacity-80"
                                            @endif
                                        >
                                            <td class="py-2 px-3 text-[11px] text-gray-600 leading-snug">
                                                <!-- Desktop: satu baris -->
                                                <span class="hidden sm:inline whitespace-nowrap">
                                                    {{ $rev->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                                </span>
                                                <!-- Mobile: dua baris -->
                                                <div class="block sm:hidden">
                                                    <span class="block">{{ $rev->created_at->timezone('Asia/Jakarta')->format('d M Y') }}</span>
                                                    <span class="block">{{ $rev->created_at->timezone('Asia/Jakarta')->format('H:i') }} WIB</span>
                                                </div>
                                            </td>
                                            <td class="py-2 px-3">
                                                <div class="flex justify-between items-start gap-2">
                                                    <span class="text-xs {{ $tindakan ? 'text-gray-800' : 'text-red-500' }} leading-snug text-justify break-words w-full">
                                                        {{ $rev->alasan_reject }}
                                                    </span>
                                                    @if ($tindakan)
                                                        <svg :class="{ 'rotate-180': openIndex === {{ $i }} }"
                                                            class="w-4 h-4 text-gray-400 transition-transform mt-0.5"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>

                                        @if ($tindakan)
                                            <tr x-show="openIndex === {{ $i }}" x-transition>
                                                <td colspan="2" class="bg-gray-50 px-6 py-4">
                                                    <p class="text-gray-700 text-xs font-semibold mb-1">Corrective Action</p>
                                                    <p class="text-[11px] text-gray-800 mb-3 leading-snug text-justify">
                                                        {{ $tindakan['tindakan'] }}
                                                    </p>
                                                    @if (!empty($tindakan['bukti']))
                                                        <p class="text-xs text-gray-700 font-medium mb-2">Images:</p>
                                                        <div class="flex flex-wrap gap-3">
                                                            @foreach ($tindakan['bukti'] as $img)
                                                                <img src="{{ $img }}"
                                                                    loading="lazy"
                                                                    onclick="openModal('{{ $img }}')"
                                                                    class="w-24 h-24 object-cover rounded-lg shadow-sm cursor-pointer hover:scale-105 transition-transform duration-150"
                                                                    alt="Proof Image">
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

        
                @endif
            </div>

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
            
            <div class="bg-white p-6 rounded-lg shadow-lg w-full mx-auto">
                    
                <!-- Header -->
                <h5 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    @if($laporan->status_lct === 'waiting_approval_temporary' || $laporan->status_lct === 'temporary_revision')
                        LCT Corrective Report Approval (Temporary)
                    @else
                        LCT Corrective Report Approval
                    @endif
                </h5>

                <!-- Garis Pemisah -->
                <div class="w-full h-[2px] bg-gray-200 my-3"></div>

                <p class="text-gray-700 font-semibold mb-2">Approve this report?</p>

                <div x-data="{ revision: false, reason: '', closed: false }">
                    @if($laporan->status_lct !== 'closed')   

                        @if($notAllowed)
                            <div class="mt-3 p-4 bg-gray-200 border border-gray-400 rounded-lg">
                                <p class="text-gray-700 font-semibold">⚠️ You cannot take action on this report.</p>
                            </div>

                        @elseif(in_array($laporan->tingkat_bahaya, ['Medium', 'High']))

                            {{-- Case: Waiting approval temporary --}}
                            @if($laporan->approved_temporary_by_ehs == 'revise')
                                <div class="mt-3 p-4 bg-yellow-100 border border-yellow-400 rounded-lg">
                                    <p class="text-yellow-800 font-semibold">⚠️ Repairs still in progress by PIC.</p>
                                </div>

                            {{-- Case: Temporary revision --}}
                            @elseif($laporan->approved_temporary_by_ehs == 'pending')
                                <div class="flex space-x-4">
                                    <!-- Approve Button -->
                                    <form action="{{ route('ehs.reporting.approve', $laporan->id_laporan_lct) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                            class="px-5 py-2.5 bg-emerald-600 text-white font-semibold rounded-lg shadow-md hover:bg-emerald-700">
                                            Approve
                                        </button>
                                    </form>
                                
                                    <!-- Revision Button -->
                                    <button type="button" @click="revision = true"
                                        class="px-5 py-2.5 bg-rose-600 text-white font-semibold rounded-lg shadow-md hover:bg-rose-700">
                                        Revision
                                    </button>
                                </div>
                                
                                <!-- Alasan Penolakan -->
                                <div x-show="revision" x-data="{ reason: '', maxLength: 255 }" class="mt-4">
                                    <form 
                                        :class="{ 'opacity-50 pointer-events-none': reason.length > maxLength }"
                                        @submit="if (reason.length > maxLength) $event.preventDefault(); revision = reason.length <= maxLength ? false : true;" 
                                        action="{{ route('ehs.reporting.reject', $laporan->id_laporan_lct) }}" 
                                        method="POST"
                                    >
                                        @csrf
                                        <label class="block text-gray-700 font-semibold">Revision Reason:</label>
                                        <textarea
                                            x-model="reason"
                                            name="alasan_reject"
                                            rows="3"
                                            class="w-full mt-2 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                            :class="{ 'border-red-500': reason.length > maxLength }"
                                        ></textarea>

                                        <!-- Karakter Counter dan Peringatan -->
                                        <div class="mt-1 text-sm" :class="reason.length > maxLength ? 'text-red-500' : 'text-gray-500'">
                                            <span x-text="reason.length"></span>/255 characters
                                            <template x-if="reason.length > maxLength">
                                                <span class="ml-2 font-semibold">Too many characters!</span>
                                            </template>
                                        </div>

                                        <div class="flex mt-3 space-x-2">
                                            <button 
                                                type="submit" 
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50" 
                                                :disabled="reason.length > maxLength"
                                            >
                                                Send Revision
                                            </button>
                                            <button 
                                                type="button" 
                                                @click="revision = false" 
                                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                        
                            {{-- Case: Approved dan sudah selesai --}}
                            @elseif($laporan->approved_temporary_by_ehs == 'approved')
                                <div class="mt-6 p-4 bg-green-100 border border-green-400 rounded-lg">
                                    <p class="text-green-800 font-semibold">✅ The report has been approved and completed by PIC.</p>
                                </div>
                            @endif
                        
                        @elseif($laporan->tingkat_bahaya === 'Low' && $laporan->status_lct === 'waiting_approval')
                            <div class="flex space-x-4">
                                <!-- Approve Button -->
                                <form action="{{ route('ehs.reporting.close', $laporan->id_laporan_lct) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                        class="px-5 py-2.5 bg-emerald-600 text-white font-semibold rounded-lg shadow-md hover:bg-emerald-700">
                                        Approve
                                    </button>
                                </form>
                            
                                <!-- Revision Button -->
                                <button type="button" @click="revision = true"
                                    class="px-5 py-2.5 bg-rose-600 text-white font-semibold rounded-lg shadow-md hover:bg-rose-700">
                                    Revision
                                </button>
                            </div>
                            
                            <!-- Alasan Penolakan -->
                            <div x-show="revision" x-data="{ reason: '', maxLength: 255 }" class="mt-4">
                                <form 
                                    :class="{ 'opacity-50 pointer-events-none': reason.length > maxLength }"
                                    @submit="if (reason.length > maxLength) $event.preventDefault(); revision = reason.length <= maxLength ? false : true;" 
                                    action="{{ route('ehs.reporting.reject', $laporan->id_laporan_lct) }}" 
                                    method="POST"
                                >
                                    @csrf
                                    <label class="block text-gray-700 font-semibold">Revision Reason:</label>
                                    <textarea
                                        x-model="reason"
                                        name="alasan_reject"
                                        rows="3"
                                        class="w-full mt-2 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                        :class="{ 'border-red-500': reason.length > maxLength }"
                                    ></textarea>

                                    <!-- Karakter Counter dan Peringatan -->
                                    <div class="mt-1 text-sm" :class="reason.length > maxLength ? 'text-red-500' : 'text-gray-500'">
                                        <span x-text="reason.length"></span>/255 characters
                                        <template x-if="reason.length > maxLength">
                                            <span class="ml-2 font-semibold">Too many characters!</span>
                                        </template>
                                    </div>

                                    <div class="flex mt-3 space-x-2">
                                        <button 
                                            type="submit" 
                                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50" 
                                            :disabled="reason.length > maxLength"
                                        >
                                            Send Revision
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="revision = false" 
                                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @else
                    
                        <div class="mt-3 p-4 bg-gray-200 border border-gray-400 rounded-lg">
                            <p class="text-gray-700 font-semibold">🔒 The report has been closed.</p>
                        </div>
                    @endif


                       <!-- Tombol History -->
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
                    
                        <!-- History Card -->
                        <div class="bg-white rounded-lg shadow-md p-4 mt-4 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-800">Corrective Action History</h2>
                                    <p class="text-sm text-gray-500">View the detailed progress and corrective actions taken for this case.</p>
                                </div>
                                <a href="{{ route($routeName, $laporan->id_laporan_lct) }}">
                                    <button class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50 cursor-pointer">
                                        <i class="fas fa-history mr-2"></i>View History
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    

                    <!-- Notifikasi Laporan Ditutup -->
                    <div x-show="closed" class="mt-3 p-4 bg-gray-200 border border-gray-400 rounded-lg">
                        <p class="text-gray-700 font-semibold">🔒 The report has been closed.</p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
