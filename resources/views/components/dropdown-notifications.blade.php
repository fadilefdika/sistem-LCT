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

    <div class="origin-top-right z-10 absolute top-full min-w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 py-1.5 rounded-lg shadow-lg overflow-hidden mt-1 {{ $align === 'right' ? 'right-0' : 'left-0' }} "
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
            $statusMapping = [
                'open' => ['label' => 'Open', 'color' => 'bg-gray-500', 'tracking' => 'Report has been created'],
                'review' => ['label' => 'Under Review', 'color' => 'bg-purple-500', 'tracking' => 'Report is under review'],
                'in_progress' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'Report has been sent, but PIC has not viewed it'],
                'progress_work' => ['label' => 'In Progress (PIC Viewing)', 'color' => 'bg-yellow-500', 'tracking' => 'PIC has viewed the report'],
                'work_permanent' => ['label' => 'In Progress (Working on Permanent LCT)', 'color' => 'bg-yellow-500', 'tracking' => 'PIC is working on a permanent LCT'],
                'waiting_approval' => ['label' => 'Waiting Approval (Low)', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for LCT Low approval from EHS'],
                'waiting_approval_temporary' => ['label' => 'Waiting Approval (Temporary)', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for temporary LCT approval from EHS'],
                'waiting_approval_permanent' => ['label' => 'Waiting Approval (Permanent)', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for permanent LCT approval from EHS'],
                'waiting_approval_taskbudget' => ['label' => 'Waiting for Task Budget Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for task and budget approval from the manager'],
                'approved' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'LCT Low has been approved by EHS'],
                'approved_temporary' => ['label' => 'Approved (Temporary)', 'color' => 'bg-green-500', 'tracking' => 'Temporary LCT has been approved by EHS'],
                'approved_permanent' => ['label' => 'Approved (Permanent)', 'color' => 'bg-green-500', 'tracking' => 'Permanent LCT has been approved by EHS'],
                'approved_taskbudget' => ['label' => 'Approved (Task Budget)', 'color' => 'bg-green-500', 'tracking' => 'Task and budget for permanent LCT has been approved by the manager'],
                'revision' => ['label' => 'Revision (Low)', 'color' => 'bg-red-500', 'tracking' => 'LCT Low needs revision by PIC'],
                'temporary_revision' => ['label' => 'Revision (Temporary)', 'color' => 'bg-red-500', 'tracking' => 'Temporary LCT needs revision by PIC'],
                'permanent_revision' => ['label' => 'Revision (Permanent)', 'color' => 'bg-red-500', 'tracking' => 'Permanent LCT needs revision by PIC'],
                'taskbudget_revision' => ['label' => 'Task Budget Revision', 'color' => 'bg-red-500', 'tracking' => 'The LCT task and budget require revision by PIC'],
                'closed' => ['label' => 'Closed', 'color' => 'bg-green-700', 'tracking' => 'Report has been closed by PIC'],
            ];

            $notifikasiGroupedByStatus = $notifikasiLCT->groupBy('status_lct');
        @endphp

        <div class="space-y-3">
            @foreach ($notifikasiGroupedByStatus as $status => $notifications)
                @php
                    $statusInfo = $statusMapping[$status] ?? ['label' => ucfirst($status), 'color' => 'bg-gray-400', 'tracking' => 'Status unknown'];
                    $statusCount = $notifications->count();
                @endphp

                <button 
                    class="flex items-center w-full px-4 py-2 text-sm text-left rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="activeStatus === '{{ $status }}' ? activeStatus = null : activeStatus = '{{ $status }}'"
                >
                    <div class="flex items-center">
                        <div class="w-2.5 h-2.5 rounded-full {{ $statusInfo['color'] }}"></div>
                        <span class="ml-2 font-medium text-gray-800 dark:text-gray-100">{{ $statusInfo['label'] }}</span>
                        <span class="ml-2 text-gray-400 dark:text-gray-500">({{ $statusCount }})</span>
                    </div>
                </button>

                <div class="space-y-2 max-h-48 overflow-y-auto" x-show="activeStatus === '{{ $status }}'" x-transition>
                    @foreach ($notifications as $notif)
                        <div class="border-b border-gray-200 dark:border-gray-700/60 last:border-0">
                            @php
                                // Format tanggal menjadi tanggal, bulan, tahun, jam
                                $formattedDate = \Carbon\Carbon::parse($notif->updated_at)
                                ->timezone('Asia/Jakarta')
                                ->format('F d, Y H:i');

                                
                                // Menentukan URL tujuan berdasarkan roleName
                                $url = '';
                                if ($roleName === 'ehs') {
                                    $url = $status === 'open' ? route('ehs.laporan-lct.show', $notif->id_laporan_lct) : route('ehs.progress-perbaikan.show', $notif->id_laporan_lct);
                                } elseif ($roleName === 'pic') {
                                    $url = route('admin.manajemen-lct.show', $notif->id_laporan_lct);
                                } elseif ($roleName === 'manajer') {
                                    $url = route('admin.budget-approval.show', $notif->id_laporan_lct);
                                }
                            @endphp

                            <a class="block py-2 px-4 hover:bg-gray-50 dark:hover:bg-gray-700/20" href="{{ $url }}" @click="open = false">
                                <span class="block text-sm mb-2">
                                    📣 <span class="font-medium text-gray-800 dark:text-gray-100">{{ $statusInfo['label'] }}</span>
                                </span>
                                <span class="block text-xs text-gray-900 dark:text-gray-300">#{{ $notif->id_laporan_lct }}</span>
                                <span class="block text-xs text-gray-600 dark:text-gray-300">Finding : {{ $notif->temuan_ketidaksesuaian }}</span>
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
