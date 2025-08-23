<div x-data="{ openRow: null }" class="p-4">
    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Findings Report</h2>
        <p class="text-sm text-gray-500 mt-1">Comprehensive list of reported findings</p>
    </div>

    @php
        // Tentukan user dan role
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $roleName = 'ehs';
        } elseif (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            // Ambil role dari session dulu, jika tidak ada fallback ke relasi user
            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        } else {
            $user = null;
            $roleName = 'guest';
        }

        // Tentukan prefix route
        $routePrefix = $roleName === 'ehs' ? 'ehs' : 'admin';
    @endphp

    <!-- Search Form -->
    <form method="GET" action="{{ route($routePrefix.'.reporting.index') }}" class="flex items-center gap-2 mb-4">
        <input 
            type="text" 
            name="id_laporan_lct" 
            placeholder="Cari ID Laporan"
            value="{{ request('id_laporan_lct') }}"
            class="w-52 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-gray-400 text-xs text-gray-700 bg-white"
        >

        <button 
            type="submit" 
            class="px-3 py-1 bg-gray-800 text-white text-xs rounded hover:bg-gray-700 transition"
        >
            Cari
        </button>

        @php
            $isThisMonth = request('tanggalAwal') === now()->startOfMonth()->format('Y-m-d')
                && request('tanggalAkhir') === now()->endOfMonth()->format('Y-m-d');
        @endphp

        @if(request()->has('id_laporan_lct') || $isThisMonth)
            <a 
                href="{{ route($routePrefix.'.reporting.index') }}" 
                class="px-3 py-1 bg-gray-200 text-gray-700 text-xs rounded hover:bg-gray-300 transition"
            >
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

        @php
            function sortLink($label, $column) {
                $currentSort = request('sort_by');
                $currentOrder = request('sort_order', 'asc');
                $newOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';

                // Default icon (both up/down)
                $iconDefault = '
                    <svg class="w-3 h-3 ml-1 inline-block text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 01.832.445l4 6a1 1 0 01-.832 1.555H6a1 1 0 01-.832-1.555l4-6A1 1 0 0110 3zm0 14a1 1 0 01-.832-.445l-4-6A1 1 0 016 9h8a1 1 0 01.832 1.555l-4 6A1 1 0 0110 17z" clip-rule="evenodd" />
                    </svg>';

                // Icon ascending
                $iconAsc = '
                    <svg class="w-3 h-3 ml-1 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M5 15l7-7 7 7"/>
                    </svg>';

                // Icon descending
                $iconDesc = '
                    <svg class="w-3 h-3 ml-1 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>';

                // Pilih icon berdasarkan kondisi
                if ($currentSort === $column) {
                    $icon = $currentOrder === 'asc' ? $iconAsc : $iconDesc;
                } else {
                    $icon = $iconDefault;
                }

                $query = request()->except(['sort_by', 'sort_order']);
                $query['sort_by'] = $column;
                $query['sort_order'] = $newOrder;
                $url = url()->current() . '?' . http_build_query($query);

                return "<a href=\"$url\" class=\"inline-flex items-center gap-1 text-gray-500\">$label $icon</a>";
            }
        @endphp


        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortLink('Finding Date', 'finding_date') !!}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortLink('Due Date', 'due_date') !!}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortLink('PIC', 'pic_name') !!}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortLink('Hazard Level', 'tingkat_bahaya') !!}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{!! sortLink('Progress Status', 'progress_status') !!}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @include('partials.tabel-reporting', ['laporans' => $laporans])
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


@php
    $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
    $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');
@endphp

<script>
    const userRole = "{{ $roleName }}";

    // Tentukan baseUrl sesuai role
    const baseUrl = userRole === 'ehs'
        ? "{{ route('ehs.reporting.index') }}"
        : "{{ route('admin.reporting.index') }}";

    document.addEventListener('DOMContentLoaded', () => {
        const perPageSelect = document.getElementById('perPageSelect');
        const wrapper = document.getElementById('report-container-report');

        // // Handler untuk perubahan jumlah data per halaman
        // if (perPageSelect) {
        //     perPageSelect.addEventListener('change', () => {
        //         const perPage = perPageSelect.value;
        //         const url = `${baseUrl}?perPage=${perPage}`;
        //         fetch(url, {
        //             headers: {
        //                 'X-Requested-With': 'XMLHttpRequest'
        //             }
        //         })
        //         .then(response => response.text())
        //         .then(html => {
        //             wrapper.innerHTML = html;
        //         });
        //     });
        // }

        // Handler untuk klik sorting
        document.addEventListener('click', function (e) {
            const target = e.target.closest('a');
            if (target && target.href.includes('sort_by=')) {
                e.preventDefault();
                console.log('Fetching sorted data from:', target.href); // Log URL yang difetch

                fetch(target.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Fetch status:', response.status); // Log status response
                    return response.text();
                })
                .then(html => {
                    wrapper.innerHTML = html;
                    console.log('Sorting applied and content updated.');
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                });
            }
        });
    });
</script>

