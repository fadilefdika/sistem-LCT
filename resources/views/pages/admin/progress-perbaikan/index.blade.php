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
                  {{-- <div>
                      <a href="{{ route(($roleName === 'ehs' ? 'ehs' : 'admin') . '.reporting.export.excel', request()->query()) }}"
                          class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg shadow hover:bg-green-600 transition">
                          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                              <path d="M4 4v16c0 .55.45 1 1 1h14a1 1 0 0 0 1-1V4m-4 4l-4 4m0 0l-4-4m4 4V4"></path>
                          </svg>
                          Export to Excel
                      </a>
                  </div> --}}
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
                        @forelse($laporans as $index => $laporan)
                            @php
                                $laporanId = $laporan->id;
                            @endphp
                        <tr @click="openRow === {{ $laporanId }} ? openRow = null : openRow = {{ $laporanId }}" class="hover:bg-gray-100 cursor-pointer text-sm transition duration-200 ease-in-out">
                            <td class="px-4 py-3 text-center font-semibold text-gray-800 w-12">
                                {{ $laporans->firstItem() + $index }}
                            </td>
                            <!-- Tanggal Temuan -->
                            <td class="px-4 py-3 text-xs text-gray-800 w-32 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('F d, Y') }}
                            </td>
                            <!-- Tenggat Waktu -->
                            @php
                                $today = now();
                                $isOverdue = false;
                                $dueDateToShow = null;
                            
                                if ($laporan->status_lct !== 'closed') {
                                    if ($laporan->tingkat_bahaya === 'Low') {
                                        // Untuk tingkat bahaya Low, pakai due_date biasa
                                        $dueDateToShow = $laporan->due_date;
                            
                                        // Overdue jika due_date ada, belum selesai (date_completion null), dan hari ini sudah lewat due_date
                                        $isOverdue = $dueDateToShow && !$laporan->date_completion && $today->gt($dueDateToShow);
                            
                                    } elseif (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                                        // Untuk Medium dan High, seharusnya pakai due_date_temp dan due_date_perm
                                        // Tapi karena belum ada data, sementara kita komentar dulu
                            
                                        // if (!$laporan->date_completion_temp) {
                                        //     $dueDateToShow = $laporan->due_date_temp;
                                        //     $isOverdue = $dueDateToShow && !$laporan->date_completion_temp && $today->gt($dueDateToShow);
                                        // } else {
                                        //     $dueDateToShow = $laporan->due_date_perm;
                                        //     $isOverdue = $dueDateToShow && !$laporan->date_completion && $today->gt($dueDateToShow);
                                        // }
                            
                                        // Sementara pakai due_date saja sebagai pengganti
                                        $dueDateToShow = $laporan->due_date;
                                        $isOverdue = $dueDateToShow && !$laporan->date_completion && $today->gt($dueDateToShow);
                                    }
                                } else {
                                    // Kalau status sudah closed, tetap tampilkan due_date sesuai tingkat bahaya
                            
                                    if ($laporan->tingkat_bahaya === 'Low') {
                                        $dueDateToShow = $laporan->due_date;
                            
                                    } elseif (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                                        // Normalnya ini pakai due_date_temp dan due_date_perm,
                                        // tapi karena belum ada, kita pakai due_date dulu
                                        // $dueDateToShow = $laporan->date_completion_temp ? $laporan->due_date_perm : $laporan->due_date_temp;
                                        
                                        $dueDateToShow = $laporan->due_date;
                                    }
                                }
                            @endphp
                            
                            <td class="px-4 py-3 w-32 text-xs whitespace-nowrap {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-gray-800' }}">
                                {{-- Tampilkan tanggal due date, format "Month day, Year" --}}
                                {{ $dueDateToShow ? \Carbon\Carbon::parse($dueDateToShow)->format('F d, Y') : '-' }}
                            
                                {{-- Jika overdue, tampilkan label "Overdue" --}}
                                @if($isOverdue)
                                    <span class="ml-1 text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Overdue</span>
                                @endif
                            </td>
                        

                        
                            @php
                                $fullname = $laporan->picUser->fullname ?? '-';
                                $words = explode(' ', $fullname);
                                $formatted = '';
                            
                                if (count($words) > 0) {
                                    $formatted .= $words[0];
                                }
                            
                                if (count($words) > 1) {
                                    $formatted .= ' ' . $words[1];
                                }
                            
                                if (count($words) > 2) {
                                    for ($i = 2; $i < count($words); $i++) {
                                        $formatted .= ' ' . strtoupper(substr($words[$i], 0, 1)) . '.';
                                    }
                                }
                            @endphp
                            
                            <td class="px-4 py-3 text-gray-800 w-40 text-[10px] whitespace-nowrap">{{ $formatted }}</td>
                            
                            
                            <!-- Tingkat Bahaya -->
                            <td class="px-4 py-3 text-gray-800 w-28">
                                @php
                                    $bahayaColors = [
                                        'High' => 'bg-red-500',
                                        'Medium' => 'bg-yellow-500',
                                        'Low' => 'bg-green-500'
                                    ];
                                @endphp
                                <span class=" text-xs font-semibold text-gray-800">
                                    {{ $laporan->tingkat_bahaya }}
                                </span>
                            </td>

                            <!-- Status Progress -->
                            <td class="px-4 py-3 text-gray-800 w-36">
                                @php
                                    // List of status with labels, colors, and tracking descriptions
                                    $statusMapping = [
                                        'open' => ['label' => 'Open', 'color' => 'bg-gray-500', 'tracking' => 'Report has been created'],
                                        'review' => ['label' => 'Review', 'color' => 'bg-purple-500', 'tracking' => 'Report is under review'],
                                        'in_progress' => ['label' => 'Not Yet', 'color' => 'bg-red-500', 'tracking' => 'Report has been sent, but PIC has not viewed it'],
                                        'progress_work' => ['label' => 'Not Yet', 'color' => 'bg-red-500', 'tracking' => 'PIC has viewed the report'],
                                        'work_permanent' => ['label' => 'In Progress', 'color' => 'bg-yellow-500', 'tracking' => 'PIC is working on a permanent LCT'],
                                        'waiting_approval' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for LCT Low approval from EHS'],
                                        'waiting_approval_temporary' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for temporary LCT approval from EHS'],
                                        'waiting_approval_permanent' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for permanent LCT approval from EHS'],
                                        'waiting_approval_taskbudget' => ['label' => 'Waiting Approval', 'color' => 'bg-blue-500', 'tracking' => 'Waiting for task and budget approval from the manager'],
                                        'approved' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'LCT Low has been approved by EHS'],
                                        'approved_temporary' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Temporary LCT has been approved by EHS'],
                                        'approved_permanent' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Permanent LCT has been approved by EHS'],
                                        'approved_taskbudget' => ['label' => 'Approved', 'color' => 'bg-green-500', 'tracking' => 'Task and budget for permanent LCT has been approved by the manager'],
                                        'revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'LCT Low needs revision by PIC'],
                                        'temporary_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'Temporary LCT needs revision by PIC'],
                                        'permanent_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'Permanent LCT needs revision by PIC'],
                                        'taskbudget_revision' => ['label' => 'Revision', 'color' => 'bg-red-500', 'tracking' => 'The LCT task and budget require revision by PIC'],
                                        'closed' => ['label' => 'Closed', 'color' => 'bg-green-700', 'tracking' => 'Report has been closed by PIC'],
                                    ];

                                    // If danger level is Medium or High, adjust specific status colors
                                    if (in_array($laporan->tingkat_bahaya, ['Medium', 'High'])) {
                                        foreach (['waiting_approval_temporary', 'approved_temporary', 'temporary_revision', 
                                                'work_permanent', 'waiting_approval_permanent', 'approved_permanent', 
                                                'permanent_revision'] as $key) {
                                            if (isset($statusMapping[$key])) {
                                                $statusMapping[$key]['color'] = match ($key) {
                                                    'approved_temporary', 'approved_permanent' => 'bg-green-500',
                                                    'temporary_revision', 'permanent_revision' => 'bg-red-500',
                                                    'waiting_approval_temporary', 'waiting_approval_permanent' => 'bg-blue-500',
                                                    'work_permanent' => 'bg-yellow-500',
                                                    default => $statusMapping[$key]['color'],
                                                };
                                            }
                                        }
                                    }

                                    // Get status from the report data
                                    $status = $statusMapping[$laporan->status_lct] ?? [
                                        'label' => 'Unknown',
                                        'color' => 'bg-gray-400',
                                        'tracking' => 'Status not found'
                                    ];
                                @endphp

                                <!-- Status Column -->
                                <span class="inline-flex items-center justify-center px-3 py-1 text-[10px] font-semibold text-gray-800 whitespace-nowrap">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                        </tr>
                        <!-- Detail Row -->
                        @php
                            $raw = $laporan->bukti_temuan;

                            // Kalau array, langsung gunakan. Kalau string, decode dulu.
                            $buktiArray = is_array($raw) ? $raw : json_decode($raw, true);

                            // Jika null karena gagal decode, fallback ke array kosong
                            $buktiArray = $buktiArray ?? [];

                            $bukti_temuan = collect($buktiArray)->map(fn($path) => asset('storage/' . $path));

                            $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
                            $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');
                                
                            $routeName = $laporan->status_lct === 'open'
                                    ? ($roleName === 'ehs' ? 'ehs.reporting.show.new' : 'admin.reporting.show')
                                    : ($roleName === 'ehs' ? 'ehs.reporting.show' : 'admin.reporting.show');

                        @endphp
        
                        <tr x-show="openRow === {{ $laporanId }}"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-y-90"
                            x-transition:enter-end="opacity-100 scale-y-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-y-100"
                            x-transition:leave-end="opacity-0 scale-y-90"
                            style="transform-origin: top">
                            <td colspan="8" class="w-full bg-gray-50 px-6 py-6">
                                <div class="w-full bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
                                    <!-- Header -->
                                    <div class="border-b pb-4 mb-4">
                                        <div class="flex justify-between items-center flex-wrap gap-4">
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-800 mb-1">📝 Finding Details</h3>
                                                <p class="text-xs text-gray-500">Report ID: <span class="font-medium text-gray-700">{{ $laporan->id_laporan_lct }}</span></p>
                                            </div>
                                            <div class="flex gap-4">
                                                <div>
                                                    <p class="text-[10px] text-gray-500 uppercase font-semibold mb-1">Status</p>
                                                    <span class="inline-flex px-3 py-1 text-[10px] font-semibold text-white rounded-full {{ $status['color'] }}">
                                                        {{ $status['label'] }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] text-gray-500 uppercase font-semibold mb-1">Hazard Level</p>
                                                    <span class="inline-flex px-3 py-1 text-[10px] font-semibold text-white rounded-full {{ $bahayaColors[$laporan->tingkat_bahaya] ?? 'bg-gray-400' }}">
                                                        {{ $laporan->tingkat_bahaya }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    

                                    <div x-data="{ activeTab: 'finder' }">
                                        <!-- Tabs -->
                                        <div class="flex border-b mb-4">
                                            <button @click="activeTab = 'finder'" 
                                                    :class="activeTab === 'finder' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'" 
                                                    class="px-4 py-2 text-sm font-semibold cursor-pointer">
                                                Finder Report
                                            </button>

                                            @if(!in_array($laporan->status_lct, ['open', 'in_progress', 'progress_work']))
                                                <button @click="activeTab = 'pic'" 
                                                        :class="activeTab === 'pic' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'" 
                                                        class="px-4 py-2 text-sm font-semibold cursor-pointer">
                                                    PIC Improvement
                                                </button>
                                            @endif
                                        </div>

                                    
                                        <!-- Finder Section -->
                                        <div x-show="activeTab === 'finder'">
                                            <div>
                                                <!-- Gambar dan Detail -->
                                                <div class="flex flex-col lg:flex-row gap-6 w-full">
                                                    <div class="lg:w-1/3 w-full" x-data="{
                                                        images: @js($bukti_temuan),
                                                        current: 0,
                                                        interval: null,
                                                        startSlider() {
                                                            if (this.images.length > 1) {
                                                                this.interval = setInterval(() => {
                                                                    this.next()
                                                                }, 3000)
                                                            }
                                                        },
                                                        stopSlider() {
                                                            clearInterval(this.interval)
                                                        },
                                                        next() {
                                                            this.current = (this.current + 1) % this.images.length
                                                        },
                                                        prev() {
                                                            this.current = (this.current - 1 + this.images.length) % this.images.length
                                                        }
                                                    }" x-init="startSlider" @mouseenter="stopSlider" @mouseleave="startSlider">
                                                        <div class="relative aspect-video bg-gray-100 rounded-md overflow-hidden border border-gray-200">
                                                            <template x-for="(img, index) in images" :key="index">
                                                                <img
                                                                    :src="img"
                                                                    alt="Evidence"
                                                                    class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500"
                                                                    :class="{ 'opacity-100': index === current, 'opacity-0': index !== current }"
                                                                >
                                                            </template>
                                                    
                                                            <!-- Panah Navigasi (hanya jika gambar > 1) -->
                                                            <template x-if="images.length > 1">
                                                                <button @click="prev"
                                                                    class="absolute cursor-pointer left-0 top-1/2 -translate-y-1/2 bg-white/60 hover:bg-white text-gray-800 px-2 py-1 rounded-r-md">
                                                                    ‹
                                                                </button>
                                                            </template>
                                                    
                                                            <template x-if="images.length > 1">
                                                                <button @click="next"
                                                                    class="absolute cursor-pointer right-0 top-1/2 -translate-y-1/2 bg-white/60 hover:bg-white text-gray-800 px-2 py-1 rounded-l-md">
                                                                    ›
                                                                </button>
                                                            </template>
                                                        </div>
                                                    
                                                        <div class="mt-2 text-[10px] text-gray-500 text-center">
                                                            <span x-text="'This report contains ' + images.length + ' image' + (images.length > 1 ? 's' : '') + '.'"></span>
                                                        </div>
                                                    </div>
                                                    
                                        
                                                    <div class="lg:w-2/3 w-full grid grid-cols-2 gap-4">
                                                        <div>
                                                            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Finding Date</p>
                                                            <p class="text-xs text-gray-800">{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('d M Y') ?? '-' }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Finder</p>
                                                            <p class="text-xs text-gray-800">{{ $laporan->user->fullname ?? '-' }}</p>
                                                        </div>
                                                        <div class="space-y-1">
                                                            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Category</p>
                                                            <p class="text-xs text-gray-800">
                                                                {{ $laporan->kategori->nama_kategori === '5S (Seiri, Seiton, Seiso, Seiketsu, dan Shitsuke)' ? '5S' : $laporan->kategori->nama_kategori }}
                                                            </p>
                                                        </div>
                                                        <div class="space-y-1">
                                                            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Area</p>
                                                            <p class="text-xs text-gray-800">
                                                                {{ optional($laporan->area)->nama_area ?? '-' }} - {{ $laporan->detail_area ?? '-' }}
                                                            </p>                                            
                                                        </div>
                                                        <div class="space-y-1 col-span-2">
                                                            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Finding</p>
                                                            <p class="text-xs text-gray-800">{{ $laporan->temuan_ketidaksesuaian ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                        
                                                <!-- Safety Recommendation -->
                                                <div class="bg-blue-50/50 p-4 rounded-md border border-blue-100 w-full">
                                                    <div class="flex items-start space-x-2">
                                                        <div class="mt-0.5">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h4 class="text-xs font-medium text-blue-800 mb-1">Safety Recommendation</h4>
                                                            <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed">{{ $laporan->rekomendasi_safety }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                        
                                                <!-- Action Button -->
                                                <div class="flex justify-end pt-2">
                                                    <a href="{{ route($routeName, $laporan->id_laporan_lct) }}"
                                                        class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                                        Details
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <!-- PIC Improvement Section -->
                                        @if(!in_array($laporan->status_lct, ['open', 'in_progress', 'progress_work']))
                                            <div x-show="activeTab === 'pic'" class="space-y-8">
                                                @php
                                                    $tindakanPerbaikan = collect(json_decode($laporan->tindakan_perbaikan, true))->map(function ($entry) {
                                                        return [
                                                            'tanggal' => $entry['tanggal'],
                                                            'tindakan' => $entry['tindakan'],
                                                            'bukti' => collect($entry['bukti'])->map(fn($path) => asset('storage/' . $path)),
                                                        ];
                                                    })->toArray(); // pastikan kembali ke array agar bisa digunakan di @foreach
                                                @endphp


                                                @if(is_array($tindakanPerbaikan) && count($tindakanPerbaikan))
                                                    @foreach($tindakanPerbaikan as $index => $tp)
                                                        <div class="space-y-4">
                                                            {{-- <p class="text-sm font-semibold text-gray-700">Revision {{ $index + 1 }}</p> --}}

                                                            <div class="flex flex-col lg:flex-row gap-6">
                                                                <!-- Image Slider -->
                                                                <div class="lg:w-1/3 w-full" x-data="{
                                                                    images: @js($tp['bukti']),
                                                                    current: 0,
                                                                    interval: null,
                                                                    startSlider() {
                                                                        if (this.images.length > 1) {
                                                                            this.interval = setInterval(() => this.next(), 3000);
                                                                        }
                                                                    },
                                                                    stopSlider() {
                                                                        clearInterval(this.interval);
                                                                    },
                                                                    next() {
                                                                        this.current = (this.current + 1) % this.images.length;
                                                                    },
                                                                    prev() {
                                                                        this.current = (this.current - 1 + this.images.length) % this.images.length;
                                                                    }
                                                                    }" x-init="startSlider" @mouseenter="stopSlider" @mouseleave="startSlider">
                                                                    <div class="relative aspect-video rounded-md overflow-hidden border border-gray-200">
                                                                        <template x-for="(img, idx) in images" :key="idx">
                                                                            <img :src="img" alt="Evidence"
                                                                                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500"
                                                                                :class="{ 'opacity-100': idx === current, 'opacity-0': idx !== current }">
                                                                        </template>

                                                                        <template x-if="images.length > 1">
                                                                            <button @click="prev"
                                                                                class="absolute left-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white text-gray-800 px-2 py-1 rounded-r-md">
                                                                                ‹
                                                                            </button>
                                                                        </template>
                                                                        <template x-if="images.length > 1">
                                                                            <button @click="next"
                                                                                class="absolute right-0 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white text-gray-800 px-2 py-1 rounded-l-md">
                                                                                ›
                                                                            </button>
                                                                        </template>
                                                                    </div>

                                                                    <p class="text-center text-[10px] text-gray-500 mt-2">
                                                                        <span x-text="'This correction contains ' + images.length + ' image' + (images.length > 1 ? 's' : '') + '.'"></span>
                                                                    </p>
                                                                </div>

                                                                <!-- Detail Tindakan -->
                                                                <div class="lg:w-2/3 w-full grid grid-cols-2 gap-4 text-sm">
                                                                    <div>
                                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Completion Date</p>
                                                                        <p class="text-xs text-gray-800">{{ \Carbon\Carbon::parse($tp['tanggal'])->format('d M Y') ?? '-' }}</p>
                                                                    </div>
                                                                    <div>
                                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Temporary Due Date</p>
                                                                        <p class="text-xs text-gray-800">{{ \Carbon\Carbon::parse($laporan->due_date)->format('d M Y') ?? '-' }}</p>
                                                                    </div>
                                                                    <div>
                                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">Permanent Due Date</p>
                                                                        <p class="text-xs text-gray-800">{{ \Carbon\Carbon::parse($laporan->due_date)->format('d M Y') ?? '-' }}</p>
                                                                    </div>
                                                                    <div>
                                                                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider">PIC</p>
                                                                        <p class="text-xs text-gray-800">{{ $laporan->picUser->fullname ?? '-' }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Safety Recommendation -->
                                                            <div class="bg-blue-50/50 p-4 rounded-md border border-blue-100">
                                                                <div class="flex items-start space-x-2">
                                                                    <div class="mt-0.5">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                                                                        </svg>
                                                                    </div>
                                                                    <div>
                                                                        <h4 class="text-xs font-medium text-blue-800 mb-1">Corrective Action</h4>
                                                                        <p class="text-xs text-gray-700 whitespace-pre-line leading-relaxed">{{ $tp['tindakan'] }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Action Button -->
                                                            <div class="flex justify-end">
                                                                <a href="{{ route($routeName, $laporan->id_laporan_lct) }}"
                                                                    class="inline-flex items-center px-4 py-2 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                                                                    View Full Details
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <p class="text-sm text-gray-500 italic">No corrective action provided.</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>                        
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-10 h-10 mb-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 14l2 2 4-4m0-3V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h6">
                                        </path>
                                    </svg>
                                    <p class="text-sm">No reports are available.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
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

</x-app-layout>
