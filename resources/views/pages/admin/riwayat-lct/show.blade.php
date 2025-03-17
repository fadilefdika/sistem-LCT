<x-app-layout>
    <div x-data="{ 
        activeTab: '{{ in_array($laporan->status_lct, [
            'approved', 'waiting_approval', 'revision', 
            'waiting_approval_temporary', 'temporary_revision'
        ]) ? 'pic' : (in_array($laporan->status_lct, [
            'approved_temporary', 'taskbudget_revision', 
            'waiting_approval_taskbudget', 'approved_taskbudget', 'approved_permanent', 'closed'
        ]) ? 'task-pic' : 'user') }}' 
    }"
     class="px-5 pt-2">
        <!-- Tabs -->
        <div class="flex space-x-4 border-b">
            <button @click="activeTab = 'user'" 
                    :class="activeTab === 'user' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                    class="px-4 py-2 focus:outline-none cursor-pointer">
                User
            </button>
        
            <button @click="activeTab = 'pic'" 
                    :class="activeTab === 'pic' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                    class="px-4 py-2 focus:outline-none cursor-pointer">
                PIC
            </button>
        
            @if(in_array($laporan->tingkat_bahaya, ['Medium', 'High']) && in_array($laporan->status_lct, ['approved_temporary','approved_taskbudget','taskbudget_revision','waiting_approval_taskbudget','approved_permanent','closed']))
                <button @click="activeTab = 'task-pic'" 
                        :class="activeTab === 'task-pic' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                        class="px-4 py-2 focus:outline-none cursor-pointer">
                    Task PIC
                </button>
            @endif
        </div>
        

        <!-- Tab Content -->
        <div class="mt-1">
            {{-- Laporan dari User --}}
            <div x-show="activeTab === 'user'">
                @include('partials.progress-perbaikan-report-user')
            </div>

            {{-- Laporan dari PIC --}}
            <div x-show="activeTab === 'pic'">
                @include('partials.progress-perbaikan-report-pic')
            </div>

            {{-- Task dari PIC --}}
            <div x-show="activeTab === 'task-pic'">
                @include('partials.progress-perbaikan-task')
            </div>
        </div>
    </div>


    {{-- script untuk modal gambar --}}
    <script>
        function openModal(imageSrc) {
            const modal = document.getElementById("imageModal");
            const modalImage = document.getElementById("modalImage");

            modal.classList.remove("hidden");
            modal.classList.add("flex"); // Agar modal muncul
            modalImage.src = imageSrc;
        }

        function closeModal() {
            const modal = document.getElementById("imageModal");
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }

        // Tutup modal jika klik di luar gambar
        document.getElementById("imageModal").addEventListener("click", function(event) {
            if (event.target === this) {
                closeModal();
            }
        });
    </script>
</x-app-layout>

