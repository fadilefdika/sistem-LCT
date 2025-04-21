<div class="max-h-screen">
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

    <!-- Content Area -->
    <div class="flex flex-col md:grid md:grid-cols-2 h-[calc(100vh-56px)] md:h-screen">
        <!-- Card Laporan dari EHS -->
        <div 
            id="report-content"
            class="w-full bg-[#F3F4F6] md:max-h-[calc(100vh)] pb-28 md:pb-0 h-full overflow-y-auto
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
            class="w-full bg-[#F3F4F6] px-3 pt-3 pb-32 md:pb-0 md:max-h-[calc(100vh)] h-full overflow-y-auto
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