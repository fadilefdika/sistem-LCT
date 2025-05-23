<x-app-layout>
  <section class="p-3">
    <div class="mx-auto max-w-screen-2xl">
      <div class="p-2 relative">
          
      <div class="overflow-visible">
          <div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
              @php
                  if (Auth::guard('ehs')->check()) {
                      $user = Auth::guard('ehs')->user();
                      $roleName = optional($user->roles->first())->name ?? 'Tidak Ada Role';
                  } else {
                      $user = Auth::user();
                      $roleName = optional($user->roleLct->first())->name ?? 'Tidak Ada Role';
                  }
                  $routePrefix = $roleName === 'ehs' ? 'ehs' : 'admin';
              @endphp

              <div class="flex justify-between items-center flex-wrap gap-3 mb-5">
                  <!-- Filter Button + Popover -->
                  <div x-data="{ open: false }" class="relative z-50">
                      <button @click="open = !open"
                          class="inline-flex items-center cursor-pointer gap-2 rounded-lg bg-black text-white text-sm px-4 py-2 shadow hover:bg-gray-800 transition">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                              <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 14.414V20a1 1 0 01-1.447.894l-4-2A1 1 0 019 18v-3.586L3.293 6.707A1 1 0 013 6V4z" />
                          </svg>
                          Filter
                      </button>

                    <div x-show="open" x-cloak @click.outside="open = false" x-transition
                        class="absolute top-full mb-3 left-0 w-[80vw] max-w-4xl bg-white border border-gray-300 shadow-xl rounded-xl p-4 space-y-4 max-h-[70vh] overflow-y-auto">
                        
                        <div class="flex justify-between items-center">
                            <h2 class="text-sm font-semibold text-gray-800">Filter Options</h2>
                            <button @click="open = false" class="text-gray-500 hover:text-gray-800 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    
                        <form method="GET" action="{{ route($routePrefix . '.reporting.index') }}"
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            
                            <!-- Risk Level -->
                            <div>
                                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Hazard Level</label>
                                <select name="riskLevel" class="w-full rounded-lg border-gray-200 text-sm p-2.5">
                                    <option value="">All Hazard Levels</option>
                                    <option value="Low" {{ request('riskLevel') == 'Low' ? 'selected' : '' }}>Low</option>
                                    <option value="Medium" {{ request('riskLevel') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="High" {{ request('riskLevel') == 'High' ? 'selected' : '' }}>High</option>
                                </select>
                            </div>
                    
                            <!-- Status LCT -->
                            <div>
                                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">LCT Status</label>
                                <select name="statusLct" class="w-full rounded-lg border-gray-200 text-sm p-2.5">
                                    <option value="">All Statuses</option>
                                    @foreach ($statusGroups as $label => $statuses)
                                        <option value="{{ implode(',', $statuses) }}"
                                            {{ request('statusLct') == implode(',', $statuses) ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                    
                            <!-- From Date -->
                            <div>
                                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">From Date</label>
                                <input type="date" name="tanggalAwal" value="{{ request('tanggalAwal') }}"
                                    class="w-full rounded-lg border-gray-200 text-sm p-2.5" />
                            </div>
                    
                            <!-- To Date -->
                            <div>
                                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">To Date</label>
                                <input type="date" name="tanggalAkhir" value="{{ request('tanggalAkhir') }}"
                                    class="w-full rounded-lg border-gray-200 text-sm p-2.5" />
                            </div>
                    
                            <!-- Department -->
                            <div>
                                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Department</label>
                                <select name="departemenId" class="w-full rounded-lg border-gray-200 text-sm p-2.5">
                                    <option value="">All Departments</option>
                                    @foreach ($departments as $id => $nama)
                                        <option value="{{ $id }}" {{ request('departemenId') == $id ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                    
                            <!-- Area -->
                            <div>
                                <label class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Area</label>
                                <select name="areaId" class="w-full rounded-lg border-gray-200 text-sm p-2.5">
                                    <option value="">All Areas</option>
                                    @foreach ($areas as $id => $nama)
                                        <option value="{{ $id }}" {{ request('areaId') == $id ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                    
                            <!-- Action Buttons -->
                            <div class="flex items-end gap-3 md:col-span-2 lg:col-span-4">
                                <button type="submit"
                                    class="px-4 py-2.5 text-sm font-medium rounded-lg bg-black text-white cursor-pointer focus:outline-none">
                                    Filters
                                </button>
                                <a href="{{ route($routePrefix . '.reporting.index') }}"
                                    class="px-4 py-2.5 text-sm font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 focus:outline-none">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>
                  
                  </div>

                  <!-- Export Button -->
                  <div>
                      <a 
                          class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg shadow hover:bg-green-600 transition">
                          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                              <path d="M4 4v16c0 .55.45 1 1 1h14a1 1 0 0 0 1-1V4m-4 4l-4 4m0 0l-4-4m4 4V4"></path>
                          </svg>
                          Export to Excel
                      </a>
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
                            <div class="flex gap-2">
                                <select id="findingYear" class="text-sm border-gray-300 rounded-md">
                                    @foreach ($availableYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                
                                <select id="findingMonth" class="text-sm border-gray-300 rounded-md">
                                    <option value="">All Months</option>
                                    @foreach ([
                                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May',
                                        6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct',
                                        11 => 'Nov', 12 => 'Dec'
                                    ] as $num => $month)
                                        <option value="{{ $num }}">{{ $month }}</option>
                                    @endforeach
                                </select>                                
                            </div>
                        </div>
                        <div class="h-[200px]">
                            <canvas id="findingChart"></canvas>
                        </div>
                    </div>
            
                    <!-- Findings by Status -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] xl:basis-1/3 basis-full flex-shrink-0 min-w-0">
                        <div class="mb-2">
                            <h3 class="text-sm font-semibold mb-2">Findings by Status</h3>
                            <div class="flex gap-2 flex-wrap">
                                <select id="statusYear" class="text-sm border-gray-300 rounded-md">
                                    @foreach ($availableYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                
                                <select id="statusMonth" class="text-sm border-gray-300 rounded-md">
                                    <option value="">All Months</option>
                                    @foreach ([
                                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May',
                                        6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct',
                                        11 => 'Nov', 12 => 'Dec'
                                    ] as $num => $month)
                                        <option value="{{ $num }}">{{ $month }}</option>
                                    @endforeach
                                </select>    
                            </div>
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
                    
                        <!-- Filter Year & Month -->
                        <div class="flex gap-2 mb-3">
                            <select id="categoryYear" class="text-sm border-gray-300 rounded-md">
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            
                            <select id="categoryMonth" class="text-sm border-gray-300 rounded-md">
                                <option value="">All Months</option>
                                @foreach ([
                                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May',
                                    6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct',
                                    11 => 'Nov', 12 => 'Dec'
                                ] as $num => $month)
                                    <option value="{{ $num }}">{{ $month }}</option>
                                @endforeach
                            </select> 
                        </div>
                    
                        <div class="h-[200px]">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                    
            
                    <!-- Findings by Area -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] overflow-x-auto xl:w-2/3 w-full">
                        <h3 class="text-sm font-semibold mb-2">Findings by Area</h3>
                    
                        <!-- Filter Year & Month -->
                        <div class="flex gap-2 mb-3">
                            <select id="areaYear" class="text-sm border-gray-300 rounded-md">
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            
                            <select id="areaMonth" class="text-sm border-gray-300 rounded-md">
                                <option value="">All Months</option>
                                @foreach ([
                                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May',
                                    6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct',
                                    11 => 'Nov', 12 => 'Dec'
                                ] as $num => $month)
                                    <option value="{{ $num }}">{{ $month }}</option>
                                @endforeach
                            </select> 
                        </div>
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
                        <div class="flex gap-2 mb-3">
                            <select id="departmentYear" class="text-sm border-gray-300 rounded-md">
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            
                            <select id="departmentMonth" class="text-sm border-gray-300 rounded-md">
                                <option value="">All Months</option>
                                @foreach ([
                                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May',
                                    6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct',
                                    11 => 'Nov', 12 => 'Dec'
                                ] as $num => $month)
                                    <option value="{{ $num }}">{{ $month }}</option>
                                @endforeach
                            </select> 
                        </div>
                        <div class="h-[200px]">
                            <canvas id="departmentChart"></canvas>
                        </div>
                    </div>

                    {{-- <!-- Overdue -->
                    <div class="bg-white p-4 rounded-xl shadow h-[320px] overflow-x-auto xl:w-2/3 w-full">
                        <h3 class="text-sm font-semibold mb-2">Overdue Findings</h3>
                        <div class="flex gap-2 mb-3">
                            <select id="overdueYear" class="text-sm border-gray-300 rounded-md">
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            
                            <select id="overdueMonth" class="text-sm border-gray-300 rounded-md">
                                <option value="">All Months</option>
                                @foreach ([
                                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May',
                                    6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct',
                                    11 => 'Nov', 12 => 'Dec'
                                ] as $num => $month)
                                    <option value="{{ $num }}">{{ $month }}</option>
                                @endforeach
                          </div>
                        <div class="h-[200px]">
                            <canvas id="overdueChart"></canvas>
                        </div>
                    </div> --}}
                </div>
            </div>
            

        </div>
      @endif

    <div class="bg-white p-4 rounded-xl shadow">
        <div x-data="{ openRow: null }">
            <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
                <select id="perPageSelect" class="border border-gray-300 text-sm rounded p-1">
                    <option value="10">10 baris</option>
                    <option value="15">15 baris</option>
                    <option value="25">25 baris</option>
                    <option value="50">50 baris</option>
                </select>
                
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
            <div>
                {{ $laporans->withQueryString()->links() }}
            </div>
        </div>
        </div>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

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



    

</x-app-layout>
