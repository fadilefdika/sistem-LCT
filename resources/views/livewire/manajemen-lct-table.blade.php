<div class="overflow-x-auto bg-white p-6 shadow-sm rounded-xl">
    
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-300 shadow-sm border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-100">
                <tr class="text-left text-sm font-semibold text-gray-700">
                    <th scope="col" class="px-4 py-3">No</th>
                    <th scope="col" class="px-4 py-3">Non-Conformity Findings</th>
                    <th scope="col" class="px-4 py-3">Area Details</th>
                    <th scope="col" class="px-4 py-3">Risk Level</th>
                    <th scope="col" class="px-4 py-3">Due Date</th>
                    <th scope="col" class="px-4 py-3">Progress Status</th>
                    <th scope="col" class="px-4 py-3">Completion Date</th>
                    <th scope="col" class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($laporans as $index => $laporan)
                    <tr class="hover:bg-gray-100 text-sm transition duration-200 ease-in-out">
                        <td class="px-6 py-4 text-gray-800">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $laporan->temuan_ketidaksesuaian }}</td>
                        <td class="px-6 py-4 text-gray-800">{{ $laporan->area }} - {{ $laporan->detail_area }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-white text-xs font-semibold
                                {{ $laporan->tingkat_bahaya === 'High' ? 'bg-red-600' : ($laporan->tingkat_bahaya === 'Medium' ? 'bg-yellow-500' : 'bg-green-500') }}">
                                {{ $laporan->tingkat_bahaya }}
                            </span>
                        </td>
                        <!-- Tenggat Waktu -->
                        <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->format('F d, Y') }}
                        </td>
                        <td class="px-6 py-4 border-b text-gray-800">
                            @php
                                $statusColors = [
                                    'in_progress' => 'bg-gray-500', // Gray for Not Started
                                    'progress_work' => 'bg-blue-500', // Blue for In Progress
                                    'waiting_approval' => 'bg-yellow-500', // Yellow for Pending Approval
                                    'approved' => 'bg-green-500', // Green for Approved
                                    'closed' => 'bg-purple-500', // Purple for Closed
                                    'revision' => 'bg-red-500', // Red for revision
                                    'waiting_approval_temporary' => 'bg-yellow-600', // Yellow for Temporary Waiting Approval
                                    'approved_temporary' => 'bg-green-600', // Green for Temporary Approved
                                    'temporary_revision' => 'bg-red-600', // Red for Temporary Revision
                                    'work_permanent' => 'bg-blue-600', // Blue for Permanent Work
                                    'waiting_approval_permanent' => 'bg-yellow-700', // Yellow for Permanent Waiting Approval
                                    'approved_permanent' => 'bg-green-700', // Green for Permanent Approved
                                    'permanent_revision' => 'bg-red-700', // Red for Permanent Revision
                                ];
                        
                                $statusLabels = [
                                    'in_progress' => 'Not Started',
                                    'progress_work' => 'In Progress',
                                    'waiting_approval' => 'Waiting Approval',
                                    'approved' => 'Approved',
                                    'closed' => 'Closed',
                                    'revision' => 'Revision',
                                    'waiting_approval_temporary' => 'Waiting Approval (Temporary)',
                                    'approved_temporary' => 'Approved (Temporary)',
                                    'temporary_revision' => 'Revision (Temporary)',
                                    'work_permanent' => 'Work (Permanent)',
                                    'waiting_approval_permanent' => 'Waiting Approval (Permanent)',
                                    'approved_permanent' => 'Approved (Permanent)',
                                    'permanent_revision' => 'Revision (Permanent)',
                                ];
                            @endphp
                        
                            <span class="inline-block px-2 py-1 rounded-full text-white text-xs font-semibold {{ $statusColors[$laporan->status_lct] ?? 'bg-gray-300' }} whitespace-nowrap">
                                {{ $statusLabels[$laporan->status_lct] ?? 'Unknown' }}
                            </span>
                        </td>
                        
                        <!-- Completion Date -->
                        <td class="px-4 py-3 text-gray-800 w-32 whitespace-nowrap">
                            {{ $laporan->date_completion ? \Carbon\Carbon::parse($laporan->date_completion)->format('F d, Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.manajemen-lct.show', $laporan->id_laporan_lct) }}" class="text-blue-600 hover:underline font-medium">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-6 text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6">
                                    </path>
                                </svg>
                                <p class="text-sm">No reports are available.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
            </tbody>
        </table>
        
    </div>

    <div class="mt-6 flex justify-between items-center border-t px-5 py-3">
        <span class="text-sm text-gray-600">
            Showing {{ $laporans->firstItem() }} to {{ $laporans->lastItem() }} of {{ $laporans->total() }} entries
        </span>
        <div>
            {{ $laporans->links('pagination::tailwind') }}
        </div>
    </div>
</div>
