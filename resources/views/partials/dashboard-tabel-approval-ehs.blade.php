@php
    $bahayaTextColors = [
    'High' => 'text-red-600',
    'Medium' => 'text-yellow-600',
    'Low' => 'text-green-600'
];
@endphp

<div class="bg-white border border-gray-200 rounded-lg overflow-hidden divide-y divide-gray-200">
    @forelse($laporans as $laporan)
        @php
            $fullname = $laporan->picUser->fullname ?? '-';
            $nameParts = explode(' ', $fullname);
            $formattedName = count($nameParts) <= 2 
                ? $fullname 
                : $nameParts[0] . ' ' . $nameParts[1] . ' ' . collect(array_slice($nameParts, 2))->map(fn($n) => strtoupper(substr($n,0,1)))->implode(' ');

            $dueDate = \Carbon\Carbon::parse($laporan->due_date)->format('M d, Y');

            $link = route(
                in_array($roleName, ['ehs', 'manajer', 'user']) 
                    ? ($roleName === 'ehs' 
                        ? 'ehs.reporting.show' 
                        : 'admin.reporting.show') 
                    : 'admin.manajemen-lct.show', 
                $laporan->id_laporan_lct
            );

            $approvalType = match (true) {
                $laporan->tingkat_bahaya === 'Low' && $laporan->status_lct === 'waiting_approval' => 'permanent',
                in_array($laporan->tingkat_bahaya, ['Medium', 'High']) && $laporan->status_lct === 'waiting_approval_temporary' => 'temporary',
                in_array($laporan->tingkat_bahaya, ['Medium', 'High']) && $laporan->status_lct === 'waiting_approval_permanent' => 'permanent',
                default => '-',
            };
        @endphp

    <a href="{{ $link }}" class="grid grid-cols-[40px_1fr_1fr] gap-x-4 items-center p-4 hover:bg-gray-50 transition cursor-pointer rounded-md">
        <!-- No -->
        <div class="text-xs font-semibold text-gray-700 text-center">
            {{ $loop->iteration }}
        </div>

        <!-- Data Column 1 -->
        <div class="text-gray-700 space-y-1">
            <div class="text-[11px]">
                <span class="font-semibold">Due Date:</span> {{ $dueDate }}
            </div>
            <div>
                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $bahayaTextColors[$laporan->tingkat_bahaya] ?? 'text-gray-500' }}">
                    {{ $laporan->tingkat_bahaya }}
                </span>
            </div>            
        </div>

        <!-- Data Column 2 -->
        <div class="text-gray-700 space-y-1 truncate">
            <div class="text-xs">
                <span class="font-semibold">PIC:</span> <span class="truncate">{{ $formattedName }}</span>
            </div>
            <div class="text-xs">
                <span class="font-semibold">Type:</span> {{ $approvalType }}
            </div>
        </div>
    </a>

    @empty
        <div class="text-center py-16 text-gray-500">
            <i class="fa-solid fa-face-smile text-3xl mb-4"></i>
            <p class="text-xs font-medium">No data found non-conformities.</p>
        </div>
    @endforelse
</div>
