<div x-data="{ activeTab: 'master-data-pic' }"
    class="px-5 pt-2 pb-4">
    <!-- Tabs -->
    <div class="flex space-x-4 border-b">
        <button @click="activeTab = 'master-data-pic'" :class="activeTab === 'master-data-pic' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
            class="px-4 py-2 focus:outline-none cursor-pointer">
            PIC Data
        </button>
 
        <button @click="activeTab = 'master-data-kategori'" 
                :class="activeTab === 'master-data-kategori' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                class="px-4 py-2 focus:outline-none cursor-pointer">
            Category Data
        </button>

        <button @click="activeTab = 'master-data-departemen'" 
                :class="activeTab === 'master-data-departemen' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                class="px-4 py-2 focus:outline-none cursor-pointer">
            Department Data
        </button>
    </div>

    <!-- Tab Content -->
    <div class="mt-3">
        <!-- Laporan -->
        <div x-show="activeTab === 'master-data-pic'">
            @include('partials.master-data-pic')
        </div>

        <div x-show="activeTab === 'master-data-kategori'">
            @include('partials.master-data-kategori')
        </div>

        <div x-show="activeTab === 'master-data-departemen'">
            @include('partials.master-data-departemen')
        </div>
        
    </div>
</div>