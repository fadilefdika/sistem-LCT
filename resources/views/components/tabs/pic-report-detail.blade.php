<div class="h-screen flex flex-col min-h-0">
    <!-- Mobile Tabs (only show on mobile) -->
    <div class="md:hidden flex border-b border-gray-200 sticky top-0 z-10 bg-white dark:bg-gray-800">
        <button 
            id="tab-report" 
            class="flex-1 py-3 px-4 font-medium text-center border-b-2 border-blue-500 text-blue-600 dark:text-blue-400"
            onclick="switchTab('report')"
        >
            Laporan EHS
        </button>
        <button 
            id="tab-form" 
            class="flex-1 py-3 px-4 font-medium text-center border-b-2 border-transparent text-gray-500 dark:text-gray-400"
            onclick="switchTab('form')"
        >
            Form Temuan
        </button>
    </div>

    @if($laporan->status_lct == 'waiting_approval_temporary')
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded-md mb-4 shadow-sm">
            <div class="flex items-start gap-2">
                <svg class="w-5 h-5 mt-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M12 8v.01M12 12v.01M12 16v.01M12 20h.01M8 12h.01M16 12h.01M12 4h.01M4 12h.01M20 12h.01"/>
                </svg>
                <div>
                    <p class="font-semibold">Action Required</p>
                    <p>Click the <strong>"Task & Timeline"</strong> tab to complete the permanent action task and provide the estimated budget immediately.</p>
                </div>                                              
            </div>
        </div>
    @endif

    <!-- Content Area -->
    <div class="flex flex-col md:grid md:grid-cols-2 flex-grow pb-1 min-h-0">
        <!-- Card Laporan dari EHS -->
        <div 
            id="report-content"
            class="relative max-w-full bg-[#F3F4F6] shadow-md p-3 overflow-y-auto flex-grow min-h-0
                {{ $laporan->status_lct == 'waiting_approval_temporary' ? 'pb-36' : 'pb-20' }}
                [&::-webkit-scrollbar]:w-1
                [&::-webkit-scrollbar-track]:rounded-full
                [&::-webkit-scrollbar-track]:bg-gray-100
                [&::-webkit-scrollbar-thumb]:rounded-full
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
            @include('partials.manajemen-lct-report', [
                'laporan' => $laporan,
                'bukti_temuan' => $bukti_temuan,
            ])    
        </div>
        
        <!-- Form Laporan Temuan -->
        <div 
            id="form-content"
            class="relative max-w-full bg-[#F3F4F6] shadow-md p-3 overflow-y-auto flex-grow min-h-0
                {{ $laporan->status_lct == 'waiting_approval_temporary' ? 'pb-36' : 'pb-20' }}
                [&::-webkit-scrollbar]:w-1
                [&::-webkit-scrollbar-track]:rounded-full
                [&::-webkit-scrollbar-track]:bg-gray-100
                [&::-webkit-scrollbar-thumb]:rounded-full
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">
            @include('partials.manajemen-lct-form', [
                'laporan' => $laporan,
            ])    
        </div>
    </div>
</div>


<script>
    function switchTab(tabName) {
        // Update tab buttons
        document.getElementById('tab-report').classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        document.getElementById('tab-report').classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        document.getElementById('tab-form').classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        document.getElementById('tab-form').classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        
        // Highlight active tab
        document.getElementById(`tab-${tabName}`).classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        document.getElementById(`tab-${tabName}`).classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        
        // Show/hide content - only apply on mobile
        if (window.innerWidth < 768) {
            if(tabName === 'report') {
                document.getElementById('report-content').classList.remove('hidden');
                document.getElementById('form-content').classList.add('hidden');
            } else {
                document.getElementById('report-content').classList.add('hidden');
                document.getElementById('form-content').classList.remove('hidden');
            }
        }
    }

    // Initialize - hide form content on mobile by default
    function handleResize() {
        if (window.innerWidth < 768) {
            // Mobile view
            document.getElementById('form-content').classList.add('hidden');
            document.getElementById('report-content').classList.remove('hidden');
        } else {
            // Desktop view - show both
            document.getElementById('form-content').classList.remove('hidden');
            document.getElementById('report-content').classList.remove('hidden');
        }
    }

    // Run on load and when window is resized
    window.addEventListener('load', handleResize);
    window.addEventListener('resize', handleResize);
</script>