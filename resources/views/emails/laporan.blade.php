<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Ketidaksesuaian</title>
</head>
<body>
    <h2>{{ $laporan['judul'] }}</h2>
    <p>{{ $laporan['deskripsi'] }}</p>
    <p><strong>Tanggal:</strong> {{ $laporan['tanggal'] }}</p>
    <p><a href="{{ $laporan['url'] }}">Lihat Detail</a></p>
</body>
</html>
