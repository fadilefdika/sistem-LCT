<x-app-layout>

    <div x-data="{ activeTab: '{{ in_array($laporan->status_lct, ['approved_temporary','waiting_approval_taskbudget', 'taskbudget_revision','approved_taskbudget', 'work_permanent','waiting_approval_permanent','permanent_revision', 'approved_permanent']) ? 'task-and-timeline' : 'laporan'}}' }"
        class="px-0 md:px-5 pt-2 pb-8">
        <!-- Tabs -->
        <div class="flex space-x-4 border-b">
            <button @click="activeTab = 'laporan'" :class="activeTab === 'laporan' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500'"
                class="px-4 py-2 focus:outline-none cursor-pointer">
                LCT Report
            </button>
            <!-- Menampilkan tombol Task & Timeline hanya jika status LCT adalah 'approved' dan tingkat bahaya Medium atau High -->
            @if(in_array($laporan->tingkat_bahaya, ['Medium', 'High']) && in_array($laporan->status_lct, ['waiting_approval_temporary','temporary_revision','approved_temporary','waiting_approval_taskbudget', 'taskbudget_revision','approved_taskbudget', 'work_permanent','waiting_approval_permanent','permanent_revision', 'approved_permanent']))
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
            <div x-show="activeTab === 'laporan'">
                @include('components.tabs.pic-report-detail')
            </div>

            <div x-show="activeTab === 'task-and-timeline'">
                @include('components.tabs.task-and-timeline')
            </div>
            
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



