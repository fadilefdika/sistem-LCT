@props([
    'align' => 'right',
    'notifikasiLCT' => [],
    'roleName' => $roleName
])

<div class="relative inline-flex" x-data="{ open: false, activeStatus: null }">
    <button
        class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 lg:hover:bg-gray-200 dark:hover:bg-gray-700/50 dark:lg:hover:bg-gray-800 rounded-full"
        :class="{ 'bg-gray-200 dark:bg-gray-800': open }"
        aria-haspopup="true"
        @click.prevent="open = !open"
        :aria-expanded="open"
    >
        <span class="sr-only">Notifications</span>
        <i class="fas fa-bell text-gray-500/80 dark:text-gray-400/80 text-base md:text-lg"></i>
        <div class="absolute top-1 right-1 w-2 h-2 md:top-0 md:right-0 md:w-2.5 md:h-2.5 bg-red-500 border-2 border-gray-100 dark:border-gray-900 rounded-full"></div>
    </button>

    <div class="origin-top-right z-10 fixed sm:absolute top-16 sm:top-full w-[calc(100vw-2rem)] sm:min-w-80 max-w-sm bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 py-1.5 rounded-lg shadow-lg overflow-hidden mt-1 {{ $align === 'right' ? 'right-4 sm:right-0' : 'left-4 sm:left-0' }}"
        @click.outside="open = false"
        @keydown.escape.window="open = false"
        x-show="open"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-out duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase pt-1.5 pb-2 px-4">Notifications <span>({{ $notifikasiLCT->count() }})</span></div>
        @php
                // Ambil user & role secara konsisten
                if (Auth::guard('ehs')->check()) {
                    $user = Auth::guard('ehs')->user();
                    $roleName = 'ehs';
                } else {
                    $user = Auth::guard('web')->user();
                    $roleName = optional($user->roleLct->first())->name ?? 'guest';
                }

                // Mapping status
                $statusMapping = [
                    'open' => ['label' => 'Open (new)', 'color' => 'bg-gray-500', 'tracking' => 'Report has been created'],
                    'review' => ['label' => 'Under Review', 'color' => 'bg-purple-500', 'tracking' => 'Report is under review'],
                    'in_progress' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'Not yet viewed by PIC'],
                    'progress_work' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'PIC has viewed the report'],
                    'work_permanent' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'Permanent LCT in progress'],
                    'waiting_approval' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Awaiting EHS approval'],
                    'waiting_approval_temporary' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for temporary LCT approval from EHS'],
                    'waiting_approval_permanent' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Awaiting EHS approval'],
                    'waiting_approval_taskbudget' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting approval manager'],
                    'approved' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Approved by EHS'],
                    'approved_temporary' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Temporary approved by EHS'],
                    'approved_permanent' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Permanent approved by EHS'],
                    'approved_taskbudget' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Manager approved task & budget'],
                    'revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'PIC must revise LCT Low'],
                    'temporary_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'Temporary LCT needs revision by PIC'],
                    'permanent_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'Permanent LCT needs revision by PIC'],
                    'taskbudget_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'PIC must revise task & budget'],
                    'closed' => ['label' => 'Closed', 'color' => 'bg-green-700', 'tracking' => 'EHS closed the report'],
                ];

                // Kelompokkan notifikasi dengan normalisasi status
                $notifikasiGroupedByLabel = $notifikasiLCT->map(function ($item) use ($statusMapping, $roleName) {
                    $pendingTemporaryStatuses = [
                        'waiting_approval_taskbudget',
                        'taskbudget_revision',
                        'approved_taskbudget',
                    ];

                    // Hanya untuk EHS: ubah status jika perlu
                    if ($roleName === 'ehs') {
                        if (in_array($item->status_lct, $pendingTemporaryStatuses) && $item->approved_temporary_by_ehs === 'pending') {
                            $item->status_lct = 'waiting_approval_temporary';
                        }

                        if ($item->status_lct === 'waiting_approval_temporary' && $item->approved_temporary_by_ehs === 'approved') {
                            $item->status_lct = 'approved_temporary';
                        }
                    }

                    // Ambil label sesuai mapping atau fallback
                    $item->label_group = $statusMapping[$item->status_lct]['label'] ?? ucfirst(str_replace('_', ' ', $item->status_lct));

                    return $item;
                })->groupBy('label_group');
            @endphp

        <div class="space-y-3 max-h-[calc(100vh-10rem)] sm:max-h-96 overflow-y-auto">
            @foreach ($notifikasiGroupedByLabel as $label => $notifications)
                @php
                    $firstStatus = $notifications->first()->status_lct;
                    $statusInfo = $statusMapping[$firstStatus] ?? ['label' => $label, 'color' => 'bg-gray-400', 'tracking' => 'Status unknown'];
                    $statusCount = $notifications->count();
                @endphp

                <button 
                    class="flex items-center w-full px-4 py-2 text-sm text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="activeStatus === '{{ $label }}' ? activeStatus = null : activeStatus = '{{ $label }}'"
                >
                    <div class="flex items-center">
                        <div class="w-2.5 h-2.5 rounded-full {{ $statusInfo['color'] }}"></div>
                        <span class="ml-2 font-medium text-gray-800 dark:text-gray-100">{{ $label }}</span>
                        <span class="ml-2 text-gray-400 dark:text-gray-500">({{ $statusCount }})</span>
                    </div>
                </button>

                <div class="space-y-2 max-h-48 overflow-y-auto" x-show="activeStatus === '{{ $label }}'" x-transition>
                    @foreach ($notifications as $notif)
                        @php
                            $formattedDate = \Carbon\Carbon::parse($notif->updated_at)
                                ->timezone('Asia/Jakarta')
                                ->format('F d, Y H:i');

                            $url = '';
                            if ($roleName === 'ehs') {
                                $url = $notif->status_lct === 'open'
                                    ? route('ehs.reporting.show.new', $notif->id_laporan_lct)
                                    : route('ehs.reporting.show', $notif->id_laporan_lct);
                            } elseif ($roleName === 'pic') {
                                $url = route('admin.manajemen-lct.show', $notif->id_laporan_lct);
                            } elseif ($roleName === 'manajer') {
                                $url = route('admin.budget-approval.show', $notif->id_laporan_lct);
                            }
                        @endphp

                        <div class="border-b border-gray-200 dark:border-gray-700/60 last:border-0">
                            <a class="block py-2 px-4 hover:bg-gray-50 dark:hover:bg-gray-700/20" href="{{ $url }}" @click="open = false">
                                <span class="block text-sm mb-2">
                                    📣 <span class="font-medium text-gray-800 dark:text-gray-100">{{ $label }}</span>
                                </span>
                                <span class="block text-xs text-gray-900 dark:text-gray-300">#{{ $notif->id_laporan_lct }}</span>
                                <span class="block text-xs text-gray-600 dark:text-gray-300 truncate">Finding : {{ $notif->temuan_ketidaksesuaian }}</span>
                                <span class="block text-xs font-medium text-gray-400 dark:text-gray-500">
                                    {{ $formattedDate }} WIB
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>


<script>
    // Fungsi untuk toggle status notifications
    function toggleStatusNotifications(status) {
        const statusDiv = document.getElementById(status);
        if (statusDiv) {
            statusDiv.style.display = statusDiv.style.display === 'none' ? 'block' : 'none';
        }
    }
</script>
