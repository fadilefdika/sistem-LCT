<h2 class="text-xl font-bold mb-4">{{$title}}</h2>
                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-300 shadow-sm border border-gray-200 rounded-lg overflow-hidden">
                                <thead class="bg-gray-100">
                                    <tr class="text-left text-sm font-semibold text-gray-600">
                                        <th class="px-4 py-3">No</th>
                                        <th class="px-4 py-3">Non-Conformity Findings</th>
                                        <th class="px-4 py-3">PIC Name</th>
                                        <th class="px-4 py-3">Risk Level</th>
                                        <th class="px-4 py-3">Progress Status</th>
                                        <th class="px-4 py-3">Due Date</th>
                                        <th class="px-4 py-3">Completion Date</th>
                                        <th class="px-4 py-3">Action</th>
                                    </tr>                
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @if($laporans->isEmpty())
                                        <tr>
                                            <td colspan="8" class="px-4 py-3 text-center text-gray-500">No data available</td>
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
                                                    // Default status color
                                                    $statusColors = [
                                                        'open' => 'bg-gray-500',
                                                        'review' => 'bg-purple-500',
                                                        'in_progress' => 'bg-gray-500',
                                                        'progress_work' => 'bg-yellow-500',
                                                        'waiting_approval' => 'bg-blue-500',
                                                        'approved' => 'bg-green-500',
                                                        'closed' => 'bg-green-700',
                                                        'revision' => 'bg-red-500'
                                                    ];
                        
                                                    // Add status for Medium & High
                                                    if ($laporan->tingkat_bahaya === 'Medium' || $laporan->tingkat_bahaya === 'High') {
                                                        $statusColors = array_merge($statusColors, [
                                                            'waiting_approval_temporary' => 'bg-blue-500',
                                                            'approved_temporary' => 'bg-green-500',
                                                            'temporary_revision' => 'bg-red-500',
                                                            'work_permanent' => 'bg-yellow-500',
                                                            'waiting_approval_permanent' => 'bg-blue-500',
                                                            'approved_permanent' => 'bg-green-500',
                                                            'permanent_revision' => 'bg-red-500'
                                                        ]);
                                                    }
                                                @endphp
                        
                                                <span class="inline-flex items-center justify-center px-3 py-1 text-[10px] font-semibold text-white rounded-full
                                                    {{ $statusColors[$laporan->status_lct] ?? 'bg-gray-400' }} whitespace-nowrap">
                                                    {{ ucwords(str_replace('_', ' ', $laporan->status_lct)) }}
                                                </span>
                                            </td>
                        
                                            <!-- Tenggat Waktu -->
                                            <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                                                {{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->format('F d, Y') }}
                                            </td>
                        
                                            <!-- Tanggal Selesai / Overdue -->
                                            <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                                                @if ($laporan->date_completion)
                                                    {{ \Carbon\Carbon::parse($laporan->date_completion)->format('F d, Y') }}
                                                @else
                                                    @php
                                                        $dueDate = \Carbon\Carbon::parse($laporan->tenggat_waktu)->startOfDay();
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
                        
                                            <!-- Tombol Aksi -->
                                            <td class="px-4 py-3 flex items-center gap-2 w-28">
                                                <a href="{{ route('admin.progress-perbaikan.show', $laporan->id_laporan_lct) }}"
                                                    class="text-blue-600 hover:underline">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>