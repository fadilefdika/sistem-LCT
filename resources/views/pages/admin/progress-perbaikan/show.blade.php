<style>
    .tab-content {
        transition: opacity 0.3s ease, visibility 0.3s ease;
        opacity: 0;
        visibility: hidden;
        height: 0;
        overflow: hidden;
    }
    .tab-content.active {
        opacity: 1;
        visibility: visible;
        height: auto; /* Biar bisa menyesuaikan tinggi konten */
        overflow: visible;
    }
</style>

@php
    // Tentukan default tab, misal 'user' atau sesuai kebutuhan
    $defaultTab = request('tab', 'user');
@endphp

<x-app-layout>
    <div class="px-0 md:px-5 pt-2">
        <!-- Tabs -->
        <div id="tabs" class="flex space-x-4 border-b">
            <button data-tab="user" class="tab-btn px-4 py-2 cursor-pointer text-gray-500">
                Finder
            </button>

            <button data-tab="pic" class="tab-btn px-4 py-2 cursor-pointer text-gray-500">
                PIC
            </button>

            @if(in_array($laporan->status_lct, [
                'closed', 'approved_temporary', 'approved_taskbudget', 'taskbudget_revision', 'waiting_approval_temporary', 'temporary_revision',
                'waiting_approval_taskbudget', 'waiting_approval_permanent', 
                'work_permanent', 'permanent_revision', 'approved_permanent'
            ]) && $laporan->tingkat_bahaya !== 'Low')
                <button data-tab="task-pic" class="tab-btn px-4 py-2 cursor-pointer text-gray-500">
                    Task PIC
                </button>
            @endif
        </div>

        <!-- Tab Content -->
        <div class="mt-1">
            <div id="tab-user" class="tab-content">
                @include('partials.progress-perbaikan-report-user')
            </div>
            <div id="tab-pic" class="tab-content">
                @include('partials.progress-perbaikan-report-pic')
            </div>
            <div id="tab-task-pic" class="tab-content">
                @include('partials.progress-perbaikan-task')
            </div>
        </div>
    </div>

    <!-- Modal Preview -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black/50 bg-opacity-75 flex items-center justify-center z-60 transition-opacity duration-300">
        <div class="relative bg-white p-1 rounded-lg shadow-lg">
            <button id="closeModalBtn"
                class="absolute -top-4 -right-4 bg-gray-800 text-white rounded-full w-10 h-10 flex items-center justify-center text-2xl font-bold shadow-md hover:bg-red-600 transition cursor-pointer"
                onclick="closeModal()">
                &times;
            </button>
            
            <img id="modalImage" class="w-[600px] h-[500px] object-cover rounded-lg">
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultTab = '{{ $defaultTab }}';
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        function setActiveTab(tab) {
            tabs.forEach(btn => {
                if (btn.dataset.tab === tab) {
                    btn.classList.add('border-b-2', 'border-blue-500', 'text-blue-500');
                    btn.classList.remove('text-gray-500');
                } else {
                    btn.classList.remove('border-b-2', 'border-blue-500', 'text-blue-500');
                    btn.classList.add('text-gray-500');
                }
            });

            contents.forEach(content => {
                if (content.id === 'tab-' + tab) {
                    content.classList.add('active');
                } else {
                    content.classList.remove('active');
                }
            });
        }

        // Set default tab on load
        setActiveTab(defaultTab);

        // Add click event on all tabs
        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                setActiveTab(btn.dataset.tab);
                history.replaceState(null, '', '?tab=' + btn.dataset.tab);
            });
        });
    });

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
