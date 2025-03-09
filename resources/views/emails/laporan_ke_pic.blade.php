<!DOCTYPE html>
<html>
<head>
    <title>Laporan Ketidaksesuaian</title>
</head>
<body>
    <h2>Laporan Ketidaksesuaian Telah Dikirim ke PIC</h2>
    <p><strong>Temuan:</strong> {{ $laporan->temuan_ketidaksesuaian }}</p>
    <p><strong>Tanggal Temuan:</strong> {{ $laporan->tanggal_temuan }}</p>
    <p><strong>Area:</strong> {{ $laporan->area }}</p>
    <p><strong>Detail Area:</strong> {{ $laporan->detail_area }}</p>
    <p><strong>Tingkat Bahaya:</strong> {{ $laporan->tingkat_bahaya }}</p>
    <p><strong>Rekomendasi:</strong> {{ $laporan->rekomendasi }}</p>
    <p><strong>Due Date:</strong> {{ $laporan->due_date }}</p>

    <p>Silakan segera menindaklanjuti laporan ini.</p>
</body>
</html>
