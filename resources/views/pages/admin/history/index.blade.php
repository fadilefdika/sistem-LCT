<x-app-layout>
    <section class="p-4 sm:p-6">
        <div class="mx-auto max-w-screen-2xl">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Riwayat Perbaikan</h1>
                    <p class="text-gray-600">Log aktivitas dari semua peran dalam proses LCT</p>
                </div>
                @php
                        $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
                        $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');
                  
                    if ($roleName === 'ehs') {
                        $routeName = 'ehs.progress-perbaikan.show';
                    } elseif ($roleName === 'pic') {
                        $routeName = 'admin.manajemen-lct.show';
                    } else {
                        $routeName = 'admin.progress-perbaikan.show';
                    }
                @endphp
            
            <a href="{{ route($routeName, $id_laporan_lct) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Kembali
            </a>

            </div>

            <!-- Table Section -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">No</th>
                                <th class="px-6 py-3 text-left">Tanggal</th>
                                <th class="px-6 py-3 text-left">User</th>
                                <th class="px-6 py-3 text-left">Role</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Alasan Reject</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($history as $i => $item)
                            
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-gray-800 font-medium">{{ $i + 1 }}</td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $item->created_at ? $item->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') : '-' }} WIB
                                    </td>                                    
                                    <td class="px-6 py-4 text-gray-700">{{ $item->user->fullname ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $item->status_lct === 'open' ? 'finder' : ($item->role ?? '-') }}
                                    </td>                                    
                                    <td class="px-6 py-4">
                                        @php
                                            $statusMapping = [
                                                'open' => ['tracking' => 'Report has been created'],
                                                'open_ehs' => ['tracking' => 'The report has been viewed by EHS'],
                                                'open_manager' => ['tracking' => 'The report budget has been viewed by Manager'],
                                                'review' => ['tracking' => 'Report is under review'],
                                                'in_progress' => ['tracking' => 'Report has been sent, but PIC has not viewed it'],
                                                'progress_work' => ['tracking' => 'PIC has viewed the report'],
                                                'work_permanent' => ['tracking' => 'PIC is working on a permanent LCT'],
                                                'waiting_approval' => ['tracking' => 'Waiting for LCT Low approval from EHS'],
                                                'waiting_approval_temporary' => ['tracking' => 'Waiting for temporary LCT approval from EHS'],
                                                'waiting_approval_permanent' => ['tracking' => 'Waiting for permanent LCT approval from EHS'],
                                                'waiting_approval_taskbudget' => ['tracking' => 'Waiting for task and budget approval from the manager'],
                                                'approved' => ['tracking' => 'LCT Low has been approved by EHS'],
                                                'approved_temporary' => ['tracking' => 'Temporary LCT has been approved by EHS'],
                                                'approved_permanent' => ['tracking' => 'Permanent LCT has been approved by EHS'],
                                                'approved_taskbudget' => ['tracking' => 'Task and budget for permanent LCT has been approved by the manager'],
                                                'revision' => ['tracking' => 'LCT Low needs revision by PIC'],
                                                'temporary_revision' => ['tracking' => 'Temporary LCT needs revision by PIC'],
                                                'permanent_revision' => ['tracking' => 'Permanent LCT needs revision by PIC'],
                                                'taskbudget_revision' => ['tracking' => 'The LCT task and budget require revision by PIC'],
                                                'closed' => ['tracking' => 'Report has been closed by PIC'],
                                            ];
                                            $status = $statusMapping[$item->status_lct] ?? ['tracking' => 'Status not found'];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 text-black rounded-full whitespace-nowrap">
                                            {{ $status['tracking'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 max-w-xs truncate">
                                        {{ $item->alasan_reject ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data log aktivitas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>
</x-app-layout>
