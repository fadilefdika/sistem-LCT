<x-app-layout>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 p-3 justify-center">
        <!-- Card 1: Laporan dari Pelapor -->
        <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="bg-primary text-black text-center py-6">
                <h5 class="text-2xl font-semibold">Laporan dari Pelapor</h5>
            </div>
            <div class="px-6 py-3">
                <div class="space-y-4">
                    <p><strong class="text-gray-700">Temuan Ketidaksesuaian:</strong><br>Kerusakan Tempat Sampah</p>
                    <p><strong class="text-gray-700">Nama Pelapor:</strong><br>Aziz</p>
                    <p><strong class="text-gray-700">Tanggal Temuan:</strong><br>20-12-2024</p>
                    <p><strong class="text-gray-700">Area Temuan:</strong><br>Gudang A</p>
                    <p><strong class="text-gray-700">Kategori Temuan:</strong><br>Kondisi Tidak Aman</p>
                    <p><strong class="text-gray-700">Rekomendasi Safety:</strong><br>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Expedita voluptates, maxime assumenda voluptatibus, iste quidem natus dignissimos nesciunt vero voluptate omnis officiis commodi fuga vel. Mollitia earum, ipsum deserunt tempore in reiciendis ipsa obcaecati expedita! Consequatur excepturi ipsum nulla dicta aperiam qui expedita soluta quam? Delectus possimus dolorum repudiandae cumque provident corporis aut tenetur ea dolor nisi repellendus illum ad, veniam a non modi ullam nesciunt similique laboriosam iure excepturi dolores molestias totam culpa! Accusamus recusandae facilis vero tenetur dolorum voluptatem, beatae cupiditate maiores sit tempora voluptatum reprehenderit laboriosam totam facere ipsum? Amet et qui nulla, quis adipisci mollitia alias!</p>
                </div>
        
                <div class="mt-6">
                    <p class="mb-3"><strong class="text-gray-700">Gambar Temuan:</strong></p>
                    <img src="{{ asset('images/user-36-05.jpg') }}" class="w-full h-auto rounded-md shadow-md object-cover" alt="Gambar Temuan">
                </div>
            </div>
        </div>
    
        <!-- Card 2: Laporan dari PIC -->
        <div class="w-full mx-auto bg-white shadow-lg rounded-lg border max-h-max">
            <div class="bg-yellow-500 text-dark text-center py-4 rounded-t-lg">
                <h5 class="text-xl font-semibold">Laporan dari PIC</h5>
            </div>
            <div class="px-6 py-4">
                <div class="space-y-4">

                    <p><strong>Temuan Ketidaksesuaian:</strong><br>Mesin rusak dan bocor oli</p>
                    <p><strong>Nama PIC:</strong><br>Jane Smith</p>
                    <p><strong>Status Bahaya:</strong><br> <span class="bg-red-500 text-white px-2 py-1 rounded-full text-sm">Tinggi ⚠️</span></p>
                    <p><strong>Batas Waktu Perbaikan:</strong><br>2025-02-15</p>
                    <p><strong>Status LCT:</strong><br> <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-sm">Dalam Proses</span></p>
                    <p><strong>Kategori Temuan:</strong><br>Kerusakan Mesin</p>
                    <div class="mt-4">
                        <p class="mb-3"><strong>Gambar Hasil Perbaikan:</strong></p>
                        <img src="{{ asset('images/user-36-09.jpg') }}" class="w-full h-auto rounded-lg shadow-sm" alt="Gambar Hasil Perbaikan">
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <button class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">Approve ✅</button>
                </div>
            </div>
        </div>
    
        <!-- Card 3: Reject Form -->
        <div class="w-full mx-auto bg-white shadow-lg rounded-lg border max-h-max">
            <div class="bg-red-500 text-white text-center py-4 rounded-t-lg">
                <h5 class="text-xl font-semibold">Menolak Hasil Perbaikan</h5>
            </div>
            <div class="p-4">
                <label for="rejectReason" class="block text-sm font-medium text-gray-700"><strong>Alasan Penolakan:</strong></label>
                <textarea id="rejectReason" rows="3" placeholder="Isi alasan penolakan..." class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
                <div class="mt-4 text-center">
                    <button class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">Reject ❌</button>
                </div>
            </div>
        </div>
    </div>
    
</x-app-layout>