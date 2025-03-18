<x-app-layout class="h-screen overflow-hidden">
    <div class="h-full">
        @php
            $userRole = optional(auth()->user()->roleLct->first())->name;
        @endphp

        <div class="grid {{ $userRole === 'manajer' ? 'md:grid-cols-1' : 'md:grid-cols-2' }} justify-center w-full h-full">
            <!-- Card Laporan dari Pelapor -->
            <div class="relative max-w-full bg-[#F3F4F6] overflow-hidden shadow-md p-3 h-full pb-20 max-h-[calc(100vh)] overflow-y-auto 
                [&::-webkit-scrollbar]:w-1
                [&::-webkit-scrollbar-track]:rounded-full
                [&::-webkit-scrollbar-track]:bg-gray-100
                [&::-webkit-scrollbar-thumb]:rounded-full
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">

                @include('partials.laporan-lct-report', [
                    'laporan' => $laporan,
                    'bukti_temuan' => $bukti_temuan,
                ])

            </div>

            <!-- Form Laporan Temuan (Sembunyikan jika role manajer) -->
            @if ($userRole !== 'manajer')
                <div class="relative max-w-full bg-[#F3F4F6] overflow-hidden shadow-md p-3 pb-20 max-h-[calc(100vh)] overflow-y-auto 
                    [&::-webkit-scrollbar]:w-1
                    [&::-webkit-scrollbar-track]:rounded-full
                    [&::-webkit-scrollbar-track]:bg-gray-100
                    [&::-webkit-scrollbar-thumb]:rounded-full
                    [&::-webkit-scrollbar-thumb]:bg-gray-300
                    dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                    dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">

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

    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("departemenPic", () => ({
                departemen: [],       // Daftar departemen
                filteredPics: [],     // PIC sesuai departemen
                selectedDept: "",     // Departemen yang dipilih
                selectedPic: "",      // PIC yang dipilih
                open: false,          // Dropdown Departemen
                openPic: false,       // Dropdown PIC
        
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
