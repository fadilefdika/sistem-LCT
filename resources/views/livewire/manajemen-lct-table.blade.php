<div class="overflow-x-auto bg-white p-6 shadow-sm rounded-xl">
    <input type="text" wire:model="search" placeholder="Cari laporan..." class="border p-2 mb-3 w-full rounded-md focus:ring focus:ring-blue-200">

    <div class="overflow-hidden rounded-lg border border-gray-200">
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
                @foreach($laporans as $index => $laporan)
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
                        <td class="px-6 py-4 text-gray-800">{{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->format('d M Y') }}</td>
                        <td class="px-6 py-4 border-b text-gray-800">
                            @php
                                $statusColors = [
                                    'in_progress' => 'bg-gray-500', // Gray for Not Started
                                    'progress_work' => 'bg-blue-500', // Blue for In Progress
                                    'waiting_approval' => 'bg-yellow-500', // Yellow for Pending Approval
                                    'approved' => 'bg-green-500', // Green for Approved
                                    'closed' => 'bg-purple-500', // Purple for Closed
                                    'revision' => 'bg-red-500', // Red for revision
                                ];
        
                                $statusLabels = [
                                    'in_progress' => 'Not Started',
                                    'progress_work' => 'In Progress',
                                    'waiting_approval' => 'Pending Approval',
                                    'approved' => 'Approved',
                                    'closed' => 'Closed',
                                    'revision' => 'Revision',
                                ];
                            @endphp
        
                            <span class="inline-block px-2 py-1 rounded-full text-white text-xs font-semibold {{ $statusColors[$laporan->status_lct] ?? 'bg-gray-300' }} whitespace-nowrap">
                                {{ $statusLabels[$laporan->status_lct] ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-800">
                            {{ $laporan->date_completion ? \Carbon\Carbon::parse($laporan->date_completion)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.manajemen-lct.show', $laporan->id_laporan_lct) }}" class="text-blue-600 hover:underline font-medium">
                                View Details
                            </a>
                        </td>
                    </tr>
                @endforeach
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
