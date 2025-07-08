<div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
    <div class="min-w-full divide-y divide-gray-300 bg-white rounded-lg">
        @if($laporans->isEmpty())
            <div class="flex flex-col items-center justify-center px-4 py-8 text-gray-500">
                <i class="fa-solid fa-face-smile text-3xl mb-2"></i>
                <p class="text-[11px] font-medium">All in good condition 🎉</p>
                <p class="text-[11px] text-gray-400">No reports at this time. Good job!</p>
            </div>
        @else
            @foreach($laporans as $laporan)
                @php
                    $bukti_temuan = json_decode($laporan->bukti_temuan, true);
                    $bukti_temuan_urls = collect($bukti_temuan)->map(fn($path) => asset('storage/' . $path));
                @endphp

                <div class="hover:bg-gray-50 text-[11px] transition duration-200 ease-in-out border-b px-3 py-3">
                    <div class="flex items-start gap-4">

                        <!-- Nomor -->
                        <div class="w-6 flex-shrink-0 text-gray-800 font-semibold pt-2 text-center">
                            {{ $loop->iteration }}
                        </div>

                        <!-- Gambar -->
                        @if($bukti_temuan_urls->isNotEmpty())
                            <img src="{{ $bukti_temuan_urls->first() }}" alt="Evidence Image" loading="lazy"
                                class="w-16 h-16 object-cover rounded-md shadow-sm border border-gray-200">
                        @else
                            <div class="w-16 h-16 flex items-center justify-center bg-gray-100 text-gray-400 rounded-md border">
                                No Image
                            </div>
                        @endif

                        @php
                            $namaKategori = $laporan->kategori->nama_kategori ?? '-';
                            $tampilKategori = $namaKategori === '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)' ? '5S' : $namaKategori;

                            $namaStatus = $laporan->status_lct;
                            $statusMapping = [
                                'open' => ['label' => 'Open'],
                                'review' => ['label' => 'Review'],
                                // In Progress
                                'in_progress' => ['label' => 'In Progress'],
                                'progress_work' => ['label' => 'In Progress'],
                                'work_permanent' => ['label' => 'In Progress'],
                                // Waiting Approval
                                'waiting_approval' => ['label' => 'Waiting Approval'],
                                'waiting_approval_temporary' => ['label' => 'Waiting Approval(temporary)'],
                                'waiting_approval_permanent' => ['label' => 'Waiting Approval(permanent)'],
                                'waiting_approval_taskbudget' => ['label' => 'Waiting Approval(task & budget)'],
                                // Approved
                                'approved' => ['label' => 'Approved'],
                                'approved_temporary' => ['label' => 'Approved'],
                                'approved_permanent' => ['label' => 'Approved'],
                                'approved_taskbudget' => ['label' => 'Approved'],
                                // Revision
                                'revision' => ['label' => 'Revision'],
                                'temporary_revision' => ['label' => 'Revision'],
                                'permanent_revision' => ['label' => 'Revision'],
                                'taskbudget_revision' => ['label' => 'Revision'],
                                // Closed
                                'closed' => ['label' => 'Closed'],
                            ];
                        @endphp

                        <!-- Konten -->
                        <div class="flex-1 space-y-1">
                            <!-- Header -->
                            <div class="flex justify-between items-start">
                                <div class="text-gray-800 font-semibold">
                                    #{{ $laporan->id_laporan_lct }}
                                </div>
                                <div class="text-gray-800 text-[8px] px-2 py-0.5 rounded-md">
                                    {{ $statusMapping[$namaStatus]['label'] }}
                                </div>
                            </div>

                            <div class="text-gray-500 text-[10px]">
                                Finding Date: {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('F j, Y') }}
                            </div>

                            <div class="text-gray-700">
                                <span class="font-medium text-[10px]">Category:</span> {{ $tampilKategori ?? '-' }} |
                                <span class="font-medium text-[10px]">Area:</span> {{ $laporan->area->nama_area ?? '-' }}
                            </div>
                            @php
                                if (Auth::guard('ehs')->check()) {
                                    $user = Auth::guard('ehs')->user();
                                    $roleName = 'ehs';
                                } else {
                                    $user = Auth::guard('web')->user();
                                    // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
                                    $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
                                }
            
                            
                                if ($roleName === 'ehs') {
                                    $routeName = 'ehs.reporting.show';
                                } elseif ($roleName === 'pic') {
                                    $routeName = 'admin.manajemen-lct.show';
                                } else {
                                    $routeName = 'admin.reporting.show';
                                }
                            @endphp
                            <div>
                                <a href="{{ route($routeName, $laporan->id_laporan_lct) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium text-[10px]">
                                    Details 
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
