<div class="grid md:grid-cols-2 justify-center w-full h-full">
    <!-- Card Laporan dari Pelapor -->
    <div class="relative max-w-full bg-[#F3F4F6] overflow-hidden h-full p-1 pb-32 max-h-[calc(100vh)] overflow-y-auto [&::-webkit-scrollbar]:w-1
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

        
            <!-- Card Informasi Pelapor -->
            @php
                $dueDate = \Carbon\Carbon::parse($laporan->due_date);
                $now = \Carbon\Carbon::now();
                $diffInHours = $now->diffInHours($dueDate, false);
                $diffInDays = floor($diffInHours / 24);
                $remainingHours = $diffInHours % 24;

                $borderClass = 'border-green-500';
                if ($diffInDays < 0) {
                    $borderClass = 'border-red-500';
                } elseif ($diffInDays === 0 && $remainingHours < 24) {
                    $borderClass = 'border-yellow-500';
                }
            @endphp
            <div class="bg-white p-5 rounded-xl shadow-md border-l-4 border-blue-500">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-2 items-center">
                    
                    <!-- PIC Name -->
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                            <i class="fas fa-user text-blue-500"></i>
                            <p>SVP Name</p>
                        </div>
                        <p class="text-gray-900 font-semibold text-sm mt-1">
                            {{ $laporan->picUser->fullname }}
                        </p>
                    </div>

                    <!-- Garis Pemisah (Hanya Muncul di Layar Lebar) -->
                    <div class="hidden sm:flex justify-center">
                        <div class="w-[2px] bg-gray-300 h-10 rounded-full"></div>
                    </div>

                    <!-- Due Date -->
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2 text-gray-600 text-xs font-medium">
                            <i class="fas fa-hourglass-half text-blue-500"></i>
                            <p>Due Date</p>
                        </div>
                        <p class="text-gray-900 font-semibold text-sm mt-1">
                            {{ $dueDate->translatedFormat('d F Y') }}
                        </p>
                        
                        <!-- Status -->
                        <p class="text-xs mt-1 font-semibold 
                            {{ in_array($laporan->status_lct, ['approved', 'closed']) ? 'text-green-500' : 
                            ($diffInDays < 0 ? 'text-red-500' : 
                            ($diffInDays === 0 && $remainingHours < 24 ? 'text-yellow-500' : 'text-green-500')) }}">

                            @if (in_array($laporan->status_lct, ['approved', 'closed']))
                                ✅ Completed
                            @elseif ($diffInDays < 0)
                                ⚠️ Overdue
                            @elseif ($diffInDays === 0 && $remainingHours < 24)
                                ⏳ Deadline Approaching
                            @else
                                ✅ Within Deadline
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
                <p class="text-gray-900 font-semibold text-sm mt-1">{{$laporan->area}} - {{$laporan->detail_area}}</p>
            </div>

            <!-- Corrective Action Image Card -->
            <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
                <p class="text-gray-700 text-lg font-semibold text-center">Corrective Action Image</p>
                <div class="flex justify-center gap-1.5 mt-2">
                    @if ($bukti_perbaikan->isNotEmpty())
                        <div class="grid grid-cols-{{ min(5, $bukti_perbaikan->count()) }} gap-2">
                            @foreach ($bukti_perbaikan->take(5) as $gambar)
                                <img src="{{ $gambar }}" 
                                    class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:scale-110 transition-transform"
                                    alt="Corrective Action Image"
                                    onclick="openModal('{{ $gambar }}')">
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 text-sm font-semibold text-center">No image available. PIC has not submitted a report.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    

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
                    <div class="flex space-x-4">
                        <!-- Approve Button -->
                        <form action="{{ route('admin.progress-perbaikan.approve', $laporan->id_laporan_lct) }}" method="POST">
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
                        <form @submit="revision = false" action="{{ route('admin.progress-perbaikan.reject', $laporan->id_laporan_lct) }}" method="POST">
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
                            <form action="{{ route('admin.progress-perbaikan.close', $laporan->id_laporan_lct) }}" method="POST">
                                @csrf 
                                <button type="submit" @click="closed = true"
                                        class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg shadow-md hover:bg-gray-800 cursor-pointer">
                                    Close
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="mt-3 p-4 bg-gray-200 border border-gray-400 rounded-lg">
                        <p class="text-gray-700 font-semibold">🔒 The report has been closed.</p>
                    </div>
                @endif


                @if($laporan->rejectLaporan->isNotEmpty())
                <div class="mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-md">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-red-500 text-xl">❌</span>
                        <p class="text-red-800 font-semibold text-lg">The report needs revision.</p>
                    </div>
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-red-200 text-red-800 text-sm font-semibold">
                                <th class="p-2 text-left">Reason</th>
                                <th class="p-2 text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporan->rejectLaporan as $reject)
                                <tr class="border-t border-red-300 text-gray-700">
                                    <td class="p-2 text-sm">{{ $reject->alasan_reject }}</td>
                                    <td class="p-2 text-sm">{{ \Carbon\Carbon::parse($reject->created_at)->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            



                <!-- Notifikasi Laporan Ditutup -->
                <div x-show="closed" class="mt-3 p-4 bg-gray-200 border border-gray-400 rounded-lg">
                    <p class="text-gray-700 font-semibold">🔒 The report has been closed.</p>
                </div>
            </div>
        </div>
    </div>
</div>