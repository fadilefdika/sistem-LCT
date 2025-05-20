@php
    $bahayaColors = [
        'High' => 'bg-red-500',
        'Medium' => 'bg-yellow-500',
        'Low' => 'bg-green-500'
    ];
@endphp

<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    @forelse($laporans as $laporan)
        @php
            $fullname = $laporan->picUser->fullname ?? '-';
            $nameParts = explode(' ', $fullname);

            if (count($nameParts) <= 2) {
                $formattedName = $fullname;
            } else {
                $formattedName = $nameParts[0] . ' ' . $nameParts[1];
                for ($i = 2; $i < count($nameParts); $i++) {
                    $formattedName .= ' ' . strtoupper(substr($nameParts[$i], 0, 1));
                }
            }

            $dueDate = \Carbon\Carbon::parse($laporan->due_date)->format('F d, Y');

            $link = route(
                in_array($roleName, ['ehs', 'manajer', 'user']) 
                    ? ($roleName === 'ehs' 
                        ? 'ehs.reporting.show' 
                        : 'admin.reporting.show') 
                    : 'admin.manajemen-lct.show', 
                $laporan->id_laporan_lct
            );
        @endphp

        <div class="flex items-center p-4 border-b border-gray-200 transition last:border-b-0">
            <!-- No -->
            <div class="w-8 text-center font-semibold text-gray-700">
                {{ $loop->iteration }}
            </div>

            <!-- Due Date -->
            <div class="w-40 text-sm text-gray-700 px-4">
                <div class="text-gray-400 text-xs">Due Date</div>
                <div class="font-medium text-xs">{{ $dueDate }}</div>
            </div>

            <!-- Hazard Level -->
            <div class="w-32 px-4">
                <div class="text-gray-400 text-xs mb-1">Hazard Level</div>
                <span class="inline-block px-2 py-[3px] text-xs font-semibold text-white rounded-full {{ $bahayaColors[$laporan->tingkat_bahaya] ?? 'bg-gray-400' }}">
                    {{ $laporan->tingkat_bahaya }}
                </span>
            </div>

            <!-- PIC -->
            <div class="flex-1 px-4">
                <div class="text-gray-400 text-xs">PIC</div>
                <div class="text-gray-800 font-medium truncate max-w-full text-xs">{{ $formattedName }}</div>
            </div>

            <!-- Action -->
            <div class="w-28 text-right">
                <a href="{{ $link }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View Details →
                </a>
            </div>
        </div>

    @empty
        <div class="text-center py-16 text-gray-500">
            <i class="fa-solid fa-face-smile text-3xl mb-4"></i>
            <p class="text-sm font-medium">No data found non-conformities.</p>
        </div>
    @endforelse
</div>
