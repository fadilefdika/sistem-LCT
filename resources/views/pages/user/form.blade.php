@extends('layouts.user-layout')

@section('content')
<div class="container mx-auto px-4 sm:px-6 md:px-8 mt-3 mb-6">
    <div class="flex justify-center">
        <div class="w-full max-w-md md:max-w-lg lg:max-w-2xl">

            <!-- Card Header: Logo dan Deskripsi -->
            <div class="bg-white shadow-lg rounded-lg text-center mb-6 p-4 sm:p-6">
                <!-- Logo Perusahaan -->
                <div class="flex justify-center mt-4 mb-4">
                    <img src="{{ asset('images/LOGO-AVI-OFFICIAL.png') }}" alt="Logo Perusahaan" class="w-2/4 max-w-xs">
                </div>

                <!-- Deskripsi -->
                <div>
                    <h4 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-3">Laporkan Temuan Ketidaksesuaian Anda</h4>
                    <p class="text-gray-600 text-sm sm:text-base">
                        Temuan ketidaksesuaian atau LCT (Laporan Ketidaksesuaian Temuan) sangat penting untuk memastikan bahwa segala aspek operasional tetap berjalan sesuai dengan standar keselamatan dan prosedur yang telah ditetapkan.
                    </p>
                </div>
            </div>

            <!-- Card Form -->
            <div class="bg-white shadow-lg rounded-lg p-4 sm:p-6">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Nama -->
                    <div class="mb-4">
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama <span class="text-red-500">*</span></label>
                        <input type="text" id="nama" name="nama" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- NPK -->
                    <div class="mb-4">
                        <label for="no_npk" class="block text-sm font-medium text-gray-700">NPK <span class="text-red-500">*</span></label>
                        <input type="number" id="no_npk" name="no_npk" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Tanggal Temuan -->
                    <div class="mb-4">
                        <label for="tanggal_temuan" class="block text-sm font-medium text-gray-700">Tanggal Temuan <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_temuan" name="tanggal_temuan" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Area -->
                    <div class="mb-4">
                        <label for="area" class="block text-sm font-medium text-gray-700">Area <span class="text-red-500">*</span></label>
                        <input type="text" id="area" name="area" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Temuan Ketidaksesuaian -->
                    <div class="mb-4">
                        <label for="temuan" class="block text-sm font-medium text-gray-700">Temuan Ketidaksesuaian <span class="text-red-500">*</span></label>
                        <input type="text" id="temuan" name="temuan" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Unggah Foto -->
                    <div class="mb-4">
                        <label for="foto_temuan" class="block text-sm font-medium text-gray-700">Unggah Foto <span class="text-red-500">*</span></label>
                        <input type="file" id="foto_temuan" name="foto_temuan" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Kategori -->
                    <div class="mb-4">
                        <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                        <input type="text" id="kategori" name="kategori" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Rekomendasi Safety -->
                    <div class="mb-4">
                        <label for="rekomendasi" class="block text-sm font-medium text-gray-700">Rekomendasi Safety <span class="text-red-500">*</span></label>
                        <textarea id="rekomendasi" name="rekomendasi" rows="4" class="mt-2 w-full px-4 py-2 border rounded-md shadow-sm focus:ring-2 focus:ring-blue-500" required></textarea>
                    </div>

                    <!-- Tombol Kirim -->
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md focus:ring-2 focus:ring-blue-500">Kirim Laporan</button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
