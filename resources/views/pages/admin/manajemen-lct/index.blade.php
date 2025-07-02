<x-app-layout>
    <section class="p-3 sm:p-5">
        <div class="mx-auto max-w-screen-2xl">

            <div class="p-4 border rounded-xl shadow-sm bg-white mb-4 relative" x-data="{ showFilter: false }">

                <!-- Bar atas: Tombol Filter (kiri) dan Export (kanan) -->
                <div class="flex justify-between items-center mb-4">
                    <!-- Tombol Filter -->
                    <button @click="showFilter = !showFilter"
                        class="inline-flex items-center cursor-pointer gap-2 rounded-lg bg-black text-white sm:text-sm text-xs px-3 py-1.5 sm:px-4 sm:py-2 shadow hover:bg-gray-800 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 14.414V20a1 1 0 01-1.447.894l-4-2A1 1 0 019 18v-3.586L3.293 6.707A1 1 0 013 6V4z" />
                        </svg>
                        Filter
                    </button>
            
                    <!-- Tombol Export -->
                    <a href="{{ route('admin.manajemen-lct.export') }}" id="export-link"
                        class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 bg-green-500 text-white text-xs sm:text-sm font-medium rounded-lg shadow hover:bg-green-600 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 4v16c0 .55.45 1 1 1h14a1 1 0 0 0 1-1V4m-4 4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export Excel
                    </a>
                </div>
            
                <!-- Filter Form -->
                <div x-show="showFilter" x-transition x-cloak class="bg-white border border-gray-300 shadow rounded-lg p-4 mt-3 space-y-3">
                    <h2 class="text-xs font-semibold text-gray-800 mb-2">Filter Options</h2>
            
                    <form method="GET" action="{{ route('admin.manajemen-lct.index') }}"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
                        <!-- Date Range -->
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Date Range</label>
                            <input type="text" class="w-full rounded-md border-gray-200 text-xs p-2" name="daterange"
                                id="kt_daterangepicker_4" placeholder="All Time" autocomplete="off" />
                            <input type="hidden" name="tanggalAwal" id="tanggalAwal">
                            <input type="hidden" name="tanggalAkhir" id="tanggalAkhir">
                        </div>
            
                        <!-- Risk Level -->
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Hazard Level</label>
                            <select name="riskLevel" class="w-full rounded-md border-gray-200 text-xs p-2">
                                <option value="">All Levels</option>
                                <option value="Low" {{ request('riskLevel') == 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ request('riskLevel') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ request('riskLevel') == 'High' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
            
                        <!-- Status LCT -->
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">LCT Status</label>
                            <select name="statusLct" class="w-full rounded-md border-gray-200 text-xs p-2">
                                <option value="">All Statuses</option>
                                @foreach ($statusGroups as $label => $statuses)
                                    <option value="{{ implode(',', $statuses) }}"
                                        {{ request('statusLct') == implode(',', $statuses) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Category -->
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Category</label>
                            <select name="categoryId" class="w-full rounded-md border-gray-200 text-xs p-2">
                                <option value="">All Category</option>
                                @foreach ($categories as $id => $nama)
                                    <option value="{{ $id }}" {{ request('categoryId') == $id ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
            
                        <!-- Area -->
                        <div>
                            <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Area</label>
                            <select name="areaId" class="w-full rounded-md border-gray-200 text-xs p-2">
                                <option value="">All Areas</option>
                                @foreach ($areas as $id => $nama)
                                    <option value="{{ $id }}" {{ request('areaId') == $id ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
            
                        <!-- Tombol Action -->
                        <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-5">
                            <button type="submit"
                                class="px-3 py-1.5 text-xs font-medium rounded-md bg-black text-white cursor-pointer focus:outline-none">
                                Apply
                            </button>
                            <a href="{{ route('admin.manajemen-lct.index') }}"
                                class="px-3 py-1.5 text-xs font-medium rounded-md border border-gray-200 text-gray-600 hover:bg-gray-50 focus:outline-none">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            

            <div class="bg-white p-4 rounded-xl shadow">
                <div id="report-container-manajemen">
                    @include('partials.tabel-manajemen-lct-wrapper', ['laporans' => $laporans])
                </div>
            </div>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            // Default tidak ada range (All Time)
            function clearRange() {
                $("#kt_daterangepicker_4").val("All Time");
                $("#tanggalAwal").val('');
                $("#tanggalAkhir").val('');
            }

            function cb(start, end) {
                $("#kt_daterangepicker_4").val(start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY"));
                $("#tanggalAwal").val(start.format("YYYY-MM-DD"));
                $("#tanggalAkhir").val(end.format("YYYY-MM-DD"));
            }

            $("#kt_daterangepicker_4").daterangepicker({
                autoUpdateInput: false,
                opens: 'left',
                ranges: {
                    "Today": [moment(), moment()],
                    "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                    "Last 7 Days": [moment().subtract(6, "days"), moment()],
                    "Last 30 Days": [moment().subtract(29, "days"), moment()],
                    "This Month": [moment().startOf("month"), moment().endOf("month")],
                    "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
                }
            });

            // Set default ke All Time (kosong)
            clearRange();

            // Ketika user pilih range, update input dan hidden
            $("#kt_daterangepicker_4").on('apply.daterangepicker', function(ev, picker) {
                cb(picker.startDate, picker.endDate);
            });

            // Kalau user cancel, set ke All Time
            $("#kt_daterangepicker_4").on('cancel.daterangepicker', function(ev, picker) {
                clearRange();
            });
        });
    </script>
    
    <script>
        $(document).ready(function() {
        
            function fetchData(params = {}) {
                $.ajax({
                    url: "{{ route('admin.manajemen-lct.index') }}",
                    type: 'GET',
                    data: params,
                    success: function(res) {
                        $('#report-container-manajemen').html(res);
                        // Scroll ke atas tabel agar user tau data baru sudah dimuat
                        if (window.Alpine) {
                            Alpine.initTree(document.querySelector('#report-container-manajemen'));
                        }
                        $('html, body').animate({ scrollTop: $('#report-container-manajemen').offset().top - 100 }, 300);
                        updateExportLink(params);
                    },
                    error: function() {
                        alert('Gagal mengambil data.');
                    }
                });
            }

            // Fungsi untuk update href export Excel
            function updateExportLink(filters) {
                const queryString = new URLSearchParams(filters).toString();
                const exportUrl = "{{ route('admin.manajemen-lct.export') }}" + (queryString ? '?' + queryString : '');
                $('#export-link').attr('href', exportUrl);
            }
                    
            // Submit filter via AJAX
            $('form').on('submit', function(e) {
                e.preventDefault();

                let params = $(this).serializeArray().reduce((obj, item) => {
                    obj[item.name] = item.value;
                    return obj;
                }, {});

                params.perPage = $('#perPageSelect').val() || 10;

                fetchData(params);         // update tabel
                loadFindingData(params);   // update chart
                loadStatusChart(params);
                loadCategoryChart(params);
                loadAreaChart(params);
                loadDepartmentChart(params);
            });
                    
            // Handle pagination click (delegated event karena link dinamis)
            $(document).on('click', '#pagination-links a', function(e) {
                e.preventDefault();
        
                let url = new URL($(this).attr('href'), window.location.origin);
                let params = Object.fromEntries(url.searchParams.entries());
        
                // Ambil filter dari form juga supaya tetap konsisten
                let formParams = $('form').serializeArray().reduce((obj, item) => {
                    obj[item.name] = item.value;
                    return obj;
                }, {});
        
                // Gabungkan params
                params = {...formParams, ...params};
        
                fetchData(params);
            });
        
            // Ganti perPage lewat select dropdown
            $(document).on('change', '#perPageSelect', function() {
                let params = $('form').serializeArray().reduce((obj, item) => {
                    obj[item.name] = item.value;
                    return obj;
                }, {});
        
                params.perPage = $(this).val();
        
                fetchData(params);
            });
        
        });
    </script>
</x-app-layout>
