<div class="p-4 border rounded-xl shadow-sm bg-white mb-4 relative" id="report-container-followup">
    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Findings Report</h2>
        <p class="text-sm text-gray-500 mt-1">Comprehensive list of reported findings</p>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('admin.manajemen-lct.index') }}" class="flex items-center gap-2 mb-4">
        <input type="text" 
            name="id_laporan_lct" 
            placeholder="Cari ID Laporan"
            value="{{ request('id_laporan_lct') }}"
            class="w-52 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-gray-400 text-xs text-gray-700 bg-white">

        <button type="submit" 
                class="px-3 py-1 bg-gray-800 text-white text-xs rounded hover:bg-gray-700 transition">
            Cari
        </button>

        @if(request()->has('id_laporan_lct'))
            <a href="{{ route('admin.manajemen-lct.index') }}" 
            class="px-3 py-1 bg-gray-200 text-gray-700 text-xs rounded hover:bg-gray-300 transition">
                Reset
            </a>
        @endif
    </form>

    <!-- Table Container -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- Table Controls -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <label for="perPageSelect" class="sr-only">Items per page</label>
                    <select id="perPageSelect" class="appearance-none bg-white border border-gray-300 text-gray-700 py-2 px-3 pr-8 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="10" @if(request('perPage') == 10) selected @endif>10 per page</option>
                        <option value="15" @if(request('perPage') == 15) selected @endif>15 per page</option>
                        <option value="25" @if(request('perPage') == 25) selected @endif>25 per page</option>
                        <option value="50" @if(request('perPage') == 50) selected @endif>50 per page</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finding Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hazard Level</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @include('partials.tabel-finding-followup', ['laporans' => $laporans])
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="text-sm text-gray-600">
                Showing
                <span class="font-medium">{{ $laporans->firstItem() }}</span>
                to
                <span class="font-medium">{{ $laporans->lastItem() }}</span>
                of
                <span class="font-medium">{{ $laporans->total() }}</span>
                results
            </div>

            <div id="pagination-links" class="flex space-x-2">
                {{ $laporans->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
