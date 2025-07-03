<x-app-layout>
        @php
            $user = Auth::guard('ehs')->check() ? Auth::guard('ehs')->user() : Auth::guard('web')->user();
            $roleName = Auth::guard('ehs')->check() ? 'ehs' : (optional($user->roleLct->first())->name ?? 'guest');
                  
            if ($roleName === 'ehs') {
                $routeName = 'ehs.reporting.index';
            } elseif ($roleName === 'pic') {
                $routeName = 'admin.manajemen-lct.index';
            } else {
                $routeName = 'admin.reporting.index';
            }
        @endphp

    <div x-data="{ activeTab: 'laporan' }" class="px-0 md:px-4 pt-2 pb-8">
        <!-- Tabs -->
        <div class="flex justify-between items-center">
            <div class="flex space-x-4 border-b">
                <button @click="activeTab = 'laporan'" 
                        :class="activeTab === 'laporan' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                        class="px-4 py-2 text-[10px] sm:text-sm focus:outline-none cursor-pointer">
                    LCT Report
                </button>

                @if(in_array($laporan->tingkat_bahaya, ['Medium', 'High']) && in_array($laporan->status_lct, [
                    'waiting_approval_temporary','temporary_revision','approved_temporary',
                    'waiting_approval_taskbudget','taskbudget_revision','approved_taskbudget',
                    'work_permanent','waiting_approval_permanent','permanent_revision','approved_permanent','closed']))
                    <button @click="activeTab = 'task-and-timeline'" 
                            :class="activeTab === 'task-and-timeline' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                            class="px-4 py-2 text-[10px] sm:text-sm focus:outline-none cursor-pointer">
                        Task & Timeline
                    </button>
                @endif
            </div>

            <a href="{{ route($routeName) }}"
                class="inline-flex items-center px-3 py-1 sm:px-4 sm:py-1.5 text-xs sm:text-sm bg-blue-500 border border-blue-500 rounded-md font-medium text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                 Back
             </a>
             
        </div>

        <!-- Tab Content -->
        <div class="mt-1">
            <div x-show="activeTab === 'laporan'">
                @include('components.tabs.pic-report-detail')
            </div>

            <div x-show="activeTab === 'task-and-timeline'">
                @include('components.tabs.task-and-timeline')
            </div>
        </div>
    </div>

    {{-- script untuk modal gambar --}}
    <script>
        function openModal(imageSrc) {
            const modal = document.getElementById("imageModal");
            const modalImage = document.getElementById("modalImage");

            modal.classList.remove("hidden");
            modal.classList.add("flex");
            modalImage.src = imageSrc;
        }

        function closeModal() {
            const modal = document.getElementById("imageModal");
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }

        document.getElementById("imageModal").addEventListener("click", function(event) {
            if (event.target === this) {
                closeModal();
            }
        });
    </script>

</x-app-layout>
