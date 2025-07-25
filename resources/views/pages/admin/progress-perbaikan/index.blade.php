<x-app-layout>
  <section class="p-3">
    <div class="mx-auto max-w-screen-2xl">
        <div class="p-2 relative">
            
        <div class="overflow-visible">
            <div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
                @php
                        if (Auth::guard('ehs')->check()) {
                            $user = Auth::guard('ehs')->user();
                            $roleName = 'ehs';
                        } else {
                            $user = Auth::guard('web')->user();
                            // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
                            $roleName = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
                        }
                    $routePrefix = $roleName === 'ehs' ? 'ehs' : 'admin';
                @endphp

                <div x-data="{ showFilter: false }" class="mb-5">

                    <!-- Top Bar -->
                    <div class="flex justify-between items-center flex-wrap gap-3">
                        <!-- Filter Button -->
                        <div>
                            <button @click="showFilter = !showFilter"
                            class="inline-flex items-center cursor-pointer gap-2 rounded-lg bg-black text-white text-sm px-4 py-2 shadow hover:bg-gray-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 14.414V20a1 1 0 01-1.447.894l-4-2A1 1 0 019 18v-3.586L3.293 6.707A1 1 0 013 6V4z" />
                            </svg>
                            Filter
                            </button>
                        </div>
                    
                        <!-- Export Button -->
                        @if(in_array($roleName, ['ehs', 'manajer']))
                            <div>
                                @if($roleName === 'ehs')
                                    <a href="{{ route('ehs.reporting.export-ppt') }}" id="export-ppt-link" 
                                        class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg shadow hover:bg-green-600 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M4 4v16c0 .55.45 1 1 1h14a1 1 0 0 0 1-1V4m-4 4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Export PPT
                                    </a>
                                    <a href="{{ route('ehs.reporting.export', request()->query()) }}" id="export-link" 
                                        class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg shadow hover:bg-green-600 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M4 4v16c0 .55.45 1 1 1h14a1 1 0 0 0 1-1V4m-4 4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Export Excel
                                    </a>
                                @elseif($roleName === 'manajer')
                                    <a href="{{ route('admin.reporting.export',request()->query()) }}" id="export-link" 
                                        class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg shadow hover:bg-green-600 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M4 4v16c0 .55.45 1 1 1h14a1 1 0 0 0 1-1V4m-4 4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Export Excell
                                    </a>
                                @endif
                            </div>
                        @endif

                    </div>
                
                    <!-- Filter Form Section (Hidden by default) -->
                    <div x-show="showFilter" x-transition x-cloak class="bg-white border border-gray-300 shadow rounded-lg p-4 mt-3 space-y-4">

                        <h2 class="text-sm font-semibold text-gray-800">Filter Options</h2>
                        
                        <form method="GET" action="{{ route($routePrefix . '.reporting.index') }}"
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">

                            <!-- Date Range -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Date Range</label>
                                <input type="text" name="daterange" id="kt_daterangepicker_4"
                                    class="w-full rounded-md border-gray-300 text-xs p-2"
                                    placeholder="All Time" autocomplete="off">
                                <input type="hidden" name="tanggalAwal" id="tanggalAwal">
                                <input type="hidden" name="tanggalAkhir" id="tanggalAkhir">
                            </div>                       

                            <!-- Hazard Level -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Hazard Level</label>
                                <select name="riskLevel" class="w-full rounded-md border-gray-300 text-xs p-2">
                                    <option value="">All Levels</option>
                                    <option value="Low" {{ request('riskLevel') == 'Low' ? 'selected' : '' }}>Low</option>
                                    <option value="Medium" {{ request('riskLevel') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="High" {{ request('riskLevel') == 'High' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>

                            <!-- LCT Status -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">LCT Status</label>
                                <select name="statusLct" class="w-full rounded-md border-gray-300 text-xs p-2">
                                    <option value="">All Statuses</option>
                                    @foreach ($statusGroups as $label => $statuses)
                                        <option value="{{ implode(',', $statuses) }}"
                                            {{ request('statusLct') == implode(',', $statuses) ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Department -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Department</label>
                                <select name="departemenId" class="w-full rounded-md border-gray-300 text-xs p-2">
                                    <option value="">All Depts</option>
                                    @foreach ($departments as $id => $nama)
                                        <option value="{{ $id }}" {{ request('departemenId') == $id ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                                <select name="categoryId" class="w-full rounded-md border-gray-300 text-xs p-2">
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
                                <label class="block text-xs font-medium text-gray-500 mb-1">Area</label>
                                <select name="areaId" class="w-full rounded-md border-gray-300 text-xs p-2">
                                    <option value="">All Areas</option>
                                    @foreach ($areas as $id => $nama)
                                        <option value="{{ $id }}" {{ request('areaId') == $id ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-4 xl:col-span-5">
                                <button type="submit"
                                    class="px-4 py-2 text-xs font-medium rounded-md bg-black text-white hover:bg-gray-900 focus:outline-none">
                                    Apply
                                </button>
                                <a href="{{ route($routePrefix . '.reporting.index') }}"
                                    class="px-4 py-2 text-xs font-medium rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 focus:outline-none">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


      @php
          $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
          $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');          
      @endphp

      @if($roleName === 'ehs')
        <div class="mb-3">
            <h2 class="text-base font-bold mb-4">Advanced Report</h2>

            <div class="space-y-4">
                <!-- Baris 1: Total Findings dan Findings by Status -->
                <div class="flex flex-col xl:flex-row gap-4">
                    <!-- Total Findings -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] xl:w-2/3 w-full">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-semibold">Total Findings</h3>
                        </div>
                        <div class="h-[200px]">
                            <canvas id="findingChart"></canvas>
                        </div>
                    </div>
            
                    <!-- Findings by Status -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] xl:basis-1/3 basis-full flex-shrink-0 min-w-0">
                        <div class="mb-2">
                            <h3 class="text-sm font-semibold mb-2">Findings by Status</h3>
                        </div>
                        <div class="h-[200px]">
                            <canvas id="statusChart" class="w-full"></canvas>
                        </div>
                    </div>
                </div>
            
                <!-- Baris 2: Findings by Category dan Area -->
                <div class="flex flex-col xl:flex-row gap-4">
                    <!-- Findings by Category -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] xl:w-1/3 w-full">
                        <h3 class="text-sm font-semibold mb-2">Findings by Category</h3>
                        <div class="h-[200px]">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                    
            
                    <!-- Findings by Area -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] overflow-x-auto xl:w-2/3 w-full">
                        <h3 class="text-sm font-semibold mb-2">Findings by Area</h3>
                        <div class="h-[200px]"><!-- beri min-width agar scroll muncul jika area banyak -->
                            <canvas id="areaChart"></canvas>
                        </div>
                    </div>                    
                </div>
            
                <!-- Baris 3: Department dan Overdue -->
                <div class="flex flex-col xl:flex-row gap-4">
                    <!-- Department -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] overflow-x-auto xl:w-2/3 w-full">
                        <h3 class="text-sm font-semibold mb-2">Findings by Department</h3>
                        <div class="h-[200px]">
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      @endif

    <div class="bg-white p-4 rounded-xl shadow">
        <div id="report-container-report">
            @include('partials.tabel-reporting-wrapper', ['laporans' => $laporans])
        </div>
    </div>
    
  </section>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

  <script>
    function confirmClose(id) {
        Swal.fire({
            title: 'Are you sure you want to close this report?',
            text: "The report will be marked as closed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, close it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Ambil action dan token dari form
                const form = document.getElementById(`form-close-${id}`);
                const action = form.getAttribute('action');
                const csrf = form.querySelector('input[name="_token"]').value;

                fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (!res.ok) throw new Error("Failed");
                    return res.json(); // opsional, tergantung response controller kamu
                })
                .then(data => {
                    Swal.fire({
                        title: 'Closed!',
                        text: 'The report has been successfully closed.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload(); // Refresh halaman setelah modal sukses ditutup
                    });
                })
                .catch(err => {
                    Swal.fire('Error', 'Failed to close the report.', 'error');
                });
            }
        });
    }

</script>


    <script src="{{ asset('js/charts.js') }}"></script>

    @php
        $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
        $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');
    @endphp

    <script>
        const userRole = "{{ $roleName }}";

        let baseUrl;
        if (userRole === 'ehs') {
            baseUrl = "{{ route('ehs.reporting.paginated') }}";
        } else if (userRole === 'manajer') {
            baseUrl = "{{ route('admin.reporting.paginated') }}";
        }

        document.getElementById('perPageSelect').addEventListener('change', function () {
            const perPage = this.value;
            fetch(`${baseUrl}?perPage=${perPage}`)
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    document.getElementById('laporanTableBody').innerHTML = data.data;
                })
                .catch(error => {
                    console.error('Gagal mengambil data laporan:', error);
                });
        });
    </script>

    <script>
        $(document).ready(function () {
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
                    "Last 7 Days": [moment().subtract(6, "days"), moment()],
                    "This Month": [moment().startOf("month"), moment().endOf("month")],
                    "Last 6 Months": [moment().subtract(6, "months").startOf("day"), moment()],
                    "Last 1 Year": [moment().subtract(1, "years").startOf("day"), moment()],
                    "Last 2 Years": [moment().subtract(2, "years").startOf("day"), moment()]
                },
                locale: {
                    cancelLabel: 'Clear',
                    applyLabel: 'Apply'
                }
            });


            clearRange();

            $("#kt_daterangepicker_4").on('apply.daterangepicker', function (ev, picker) {
                cb(picker.startDate, picker.endDate);
            });

            $("#kt_daterangepicker_4").on('cancel.daterangepicker', function (ev, picker) {
                clearRange();
            });
        });
    </script>

    
    <script>
        $(document).ready(function() {
        
            function fetchData(params = {}) {
                $.ajax({
                    url: "{{ route($routePrefix . '.reporting.index') }}",
                    type: 'GET',
                    data: params,
                    success: function(res) {
                        $('#report-container-report').html(res);
                        // Scroll ke atas tabel agar user tau data baru sudah dimuat
                        if (window.Alpine) {
                            Alpine.initTree(document.querySelector('#report-container-report'));
                        }
                        $('html, body').animate({ scrollTop: $('#report-container-report').offset().top - 100 }, 300);
                        updateExportLink(params);
                        updateExportPptLink(params);
                    },
                    error: function() {
                        alert('Gagal mengambil data.');
                    }
                });
            }

            // Fungsi untuk update href export Excel
            const exportBaseUrl = "{{ route($routePrefix . '.reporting.export') }}";

            function updateExportLink(filters) {
                const queryString = new URLSearchParams(filters).toString();
                const exportUrl = exportBaseUrl + (queryString ? '?' + queryString : '');
                $('#export-link').attr('href', exportUrl);
            }

            function updateExportPptLink(filters) {
                const queryString = new URLSearchParams(filters).toString();
                const exportPptUrl = "{{ route('ehs.reporting.export-ppt') }}" + (queryString ? '?' + queryString : '');
                $('#export-ppt-link').attr('href', exportPptUrl);
            }

                    
            // Submit filter via AJAX
            $('form').on('submit', function(e) {
                e.preventDefault();

                let params = $(this).serializeArray().reduce((obj, item) => {
                    obj[item.name] = item.value;
                    return obj;
                }, {});

                // Tambahkan perPage jika ada
                params.perPage = $('#perPageSelect').val() || 10;

                // Hitung durasi range tanggal untuk tentukan groupBy
                const start = moment(params.tanggalAwal);
                const end = moment(params.tanggalAkhir);
                const monthsDiff = end.diff(start, 'months', true);

                if (monthsDiff >= 6) {
                    params.groupBy = 'month'; // jika lebih dari atau sama dengan 6 bulan
                } else {
                    params.groupBy = 'date'; // default
                }

                // Kirim ke semua chart & tabel
                fetchData(params);
                loadStatusChart(params);
                loadCategoryChart(params);
                loadAreaChart(params);
                loadDepartmentChart(params);
                loadFindingData(params);
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
