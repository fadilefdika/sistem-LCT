<x-app-layout>
    <div x-data="{ activeTab: '{{ in_array($laporan->status_lct, ['approved', 'waiting_approval', 'rejected']) ? 'pic' : 'user' }}' }" class="px-5 pt-2">
        <!-- Tabs -->
        <div class="flex space-x-4 border-b">
            <button @click="activeTab = 'user'" 
                    :class="activeTab === 'user' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                    class="px-4 py-2 focus:outline-none cursor-pointer">
                User
            </button>
        
            <button @click="activeTab = 'pic'" 
                    :class="activeTab === 'pic' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                    class="px-4 py-2 focus:outline-none cursor-pointer">
                PIC
            </button>
        
            @if(in_array($laporan->tingkat_bahaya, ['Medium', 'High']) && $laporan->status_lct === 'approved')
                <button @click="activeTab = 'task-pic'" 
                        :class="activeTab === 'task-pic' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                        class="px-4 py-2 focus:outline-none cursor-pointer">
                    Task PIC
                </button>
            @endif
        </div>
        

        <!-- Tab Content -->
        <div class="mt-4">
            {{-- Laporan dari User --}}
            <div x-show="activeTab === 'user'">
                <div class="my-3 max-h-min rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4">
    
                    <!-- Laporan dari Pelapor (Full Width) -->
                    <div class="col-span-2 bg-white p-6 rounded-xl shadow-md">
                        <h5 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                            📝 Laporan dari Pelapor
                        </h5>
                        <div class="w-full h-[2px] bg-gray-200 my-3"></div>
                        <p class="text-gray-500 text-xs">Temuan Ketidaksesuaian</p>
                        <p class="text-gray-900 font-semibold text-lg">{{$laporan->temuan_ketidaksesuaian}}</p>
                    </div>
                
                    <!-- Informasi Pelapor -->
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-gray-500 text-xs flex items-center gap-1">
                                    <i class="fas fa-user text-blue-500"></i> Nama Pelapor
                                </p>
                                <p class="text-gray-900 font-semibold text-sm mt-1">{{$laporan->user->fullname}}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-xs flex items-center gap-1">
                                    <i class="fas fa-calendar-alt text-green-500"></i> Tanggal Temuan
                                </p>
                                <p class="text-gray-900 font-semibold text-sm mt-1">
                                    {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->translatedFormat('l, d F Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                
                    <!-- Area Temuan -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <p class="text-gray-500 text-xs flex items-center gap-1">
                            <i class="fas fa-map-marker-alt text-red-500"></i> Area Temuan
                        </p>
                        <p class="text-gray-900 font-semibold text-sm mt-1">{{$laporan->area}} - {{$laporan->detail_area}}</p>
                    </div>
                
                    <!-- Kategori Temuan -->
                    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 ease-in-out col-span-2">
                        <p class="text-gray-500 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-flag text-yellow-500"></i> Kategori Temuan
                        </p>
                        <p class="text-gray-900 font-semibold mt-2 bg-yellow-100 p-2 rounded-lg hover:bg-yellow-200 transition-all duration-200 ease-in-out">
                            {{$laporan->kategori_temuan}}
                        </p>
                    </div>
                
                    <!-- Rekomendasi Safety -->
                    <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-all duration-300 ease-in-out col-span-2">
                        <p class="text-gray-500 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-shield-alt text-green-500"></i> Rekomendasi Safety
                        </p>
                        <p class="text-gray-900 mt-2 text-justify leading-relaxed text-sm">
                            {{$laporan->rekomendasi_safety}}
                        </p>
                    </div>
                
                    <!-- Card Gambar Temuan -->
                            <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
                                <p class="text-gray-700 text-lg font-semibold">Gambar Temuan</p>
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
                            <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 transition-opacity duration-300">
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
                </div>
            </div>


            {{-- Laporan dari PIC --}}
            <div x-show="activeTab === 'pic'" class="oveflow-hidden">
                <div class="grid md:grid-cols-2 justify-center w-full h-full">
                    <!-- Card Laporan dari Pelapor -->
                    <div class="relative max-w-full bg-[#F3F4F6] overflow-hidden h-full p-1 pb-20 max-h-[calc(100vh)] overflow-y-auto [&::-webkit-scrollbar]:w-1
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
                                        📝 Laporan dari PIC
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
                                    <p class="text-gray-500 text-xs">Temuan Ketidaksesuaian</p>
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
                            <div class="bg-white p-5 rounded-xl shadow-md border-l-4 
                                {{ $diffInDays < 0 ? 'border-red-500' : ($diffInDays === 0 && $remainingHours < 24 ? 'border-yellow-500' : 'border-green-500') }}">

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-2 items-center">
                                    
                                    <!-- Nama Pelapor -->
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                                            <i class="fas fa-user text-blue-500"></i>
                                            <p>Nama PIC</p>
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
                                            {{ $diffInDays < 0 ? 'text-red-500' : ($diffInDays === 0 && $remainingHours < 24 ? 'text-yellow-500' : 'text-green-500') }}">
                                            @if($diffInDays < 0)
                                                ⚠️ Melewati batas waktu
                                            @elseif($diffInDays === 0 && $remainingHours < 24)
                                                ⏳ Batas waktu hampir habis
                                            @else
                                                ✅ Masih dalam batas waktu
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
                                        <p>Tanggal Selesai</p>
                                    </div>

                                    @if($laporan->date_completion == null)
                                        <!-- Jika belum selesai -->
                                        <p class="text-red-500 font-semibold text-sm mt-1">Belum selesai</p>
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
                                            <p class="text-xs text-red-500 font-medium mt-1">⚠️ Terlambat {{ $completionDate->diffInDays($dueDate) }} hari</p>
                                        @endif
                                    @endif
                                </div>
                            </div>

                        
                            <!-- Card Area Temuan -->
                            <div class="bg-white p-4 rounded-lg shadow-md border-gray-300">
                                <div class="flex items-center gap-1 text-gray-500 text-xs tracking-wide">
                                    <i class="fas fa-map-marker-alt text-red-500"></i>
                                    <p>Area Temuan</p>
                                </div>
                                <p class="text-gray-900 font-semibold text-sm mt-1">{{$laporan->area}} - {{$laporan->detail_area}}</p>
                            </div>

                            <!-- Card Gambar Perbaikan -->
                            <div class="bg-white p-4 rounded-lg shadow-md border-gray-300 mt-3">
                                <p class="text-gray-700 text-lg font-semibold">Gambar Hasil Perbaikan</p>
                                <div class="grid grid-cols-5 gap-2 mt-2">
                                    @foreach ($bukti_perbaikan->take(5) as $gambar)
                                        <img src="{{ $gambar }}" 
                                            class="w-24 h-24 object-cover rounded-lg cursor-pointer hover:scale-110 transition-transform"
                                            alt="Gambar Hasil Perbaikan"
                                            onclick="openModal('{{ $gambar }}')">
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                
                    <!-- Form Laporan Temuan -->
                    
                    <div class="relative max-w-full bg-gray-100 overflow-hidden shadow-md p-1 pb-20 max-h-[calc(100vh)] overflow-y-auto 
                        [&::-webkit-scrollbar]:w-1 [&::-webkit-scrollbar-track]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100
                        [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-gray-300
                        dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
                        
                        <div class="bg-white p-6 rounded-lg shadow-lg max-w-xl mx-auto">
                            
                            <!-- Header -->
                            <h5 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                                Approval Laporan Perbaikan LCT 
                            </h5>

                            <!-- Garis Pemisah -->
                            <div class="w-full h-[2px] bg-gray-200 my-3"></div>

                            <p class="text-gray-700 font-semibold mb-2">Setujui laporan ini?</p>

                            <div x-data="{ rejected: false, reason: '', closed: false }">
                                <div class="flex space-x-4">
                                    <!-- Approve Button -->
                                    <form action="{{ route('admin.progress-perbaikan.approve', $laporan->id_laporan_lct) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                            class="px-5 py-2.5 bg-emerald-600 text-white font-semibold rounded-lg shadow-md transition-all hover:bg-emerald-700 cursor-pointer disabled:bg-gray-400 disabled:cursor-not-allowed"
                                            @if(in_array($laporan->status_lct, ['approved', 'progress_work'])) disabled @endif>
                                            Approve
                                        </button>
                                    
                                    </form>
                                
                                    <button type="button" @click="rejected = true"
                                        class="px-5 py-2.5 bg-rose-600 text-white font-semibold rounded-lg shadow-md transition-all hover:bg-rose-700 cursor-pointer"
                                        @if(in_array($laporan->status_lct, ['approved', 'progress_work'])) disabled @endif>
                                        Reject
                                    </button>
                                </div>
                                
                                <!-- Alasan Penolakan -->
                                <div x-show="rejected" class="mt-4">
                                    <form @submit="rejected = false" action="{{ route('admin.progress-perbaikan.reject', $laporan->id_laporan_lct) }}" method="POST">
                                        @csrf
                                        <label class="block text-gray-700 font-semibold">Alasan Penolakan:</label>
                                        <textarea x-model="reason" name="alasan_reject" rows="3"
                                            class="w-full mt-2 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"></textarea>

                                        <div class="flex mt-3 space-x-2">
                                            <button type="submit"
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 cursor-pointer">
                                                Kirim Revisi
                                            </button>
                                            <button type="button" @click="rejected = false"
                                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 cursor-pointer">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Status Laporan -->
                                @if($laporan->status_lct === "approved")
                                    @if($laporan->tingkat_bahaya === 'Low')
                                        <div class="mt-6 p-4 bg-green-100 border border-green-400 rounded-lg flex justify-between items-center">
                                            <p class="text-green-800 font-semibold">✅ Laporan telah disetujui.</p>
                                            <form action="{{ route('admin.progress-perbaikan.close', $laporan->id_laporan_lct) }}" method="POST">
                                                @csrf
                                                <button type="submit" @click="closed = true"
                                                        class="px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg shadow-md hover:bg-gray-800 cursor-pointer">
                                                    Close
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endif


                                @if($laporan->rejectLaporan->isNotEmpty())
                                <div class="mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-md">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-red-500 text-xl">❌</span>
                                        <p class="text-red-800 font-semibold text-lg">Laporan Ditolak</p>
                                    </div>
                                    <table class="w-full border-collapse">
                                        <thead>
                                            <tr class="bg-red-200 text-red-800 text-sm font-semibold">
                                                <th class="p-2 text-left">Alasan</th>
                                                <th class="p-2 text-left">Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($laporan->rejectLaporan as $reject)
                                                <tr class="border-t border-red-300 text-gray-700">
                                                    <td class="p-2 text-sm">{{ $reject->alasan_reject }}</td>
                                                    <td class="p-2 text-sm">{{ \Carbon\Carbon::parse($reject->created_at)->format('d M Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            



                                <!-- Notifikasi Laporan Ditutup -->
                                <div x-show="closed" class="mt-3 p-4 bg-gray-200 border border-gray-400 rounded-lg">
                                    <p class="text-gray-700 font-semibold">🔒 Laporan telah ditutup.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Task dari PIC --}}
            <div x-show="activeTab === 'task-pic'">
                <p>ini buat mantau</p>
            </div>
        </div>
    </div>


    {{-- script untuk modal gambar --}}
    <script>
        function openModal(imageSrc) {
            const modal = document.getElementById("imageModal");
            const modalImage = document.getElementById("modalImage");

            modal.classList.remove("hidden");
            modal.classList.add("flex"); // Agar modal muncul
            modalImage.src = imageSrc;
        }

        function closeModal() {
            const modal = document.getElementById("imageModal");
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }

        // Tutup modal jika klik di luar gambar
        document.getElementById("imageModal").addEventListener("click", function(event) {
            if (event.target === this) {
                closeModal();
            }
        });
    </script>
</x-app-layout>

