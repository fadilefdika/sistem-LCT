<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Ketidaksesuaian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <div class="max-w-2xl w-full bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            🚨 Laporan Ketidaksesuaian Telah Dikirim ke PIC 
            <span class="text-blue-600">{{ $laporan->picUser->fullname }}</span>
        </h2>

        <div class="border-t border-gray-300 pt-4 space-y-3 text-gray-700 text-sm">
            <div class="flex justify-between">
                <span class="font-semibold text-gray-900">Temuan:</span>
                <span class="text-right">{{ $laporan->temuan_ketidaksesuaian }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-900">Tanggal Temuan:</span>
                <span class="text-right">{{ $laporan->tanggal_temuan }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-900">Area:</span>
                <span class="text-right">{{ $laporan->area }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-900">Detail Area:</span>
                <span class="text-right">{{ $laporan->detail_area }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-900">Tingkat Bahaya:</span>
                <span class="text-right">{{ $laporan->tingkat_bahaya }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-900">Rekomendasi:</span>
                <span class="text-right">{{ $laporan->rekomendasi }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold text-gray-900">Due Date:</span>
                <span class="text-right">{{ $laporan->due_date }}</span>
            </div>
        </div>

        <!-- Link ke detail laporan -->
        <div class="mt-6">
            <p class="text-sm font-semibold text-gray-900">🔗 Link Detail Laporan:</p>
            <a href="http://127.0.0.1:8000/manajemen-lct/{{ $laporan->id_laporan_lct }}" 
               class="block text-blue-600 underline break-all">
                http://127.0.0.1:8000/manajemen-lct/{{ $laporan->id_laporan_lct }}
            </a>
        </div>

        <p class="mt-4 text-sm text-gray-600 italic">
            Silakan segera menindaklanjuti laporan ini untuk tindakan perbaikan. 🔍✅
        </p>
    </div>

</body>
</html>
