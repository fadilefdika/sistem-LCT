<x-app-layout class="h-screen md:overflow-hidden">
    @php
        $userRole = optional(auth()->user()->roleLct->first())->name;
    @endphp

    <div x-data="tabs()" x-init="init()" class="h-full flex flex-col">

        <!-- Mobile Tab Switcher -->
        @if ($userRole !== 'manajer')
        <div class="md:hidden flex justify-around border-b bg-white shadow z-10">
            <button @click="tab = 'report'"
                    :class="{ 'border-b-2 border-blue-500 font-bold': tab === 'report' }"
                    class="w-full py-2 text-center">
                Laporan
            </button>
            <button @click="tab = 'form'"
                    :class="{ 'border-b-2 border-blue-500 font-bold': tab === 'form' }"
                    class="w-full py-2 text-center">
                Formulir
            </button>
        </div>
        @endif

        <!-- Content -->
        <div class="flex-1 overflow-y-auto">
            <div class="grid md:grid-cols-{{ $userRole === 'manajer' ? '1' : '2' }} w-full h-full">

                <!-- Report -->
                <div 
                    x-show="tab === 'report' || isDesktop || '{{ $userRole }}' === 'manajer'"  
                    class="relative max-w-full bg-[#F3F4F6] overflow-hidden shadow-md p-3 pb-20 max-h-[calc(100vh)] overflow-y-auto
                        [&::-webkit-scrollbar]:w-1
                        [&::-webkit-scrollbar-track]:rounded-full
                        [&::-webkit-scrollbar-track]:bg-gray-100
                        [&::-webkit-scrollbar-thumb]:rounded-full
                        [&::-webkit-scrollbar-thumb]:bg-gray-300
                        dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                        dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500"
                >
                    @include('partials.laporan-lct-report', [
                        'laporan' => $laporan,
                        'bukti_temuan' => $bukti_temuan,
                    ])
                </div>

                @if ($userRole !== 'manajer')
                <!-- Form -->
                <div 
                    x-show="tab === 'form' || isDesktop"
                    class="relative max-w-full bg-[#F3F4F6] overflow-hidden shadow-md p-3 pb-20 max-h-[calc(100vh)] overflow-y-auto
                        [&::-webkit-scrollbar]:w-1
                        [&::-webkit-scrollbar-track]:rounded-full
                        [&::-webkit-scrollbar-track]:bg-gray-100
                        [&::-webkit-scrollbar-thumb]:rounded-full
                        [&::-webkit-scrollbar-thumb]:bg-gray-300
                        dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                        dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500"
                >
                    @include('partials.laporan-lct-form', [
                        'laporan' => $laporan,
                        'departemen' => $departemen,
                        'picDepartemen' => $picDepartemen,
                        'bukti_temuan' => $bukti_temuan,
                    ])
                </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Alpine: tabs logic --}}
    <script>
        function tabs() {
            return {
                tab: 'report',
                isDesktop: window.innerWidth >= 768,
                init() {
                    this.updateScreen()
                    window.addEventListener('resize', this.updateScreen.bind(this))
                },
                updateScreen() {
                    this.isDesktop = window.innerWidth >= 768;
                }
            }
        }
    </script>

    {{-- Alpine untuk form --}}
    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("departemenPic", () => ({
                departemen: [],
                filteredPics: [],
                selectedDept: "",
                selectedPic: "",
                open: false,
                openPic: false,
                initData(departemenData, picDepartemenData) {
                    this.departemen = departemenData;
                    this.picDepartemen = picDepartemenData;
                },
                updatePIC() {
                    if (!this.selectedDept) return;
                    this.filteredPics = this.picDepartemen
                        .filter(item => item.departemen_id == this.selectedDept)
                        .map(item => item.pic);
                }
            }));
        });
    </script>

    {{-- Modal Gambar --}}
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
