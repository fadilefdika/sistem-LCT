<div x-data="{ openRow: null }">
    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
        {{-- <select id="perPageSelect" class="border border-gray-300 text-sm rounded p-1">
            <option value="10" @if(request('perPage') == 10) selected @endif>10 baris</option>
            <option value="15" @if(request('perPage') == 15) selected @endif>15 baris</option>
            <option value="25" @if(request('perPage') == 25) selected @endif>25 baris</option>
            <option value="50" @if(request('perPage') == 50) selected @endif>50 baris</option>
        </select> --}}
        
        <table class="w-full min-w-full divide-y divide-gray-300 shadow-sm border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr class="text-left text-xs font-semibold text-gray-600">
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3 whitespace-nowrap">Finding Date</th>
                    <th class="px-4 py-3">Due Date</th>
                    <th class="px-4 py-3">PIC</th>
                    <th class="px-4 py-3">Hazard Level</th>
                    <th class="px-4 py-3">Progress Status</th>
                </tr>                 
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @include('partials.tabel-reporting', ['laporans' => $laporans])
            </tbody>
        </table>
    </div>

    <div class="flex items-center justify-between mt-6 px-2">
        <div class="text-sm text-gray-600">
            Showing
            <span class="font-semibold">{{ $laporans->firstItem() }}</span>
            to
            <span class="font-semibold">{{ $laporans->lastItem() }}</span>
            of
            <span class="font-semibold">{{ $laporans->total() }}</span>
            results
        </div>

        <div id="pagination-links">
            {{ $laporans->withQueryString()->links() }}
        </div>
    </div>
</div>
