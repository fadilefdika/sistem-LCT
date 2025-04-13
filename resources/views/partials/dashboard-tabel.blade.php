<h2 class="text-xl font-bold mb-4">{{ $title }}</h2>

<div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="min-w-full divide-y divide-gray-300 shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <thead class="bg-gray-100">
            <tr class="text-left text-sm font-semibold text-gray-600">
                <th class="px-4 py-3">No</th>
                <th class="px-4 py-3">Non-Conformity Findings</th>
                <th class="px-4 py-3">SVP Name</th>
                <th class="px-4 py-3">Hazard Level</th>
                <th class="px-4 py-3">Progress Status</th>
                <th class="px-4 py-3">Tracking Status</th>
                <th class="px-4 py-3">Due Date</th>
                <th class="px-4 py-3">Completion Date</th>
                <th class="px-4 py-3">Action</th>
            </tr>                
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">

            @if ($laporans->isEmpty())
                <tr>
                    <td colspan="8">
                        <div class="flex flex-col items-center justify-center py-16 text-gray-500">
                            <i class="fa-solid fa-face-smile text-4xl mb-4"></i>
                            <p class="text-base font-medium">No data found non-conformities.</p>
                        </div>
                    </td>
                </tr>
            @else
            @foreach($laporans as $laporan)
            <tr class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out">
                <td class="px-4 py-3 text-center font-semibold text-gray-800 w-12">
                    {{ $loop->iteration }}
                </td>
                <td class="px-4 py-3 text-gray-800 max-w-xs truncate cursor-pointer relative group">
                    <span class="temuan-clamp" title="{{ $laporan->temuan_ketidaksesuaian }}">
                        {{ $laporan->temuan_ketidaksesuaian }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-800 w-40">{{ $laporan->picUser->fullname ?? '-' }}</td>

                <!-- Tingkat Bahaya -->
                <td class="px-4 py-3 text-gray-800 w-28">
                    @php
                        $bahayaColors = [
                            'High' => 'bg-red-500',
                            'Medium' => 'bg-yellow-500',
                            'Low' => 'bg-green-500'
                        ];
                    @endphp
                    <span class="px-3 py-1 text-xs font-semibold text-white rounded-full {{ $bahayaColors[$laporan->tingkat_bahaya] ?? 'bg-gray-400' }}">
                        {{ $laporan->tingkat_bahaya }}
                    </span>
                </td>

                <!-- Status Progress -->
                <td class="px-4 py-3 text-gray-800 w-36">
                    @php
                        $statusMapping = [
                            'open' => ['label' => 'Open', 'color' => 'bg-gray-500', 'tracking' => 'Report has been created'],
                            'review' => ['label' => 'Review', 'color' => 'bg-purple-500', 'tracking' => 'Report is under review'],
                            'in_progress' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'Report has been sent, but PIC has not viewed it'],
                            'progress_work' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'PIC has viewed the report'],
                            'work_permanent' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'PIC is working on a permanent LCT'],
                            'waiting_approval' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for LCT Low approval from EHS'],
                            'waiting_approval_temporary' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for temporary LCT approval from EHS'],
                            'waiting_approval_permanent' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for permanent LCT approval from EHS'],
                            'waiting_approval_taskbudget' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for task and budget approval from the manager'],
                            'approved' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'LCT Low has been approved by PIC'],
                            'approved_temporary' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Temporary LCT has been approved by EHS'],
                            'approved_permanent' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Permanent LCT has been approved by EHS'],
                            'approved_taskbudget' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Task and budget for permanent LCT has been approved by the manager'],
                            'revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'LCT Low needs revision by PIC'],
                            'temporary_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'Temporary LCT needs revision by PIC'],
                            'permanent_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'Permanent LCT needs revision by PIC'],
                            'taskbudget_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'The LCT task and budget require revision by PIC'],
                            'closed' => ['label' => 'Closed', 'color' => 'bg-green-700', 'tracking' => 'Report has been closed by PIC'],
                        ];

                        $status = $statusMapping[$laporan->status_lct] ?? ['label' => 'Unknown', 'color' => 'bg-gray-400', 'tracking' => '-'];
                    @endphp

                    <span class="inline-flex items-center justify-center px-3 py-1 text-[10px] font-semibold text-white rounded-full {{ $status['color'] }} whitespace-nowrap">
                        {{ $status['label'] }}
                    </span>
                </td>

                <!-- Tracking Status -->
                <td>
                    <span class="inline-flex items-center justify-center px-3 py-1 text-black rounded-full whitespace-nowrap">
                        {{ $status['tracking'] }}
                    </span>
                </td>


                <!-- Tenggat Waktu -->
                <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($laporan->due_date)->format('F d, Y') }}
                </td>

                <!-- Tanggal Selesai / Overdue -->
                <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                    @if ($laporan->date_completion)
                        {{ \Carbon\Carbon::parse($laporan->date_completion)->format('F d, Y') }}
                    @else
                        @php
                            $dueDate = \Carbon\Carbon::parse($laporan->due_date)->startOfDay();
                            $today = \Carbon\Carbon::now()->startOfDay();
                            $overdueDays = $dueDate->diffInDays($today, false);
                        @endphp

                        @if ($overdueDays > 0)
                            <span class="bg-red-100 text-red-600 text-sm font-semibold px-2 py-1 rounded">
                                Overdue {{ $overdueDays }} days
                            </span>
                        @elseif ($overdueDays === 0)
                            <span class="bg-yellow-100 text-yellow-600 text-sm font-semibold px-2 py-1 rounded">
                                Due Today
                            </span>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    @endif
                </td>
                @php
                    $user = Auth::user();
                    $roleName = optional($user->roleLct->first())->name;
                @endphp

                <!-- Tombol Aksi -->
                <td class="px-4 py-3 flex items-center gap-2 w-28">
                    @if(in_array($roleName, ['ehs', 'manajer','user']))
                        <a href="{{ route('admin.progress-perbaikan.show', $laporan->id_laporan_lct) }}"
                            class="text-blue-600 hover:underline">
                            Detail
                        </a>
                    @else
                        <a href="{{ route('admin.manajemen-lct.show', $laporan->id_laporan_lct) }}"
                            class="text-blue-600 hover:underline">
                            Detail
                        </a>
                    @endif
                </td>
            </tr>
            @endforeach
            @endif

        </tbody>
    </table>
</div>
