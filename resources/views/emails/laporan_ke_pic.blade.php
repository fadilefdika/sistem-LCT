<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Ketidaksesuaian</title>
</head>
<body style="background-color: #f3f4f6; font-family: Arial, sans-serif; padding: 20px; margin: 0;">

    <div style="max-width: 600px; background-color: #ffffff; padding: 20px; margin: auto; border: 1px solid #ddd;">
        <h2 style="font-size: 18px; font-weight: bold; color: #333; margin-bottom: 16px;">
            🚨 Laporan Ketidaksesuaian Telah Dikirim ke PIC 
            <span style="color: #007BFF;">{{ $laporan->picUser->fullname }}</span>
        </h2>

        <!-- Tabel Laporan -->
        <div style="overflow-x: auto;">
            <table width="100%" border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; border: 1px solid #000;">
                <thead>
                    <tr bgcolor="#f0f0f0">
                        <th style="border: 1px solid #000; text-align: left; padding: 8px; font-weight: bold;">Kategori</th>
                        <th style="border: 1px solid #000; text-align: left; padding: 8px; font-weight: bold;">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Temuan</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->temuan_ketidaksesuaian }}</td>
                    </tr>
                    <tr bgcolor="#f9f9f9">
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Tanggal Temuan</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->tanggal_temuan }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Area</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->area }}</td>
                    </tr>
                    <tr bgcolor="#f9f9f9">
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Detail Area</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->detail_area }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Tingkat Bahaya</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->tingkat_bahaya }}</td>
                    </tr>
                    <tr bgcolor="#f9f9f9">
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Rekomendasi</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->rekomendasi }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Due Date</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->due_date }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Link ke Detail Laporan -->
        <div style="margin-top: 20px;">
            <p style="font-size: 14px; font-weight: bold; color: #333;">🔗 Link Detail Laporan:</p>
            <a href="http://127.0.0.1:8000/manajemen-lct/{{ $laporan->id_laporan_lct }}" 
               style="font-size: 14px; color: #007BFF; text-decoration: underline; word-wrap: break-word;">
                http://127.0.0.1:8000/manajemen-lct/{{ $laporan->id_laporan_lct }}
            </a>
        </div>

        <p style="margin-top: 20px; font-size: 13px; color: #666; font-style: italic;">
            Silakan segera menindaklanjuti laporan ini untuk tindakan perbaikan. 🔍✅
        </p>
    </div>

</body>
</html>
