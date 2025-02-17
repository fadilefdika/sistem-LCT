<x-app-layout>
    <div class="grid md:grid-cols-2 gap-3 justify-center p-3">
        <!-- Card Laporan dari Pelapor -->
        <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-xl overflow-hidden">
            <div class="bg-primary text-black text-center py-6">
                <h5 class="text-2xl font-semibold">Laporan dari Pelapor</h5>
            </div>
            <div class="px-8 py-3">
                <div class="space-y-4">
                    <p><strong class="text-gray-700">Temuan Ketidaksesuaian:</strong><br>Kerusakan Tempat Sampah</p>
                    <p><strong class="text-gray-700">Nama Pelapor:</strong><br>Aziz</p>
                    <p><strong class="text-gray-700">Tanggal Temuan:</strong><br>20-12-2024</p>
                    <p><strong class="text-gray-700">Area Temuan:</strong><br>Gudang A</p>
                    <p><strong class="text-gray-700">Kategori Temuan:</strong><br>Kondisi Tidak Aman</p>
                    <p><strong class="text-gray-700">Rekomendasi Safety:</strong><br>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Expedita voluptates, maxime assumenda voluptatibus, iste quidem natus dignissimos nesciunt vero voluptate omnis officiis commodi fuga vel. Mollitia earum, ipsum deserunt tempore in reiciendis ipsa obcaecati expedita! Consequatur excepturi ipsum nulla dicta aperiam qui expedita soluta quam? Delectus possimus dolorum repudiandae cumque provident corporis aut tenetur ea dolor nisi repellendus illum ad, veniam a non modi ullam nesciunt similique laboriosam iure excepturi dolores molestias totam culpa! Accusamus recusandae facilis vero tenetur dolorum voluptatem, beatae cupiditate maiores sit tempora voluptatum reprehenderit laboriosam totam facere ipsum? Amet et qui nulla, quis adipisci mollitia alias!</p>
                </div>
        
                <div class="mt-6">
                    <img src="{{ asset('images/user-36-05.jpg') }}" class="w-full h-auto rounded-md shadow-md object-cover" alt="Gambar Temuan">
                </div>
            </div>
        </div>
        
    
        <!-- Form Laporan Temuan -->
        <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-md overflow-hidden max-h-max">
            <div class="bg-primary text-black text-center py-4 px-7">
                <h5 class="text-xl font-bold">Formulir Pengajuan Laporan Ketidaksesuaian ke PIC</h5>
            </div>
            <div class="p-6">
                <form action="#{{-- route('laporan.store') --}}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <!-- Form fields -->
                        <div class="mb-4">
                            <label for="area_temuan" class="block text-sm font-medium text-gray-700">Area Temuan</label>
                            <input type="text" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="area_temuan" name="area_temuan" required>
                        </div>
                        <div class="mb-4">
                            <label for="tanggal_temuan" class="block text-sm font-medium text-gray-700">Tanggal Temuan</label>
                            <input type="date" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="tanggal_temuan" name="tanggal_temuan" required>
                        </div>
                        <div class="mb-4">
                            <label for="nama_pic" class="block text-sm font-medium text-gray-700">Nama PIC</label>
                            <select class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="nama_pic" name="nama_pic" required>
                                <option value="">Pilih PIC</option>
                                <option value="pic1">PIC 1</option>
                                <option value="pic2">PIC 2</option>
                                <option value="pic3">PIC 3</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="tingkat_bahaya" class="block text-sm font-medium text-gray-700">Tingkat Bahaya</label>
                            <select class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="tingkat_bahaya" name="tingkat_bahaya" required>
                                <option value="">Pilih Tingkat Bahaya</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="kategori_temuan" class="block text-sm font-medium text-gray-700">Kategori Temuan</label>
                            <select class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="kategori_temuan" name="kategori_temuan" required>
                                <option value="">Pilih Kategori Temuan</option>
                                <option value="produksi">Produksi</option>
                                <option value="keamanan">Keamanan</option>
                                <option value="lingkungan">Lingkungan</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="departemen" class="block text-sm font-medium text-gray-700">Departemen</label>
                            <select class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="departemen" name="departemen" required>
                                <option value="">Pilih Departemen</option>
                                <option value="produksi">Produksi</option>
                                <option value="keamanan">Keamanan</option>
                                <option value="lingkungan">Lingkungan</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="temuan_ketidaksesuaian" class="block text-sm font-medium text-gray-700">Temuan Ketidaksesuaian</label>
                            <input type="text" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="temuan_ketidaksesuaian" name="temuan_ketidaksesuaian" required>
                        </div>
                        <div class="mb-4">
                            <label for="batas_waktu" class="block text-sm font-medium text-gray-700">Batas Waktu Perbaikan</label>
                            <input type="date" class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="batas_waktu" name="batas_waktu" required>
                        </div>
                        
                        <!-- Rekomendasi -->
                        <div class="mb-4">
                            <label for="rekomendasi" class="block text-sm font-medium text-gray-700">Rekomendasi</label>
                            <textarea class="w-full p-3 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary" id="rekomendasi" name="rekomendasi" rows="4" required></textarea>
                        </div>
            
                        <!-- Submit button -->
                        <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Kirim Laporan</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
    
</x-app-layout>