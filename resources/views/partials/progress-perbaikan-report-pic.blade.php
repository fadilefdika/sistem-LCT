<div x-data="{
        tab: window.innerWidth >= 768 ? 'laporan' : 'laporan',
        isDesktop: window.innerWidth >= 768
    }" x-init="window.addEventListener('resize', () => {
        isDesktop = window.innerWidth >= 768;
    })" class="flex flex-col w-full h-full">

    <div class="md:hidden flex justify-around bg-white shadow-sm border-b">
        <button @click="tab = 'laporan'" :class="tab === 'laporan' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'" class="px-4 py-2 text-sm font-semibold">
            Report
        </button>
        <button @click="tab = 'form'" :class="tab === 'form' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'" class="px-4 py-2 text-sm font-semibold">
            Card Approval
        </button>
    </div>

    <div class="grid md:grid-cols-2 justify-center w-full h-full">

        <!-- Laporan Card -->
        <div 
            x-show="tab === 'laporan' || isDesktop" 
            class="relative max-w-full bg-[#F3F4F6] overflow-hidden h-full p-1 pb-20 max-h-[calc(100vh)] overflow-y-auto"
            >
            <!-- Card Laporan dari Pelapor -->
            <div class="relative max-w-full bg-[#F3F4F6] overflow-hidden h-full p-1 pb-24 max-h-[calc(100vh)] overflow-y-auto [&::-webkit-scrollbar]:w-1
                [&::-webkit-scrollbar-track]:rounded-full
                [&::-webkit-scrollbar-track]:bg-gray-100
                [&::-webkit-scrollbar-thumb]:rounded-full
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                
                <div class="max-h-min rounded-lg space-y-3">
                    <!-- Card Laporan -->
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

                    <div class="bg-white p-5 rounded-xl shadow-md border">
                        <div class="flex items-center justify-between">
                            <!-- Header -->
                            <h5 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                📝 Report from PIC
                            </h5>

                            <!-- Badge Tingkat Bahaya -->
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $badge['color'] }} flex items-center gap-1">
                                {!! $badge['icon'] !!} {{ $level }}
                            </span>
                        </div>

                        <!-- Garis Pemisah -->
                        <div class="w-full h-[2px] bg-gray-200 my-3"></div>

                        <!-- Isi Laporan -->
                        <div class="space-y-1">
                            <p class="text-gray-500 text-xs">Non-Conformity Finding</p>
                            <p class="text-gray-900 font-semibold text-lg">{{$laporan->temuan_ketidaksesuaian}}</p>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-md border-l-4 border-blue-500">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            {{-- 2. Due Date & Deadline Status --}}
                            @php
                                $dueDate = $laporan->due_date ? \Carbon\Carbon::parse($laporan->due_date) : null;
                                $now = \Carbon\Carbon::now();
                                $diffInHours = $dueDate ? $now->diffInHours($dueDate, false) : 0;
                                $diffInDays = $dueDate ? floor($diffInHours / 24) : 0;
                                $remainingHours = $dueDate ? $diffInHours % 24 : 0;
                            @endphp
                            <div>
                                <div class="flex items-center gap-2 text-gray-500 text-xs font-semibold">
                                    <i class="fas fa-hourglass-half text-blue-500"></i>
                                    <span>Due Date</span>
                                </div>
                                <p class="text-gray-900 font-semibold text-sm mt-1">
                                    {{ $dueDate ? $dueDate->translatedFormat('d F Y') : '-' }}
                                </p>
                                <p class="text-xs mt-1 font-semibold
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
                                </p>
                            </div>
                    
                            {{-- 3. Completion Date --}}
                            <div>
                                <div class="flex items-center gap-2 text-gray-500 text-xs font-semibold">
                                    <i class="fas fa-calendar-check
                                        {{ $laporan->date_completion == null ? 'text-red-500' : 'text-green-500' }}"></i>
                                    <span>Completion Date</span>
                                </div>
                                @if($laporan->date_completion == null)
                                    <p class="text-red-500 font-semibold text-sm mt-1">Not Completed Yet</p>
                                @else
                                    @php
                                        $completionDate = \Carbon\Carbon::parse($laporan->date_completion);
                                        $isLate = $dueDate && $completionDate->greaterThan($dueDate);
                                    @endphp
                                    <p class="text-gray-900 font-semibold text-sm mt-1">
                                        {{ $completionDate->translatedFormat('d F Y') }}
                                    </p>
                                    @if($isLate)
                                        <p class="text-xs text-red-500 font-medium mt-1">
                                            ⚠️ Overdue by {{ $completionDate->diffInDays($dueDate) }} day(s)
                                        </p>
                                    @endif
                                @endif
                            </div>
                    
                            {{-- 1. PIC --}}
                            <div>
                                <div class="flex items-center gap-2 text-gray-500 text-xs font-semibold">
                                    <i class="fas fa-user text-blue-500"></i>
                                    <span>PIC</span>
                                </div>
                                <p class="text-gray-900 font-semibold text-sm mt-1">
                                    @if($laporan->picUser && $laporan->picUser->fullname)
                                        {{ $laporan->picUser->fullname }}
                                    @else
                                        <span class="text-gray-400">No PIC available</span>
                                    @endif
                                </p>
                            </div>

                            {{-- 4. Area Temuan --}}
                            <div>
                                <div class="flex items-center gap-2 text-gray-500 text-xs font-semibold">
                                    <i class="fas fa-map-marker-alt text-red-500"></i>
                                    <span>Finding Area</span>
                                </div>
                                <p class="text-gray-900 font-semibold text-sm mt-1">
                                    @if($laporan->area && $laporan->area->nama_area && $laporan->detail_area)
                                        {{ $laporan->area->nama_area }} - {{ $laporan->detail_area }}
                                    @else
                                        <span class="text-gray-400">No area details available</span>
                                    @endif
                                </p>
                            </div>
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
                                    <p class="text-gray-700 text-xs font-semibold">Temporary Corective Action</p>
                                </div>

                                <div class="mb-2">
                                    <p class="text-xs font-medium text-gray-700 mb-1">Action:</p>
                                    <p class="text-gray-900 font-medium text-sm">{{ $tindakan_perbaikan[0]['tindakan'] }}</p>
                                </div>

                                @if (!empty($tindakan_perbaikan[0]['bukti']))
                                    <p class="text-xs font-medium text-gray-700 mb-1 mt-3">Images:</p>
                                    <div class="flex overflow-x-auto gap-2">
                                        @foreach ($tindakan_perbaikan[0]['bukti'] as $img)
                                            <img src="{{ $img }}" loading="lazy" class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:scale-110 transition" onclick="openModal('{{ $img }}')" alt="Proof">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Tampilkan tindakan_perbaikan berikutnya jika ada revisi --}}
                        @if ($hasRevisions)
                            <div x-data="{ openIndex: null }" class="bg-white p-6 rounded-xl border border-red-200 mt-4 shadow-lg space-y-4">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-exclamation-circle text-red-500 text-xl"></i>
                                    <p class="text-red-600 font-semibold text-sm">Revised Report</p>
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
                                                <td class="py-2 px-3 text-gray-500 text-[11px] whitespace-nowrap align-top">
                                                    {{ $rev->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                                </td>
                                                <td class="py-2 px-3 align-top">
                                                    <div class="flex flex-col sm:flex-row sm:justify-between items-start gap-2 w-full">
                                                        <span class="{{ $tindakan ? 'text-gray-800' : 'text-red-500' }} text-xs leading-snug break-words w-full">
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
                                                                        loading="lazy"
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
                </div>
            </div>
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
            $notAllowed = in_array($roleName, ['user', 'manajer', 'pic']);
            $tingkatBahaya = $laporan->tingkat_bahaya ?? '';
            $statusLct = $laporan->status_lct ?? '';
            $approvedByEhs = $laporan->approved_temporary_by_ehs ?? '';
        @endphp
    

        <!-- Form Card -->
        <div 
            x-show="tab === 'form' || isDesktop" 
            class="relative max-w-full bg-gray-100 overflow-hidden p-1 pb-32 px-2 max-h-[calc(100vh)] overflow-y-auto"
            >
            <!-- Form Laporan Temuan -->
            <div class="relative max-w-full bg-gray-100 overflow-hidden p-1 pb-32 px-2 max-h-[calc(100vh)] overflow-y-auto 
                [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100
                [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">

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
                                    @if(
                                        ($tingkatBahaya === 'low' && $statusLct !== 'closed') ||
                                        (in_array($tingkatBahaya, ['medium', 'high']) && $approvedByEhs !== 'approved')
                                    )
                                        <p class="text-gray-700 font-semibold">⚠️ Report not yet approved by EHS</p>
                                    @else
                                        <p class="text-gray-700 font-semibold">✅ Report already approved by EHS</p>
                                    @endif
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
                                            History
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
</div>