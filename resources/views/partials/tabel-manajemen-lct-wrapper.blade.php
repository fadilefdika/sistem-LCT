<div x-data="{ openRow: null }">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Findings Report</h2>
        <p class="text-sm text-gray-500 mt-1">Comprehensive list of reported findings</p>
    </div>
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

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-300 shadow-sm border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr class="text-left text-sm font-semibold text-gray-700">
                        <th scope="col" class="px-1 py-3">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Date</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Area Details</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hazard Level</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @include('partials.tabel-manajemen-lct', ['laporans' => $laporans])
                </tbody>
            </table>
            
        </div>

        <div class="flex items-center justify-between mt-6 px-2">
            <!-- Left side: Showing info -->
            <div class="text-sm text-gray-600">
                Showing
                <span class="font-semibold">{{ $laporans->firstItem() }}</span>
                to
                <span class="font-semibold">{{ $laporans->lastItem() }}</span>
                of
                <span class="font-semibold">{{ $laporans->total() }}</span>
                results
            </div>

            <!-- Right side: Pagination -->
            <div id="pagination-links" class="flex space-x-2">
                {{ $laporans->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const perPageSelect = document.getElementById('perPageSelect');
        const wrapper = document.getElementById('report-container-manajemen');
    
        perPageSelect.addEventListener('change', () => {
            const perPage = perPageSelect.value;
            fetch(`{{ route('admin.manajemen-lct.index') }}?perPage=${perPage}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                wrapper.innerHTML = html;
            });
        });
    });
</script>
    