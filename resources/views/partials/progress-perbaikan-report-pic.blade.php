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
                                📝 Report from the PIC
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

                    <div class="bg-white p-5 rounded-xl shadow-md border-l-4 border-blue-500">

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-2 items-center">
                            
                            <!-- PIC Name -->
                            <div class="flex flex-col">
                                <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                                    <i class="fas fa-user text-blue-500"></i>
                                    <p>PIC</p>
                                </div>
                                <p class="text-gray-900 font-semibold text-sm mt-1">
                                    @if($laporan->picUser && $laporan->picUser->fullname)
                                        {{ $laporan->picUser->fullname }}
                                    @else
                                        <span class="text-gray-400">No PIC available</span>
                                    @endif
                                </p>                                
                            </div>

                            <!-- Garis Pemisah (Hanya Muncul di Layar Lebar) -->
                            <div class="hidden sm:flex justify-center">
                                <div class="w-[2px] bg-gray-300 h-10 rounded-full"></div>
                            </div>

                             <!-- Card Informasi Pelapor -->
                             @php
                                $dueDate = $laporan->due_date ? \Carbon\Carbon::parse($laporan->due_date) : null; // Pastikan hanya parsing jika due_date tidak NULL
                                $now = \Carbon\Carbon::now();
                                $diffInHours = $dueDate ? $now->diffInHours($dueDate, false) : 0;
                                $diffInDays = $dueDate ? floor($diffInHours / 24) : 0;
                                $remainingHours = $dueDate ? $diffInHours % 24 : 0;
                            
                                $borderClass = 'border-green-500';
                                if ($dueDate && $diffInDays < 0) {
                                    $borderClass = 'border-red-500';
                                } elseif ($dueDate && $diffInDays === 0 && $remainingHours < 24) {
                                    $borderClass = 'border-yellow-500';
                                }
                            @endphp

                            <!-- Due Date -->
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2 text-gray-600 text-xs font-medium">
                                    <i class="fas fa-hourglass-half text-blue-500"></i>
                                    <p>Due Date</p>
                                </div>
                                <p class="text-gray-900 font-semibold text-sm mt-1">
                                    <!-- Hanya tampilkan tanggal jika $dueDate ada -->
                                    {{ $dueDate ? $dueDate->translatedFormat('d F Y') : '-' }}
                                </p>                                
                                <!-- Status -->
                                <p class="text-xs mt-1 font-semibold 
                                    {{ in_array($laporan->status_lct, ['approved', 'closed']) ? 'text-green-500' : 
                                    ($dueDate && $diffInDays < 0 ? 'text-red-500' : 
                                    ($dueDate && $diffInDays === 0 && $remainingHours < 24 ? 'text-yellow-500' : 'text-green-500')) }}">
                                    @if ($laporan->status_lct == 'approved'))
                                        ✅ Completed
                                    @elseif ($dueDate && $diffInDays < 0)
                                        ⚠️ Overdue
                                    @elseif ($dueDate && $diffInDays === 0 && $remainingHours < 24)
                                        ⏳ Deadline Approaching
                                    @elseif ($dueDate) <!-- Pastikan $dueDate ada -->
                                        ✅ Within Deadline
                                    @else
                                        <p></p> <!-- Jika tidak ada due_date, kosongkan -->
                                    @endif
                                </p>

                            </div>

                        </div>
                    </div>
                
                    <!-- Card Tanggal Selesai -->
                    <div class="bg-white p-4 rounded-lg shadow-md border-l-4 
                        {{ $laporan->date_completion == null ? 'border-red-500' : 'border-green-500' }}">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2 text-gray-600 text-xs font-medium">
                                <i class="fas fa-calendar-alt 
                                    {{ $laporan->date_completion == null ? 'text-red-500' : 'text-green-500' }}"></i>
                                <p>Completion Date</p>
                            </div>

                            @if($laporan->date_completion == null)
                                <!-- Jika belum selesai -->
                                <p class="text-red-500 font-semibold text-sm mt-1">Not Completed Yet</p>
                            @else
                                @php
                                    $dueDate = \Carbon\Carbon::parse($laporan->due_date); // Ambil due date
                                    $completionDate = \Carbon\Carbon::parse($laporan->date_completion); // Ambil tanggal selesai
                                    $isLate = $completionDate->greaterThan($dueDate); // Cek apakah terlambat
                                @endphp

                                <!-- Jika sudah selesai -->
                                <p class="text-gray-900 font-semibold text-sm mt-1">
                                    {{ $completionDate->translatedFormat('d F Y') }}
                                </p>

                                @if($isLate)
                                    <!-- Jika terlambat -->
                                    <p class="text-xs text-red-500 font-medium mt-1">⚠️ Overdue {{ $completionDate->diffInDays($dueDate) }} hari</p>
                                @endif
                            @endif
                        </div>
                    </div>
                
                    <!-- Card Area Temuan -->
                    <div class="bg-white p-4 rounded-lg shadow-md border-l-4 border-red-500">
                        <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                            <i class="fas fa-map-marker-alt text-red-500"></i>
                            <p>Finding Area</p>
                        </div>
                        <p class="text-gray-900 font-semibold text-sm mt-1">
                            @if($laporan->area && $laporan->area->nama_area && $laporan->detail_area)
                                {{ $laporan->area->nama_area }} - {{ $laporan->detail_area }}
                            @else
                                <span class="text-gray-400">No area details available</span>
                            @endif
                        </p>                        
                    </div>

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
                </div>
            </div>
        </div>
        
        @php
            // Cek apakah pengguna menggunakan guard 'ehs' atau 'web' (untuk pengguna biasa)
            if (Auth::guard('ehs')->check()) {
                // Jika pengguna adalah EHS, ambil role dari relasi 'roles' pada model EhsUser
                $user = Auth::guard('ehs')->user();
                $roleName = optional($user->roles->first())->name;
            } else {
                // Jika pengguna adalah User biasa, ambil role dari relasi 'roleLct' pada model User
                $user = Auth::user();
                $roleName = optional($user->roleLct->first())->name;
            }
        
            // Tentukan role yang tidak diizinkan
            $notAllowed = in_array($roleName, ['user', 'manajer']);
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
                            <!-- Notifikasi Role Tidak Diizinkan -->
                            <div class="mt-3 p-4 bg-gray-200 border border-gray-400 rounded-lg">
                                <p class="text-gray-700 font-semibold">⚠️ You cannot take action on this report.</p>
                            </div>
                        @elseif(in_array($laporan->status_lct, ['in_progress', 'progress_work']))
                            <!-- Notifikasi PIC Belum Selesai -->
                            <div class="mt-3 p-4 bg-yellow-100 border border-yellow-400 rounded-lg">
                                <p class="text-yellow-800 font-semibold">⚠️ PIC belum menyelesaikan progres perbaikan. Anda tidak dapat memberikan keputusan sekarang.</p>
                            </div>
                            @else
                                <div class="flex space-x-4">
                                    <!-- Approve Button -->
                                    <form action="{{ route('ehs.reporting.approve', $laporan->id_laporan_lct) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                            class="px-5 py-2.5 bg-emerald-600 text-white font-semibold rounded-lg shadow-md transition-all hover:bg-emerald-700 cursor-pointer disabled:bg-gray-400 disabled:cursor-not-allowed"
                                            @disabled(in_array($laporan->status_lct, ['approved', 'progress_work', 'revision', 'revision_temporary']))>
                                            Approve
                                        </button>
                                    </form>

                                    <!-- Reject Button -->
                                    <button type="button" @click="revision = true"
                                        class="px-5 py-2.5 bg-rose-600 text-white font-semibold rounded-lg shadow-md transition-all hover:bg-rose-700 cursor-pointer"
                                        @disabled(in_array($laporan->status_lct, ['approved', 'progress_work']))>
                                        Revision
                                    </button>
                                </div>
                            
                                <!-- Alasan Penolakan -->
                                <div x-show="revision" class="mt-4">
                                    <form @submit="revision = false" action="{{ route('ehs.reporting.reject', $laporan->id_laporan_lct) }}" method="POST">
                                        @csrf
                                        <label class="block text-gray-700 font-semibold">Revision Reason:</label>
                                        <textarea x-model="reason" name="alasan_reject" rows="3"
                                            class="w-full mt-2 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>

                                        <div class="flex mt-3 space-x-2">
                                            <button type="submit"
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 cursor-pointer">
                                                Send Revision
                                            </button>
                                            <button type="button" @click="revision = false"
                                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 cursor-pointer">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Status Laporan -->
                                @if($laporan->status_lct === "approved" && $laporan->tingkat_bahaya === 'Low')
                                    <div class="mt-6 p-4 bg-green-100 border border-green-400 rounded-lg flex justify-between items-center">
                                        <p class="text-green-800 font-semibold">✅ The report has been approved.</p>
                                        <form action="{{ route('ehs.reporting.close', $laporan->id_laporan_lct) }}" method="POST">
                                            @csrf 
                                            <button type="submit" @click="closed = true"
                                                    class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg shadow-md hover:bg-gray-800 cursor-pointer">
                                                Close
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endif
                        @else
                            <div class="mt-3 p-4 bg-gray-200 border border-gray-400 rounded-lg">
                                <p class="text-gray-700 font-semibold">🔒 The report has been closed.</p>
                            </div>
                        @endif

                        <a href="{{ route(
                            $roleName === 'ehs' 
                                ? 'ehs.reporting.history' 
                                : 'admin.reporting.history', 
                            $laporan->id_laporan_lct
                        ) }}" class="inline-block">
                            <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-50">
                                <i class="fas fa-history mr-2"></i>History
                            </button>
                        </a>
                        
                        

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