<x-app-layout>

    <div x-data="{ activeTab: ['approved', 'rejected', 'pending'].includes('{{ $budget->status_budget ?? '' }}') ? 'task-and-timeline' : 'laporan' }"
        class="px-5 pt-2 pb-8">
        <!-- Tabs -->
        <div class="flex space-x-4 border-b">
            <button @click="activeTab = 'laporan'" :class="activeTab === 'laporan' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                class="px-4 py-2 focus:outline-none cursor-pointer">
                Laporan LCT
            </button>
            <!-- Menampilkan tombol Task & Timeline hanya jika status LCT adalah 'approved' dan tingkat bahaya Medium atau High -->
            @if(in_array($laporan->tingkat_bahaya, ['Medium', 'High']) && $laporan->status_lct === 'approved')
            <button @click="activeTab = 'task-and-timeline'" 
                    :class="activeTab === 'task-and-timeline' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                    class="px-4 py-2 focus:outline-none cursor-pointer">
                Task & Timeline
            </button>
            @endif
        </div>

        <!-- Tab Content -->
        <div class="mt-1">
            <!-- Laporan -->
            <div x-show="activeTab === 'laporan'" >
                @include('components.tabs.pic-report-detail')
            </div>

            <div x-show="activeTab === 'task-and-timeline'">
                @include('components.tabs.task-and-timeline')
            </div>
            
            </div>
        </div>
    </div>

</x-app-layout>



