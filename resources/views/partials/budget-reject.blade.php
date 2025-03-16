<div class="bg-red-50 p-6 rounded-lg shadow-md border-l-4 border-red-600 mb-6">
    <p class="text-sm text-gray-700 font-semibold">Budget Revision</p>

    @php
        $rejectLaporan = $rejectLaporan->sortByDesc('created_at')->values(); // Urutkan & reset indeks
    @endphp

    <div x-data="{ expanded: false }" class="relative">
        <!-- Tampilan utama: Hanya reject terbaru -->
        <div class="cursor-pointer bg-white p-4 rounded-lg shadow-md flex justify-between items-center border border-gray-300"
             @click="expanded = !expanded">
            <div>
                <p class="text-sm text-gray-700">
                    <strong class="text-gray-800">Revision on:</strong>
                    <span class="text-red-600">{{ $rejectLaporan[0]->created_at->format('F j, Y h:i A') }}</span>
                </p>
                <p class="text-sm text-gray-700 mt-2">
                    <strong class="text-gray-800">Reason:</strong>
                    <span>{{ Str::limit($rejectLaporan[0]->alasan_reject, 50) }}</span>
                </p>
            </div>
            <svg :class="expanded ? 'rotate-180' : 'rotate-0'" class="w-5 h-5 text-gray-500 transition-transform duration-300"
                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        <!-- Dropdown: Semua reject -->
        <div x-show="expanded" @click.outside="expanded = false" x-cloak
            class="absolute w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-2 z-10 p-4 
                transition-all duration-300 ease-in-out transform origin-top"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2">

            @foreach($rejectLaporan as $index => $reject)
            <div class="p-4 transition duration-200 hover:bg-gray-100 
                        {{ $index !== 0 ? 'border-t border-gray-300' : '' }}">
                <p class="text-sm text-gray-700">
                    <strong class="text-gray-800">Revision on:</strong>
                    <span class="text-red-600">{{ $reject->created_at->format('F j, Y h:i A') }}</span>
                </p>
                <p class="text-sm text-gray-700 mt-2">
                    <strong class="text-gray-800">Reason:</strong>
                    <span>{{ $reject->alasan_reject }}</span>
                </p>
            </div>
            @endforeach
        </div>


    </div>
</div>
