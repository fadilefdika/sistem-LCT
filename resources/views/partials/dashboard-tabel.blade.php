@php
    $bahayaTextColors = [
        'High' => 'text-red-600',
        'Medium' => 'text-yellow-600',
        'Low' => 'text-green-600'
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

<a href="{{ $link }}" class="block p-3 border-b border-gray-200 last:border-b-0 transition hover:bg-gray-50">
    <!-- Mobile Layout (2 rows) -->
    <div class="sm:hidden">
        <!-- Row 1 -->
        <div class="flex items-center gap-4 mb-2">
            <!-- No -->
            <div class="w-8 text-center font-semibold text-gray-700 text-xs">
                {{ $loop->iteration }}
            </div>

            <!-- Due Date -->
            <div class="flex-1 text-sm text-gray-700">
                <div class="text-xs text-gray-400">Due Date</div>
                <div class="text-xs font-medium">{{ $dueDate }}</div>
            </div>

            <!-- Hazard Level -->
            <div class="text-right">
                <div class="text-[11px] font-medium text-gray-500 mb-1 tracking-wide uppercase">Hazard Level</div>
                <span class="inline-block px-2 py-[2px] rounded-full text-xs font-semibold {{ $bahayaTextColors[$laporan->tingkat_bahaya] ?? 'text-gray-500' }}">
                    {{ $laporan->tingkat_bahaya }}
                </span>
            </div>
        </div>

        <!-- Row 2 -->
        <div class="flex items-center gap-4">
            <div class="w-8"></div>
            <div class="flex-1">
                <div class="text-xs text-gray-400">PIC</div>
                <div class="text-xs font-medium text-gray-800 truncate">
                    {{ $formattedName }}
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop Layout (1 row) -->
    <div class="hidden sm:flex items-center">
        <!-- No -->
        <div class="w-8 text-center font-semibold text-gray-700 text-xs">
            {{ $loop->iteration }}
        </div>

        <!-- Due Date -->
        <div class="w-40 px-4 text-sm text-gray-700">
            <div class="text-xs text-gray-400">Due Date</div>
            <div class="text-xs font-medium">{{ $dueDate }}</div>
        </div>

        <!-- Hazard Level -->
        <div class="w-32 px-4">
            <div class="text-[11px] font-medium text-gray-500 mb-1 tracking-wide uppercase">Hazard Level</div>
            <span class="inline-block px-2 py-[2px] rounded-full text-xs font-semibold {{ $bahayaTextColors[$laporan->tingkat_bahaya] ?? 'text-gray-500' }}">
                {{ $laporan->tingkat_bahaya }}
            </span>
        </div>

        <!-- PIC -->
        <div class="flex-1 px-4">
            <div class="text-xs text-gray-400">PIC</div>
            <div class="text-xs font-medium text-gray-800 truncate">
                {{ $formattedName }}
            </div>
        </div>
    </div>
</a>

    @empty
        <div class="text-center py-16 text-gray-500">
            <i class="fa-solid fa-face-smile text-3xl mb-4"></i>
            <p class="text-sm font-medium">No data found non-conformities.</p>
        </div>
    @endforelse
</div>
