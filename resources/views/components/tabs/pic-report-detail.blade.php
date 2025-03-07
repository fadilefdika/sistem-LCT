<div class="max-h-screen flex justify-center items-center">
    <div class="grid md:grid-cols-2 justify-center w-full overflow-hidden">
        <!-- Card Laporan dari EHS -->
        <div class="w-full mx-auto bg-[#F3F4F6] max-h-[calc(100vh)] pb-28 overflow-y-auto 
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
        <div class="max-w-full bg-[#F3F4F6] px-3 pt-3 pb-32 max-h-[calc(100vh)] overflow-y-auto [&::-webkit-scrollbar]:w-1
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
</div>
