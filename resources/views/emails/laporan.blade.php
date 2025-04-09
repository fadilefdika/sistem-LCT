<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nonconformity Report</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
    <div style="background-color: #ffffff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #333;">📢 New Nonconformity Report</h2>
        <p>The following is a newly submitted nonconformity report by <strong>{{ $laporan->user->fullname }}</strong>.</p>

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td><strong>Report ID</strong></td>
                <td>{{ $laporan->id_laporan_lct }}</td>
            </tr>
            <tr>
                <td><strong>Finding Date</strong></td>
                <td>{{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td><strong>Area</strong></td>
                <td>{{ $laporan->area }} - {{ $laporan->detail_area }}</td>
            </tr>
            <tr>
                <td><strong>Category</strong></td>
                <td>{{ $laporan->kategori->nama_kategori ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Finding</strong></td>
                <td>{{ $laporan->temuan_ketidaksesuaian }}</td>
            </tr>
            <tr>
                <td><strong>Safety Recommendation</strong></td>
                <td>{{ $laporan->rekomendasi_safety }}</td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td>{{ strtoupper($laporan->status_lct) }}</td>
            </tr>
        </table>

        <p style="margin-top: 20px;">
            👉 <a href="{{ url('/laporan-lct/' . $laporan->id_laporan_lct) }}" style="color: #007BFF;">Click here to view the full report details</a>
        </p>

        <p style="margin-top: 30px; font-size: 14px; color: #777;">
            This email was automatically sent by the Nonconformity Report System.
        </p>
    </div>
</body>
</html>
