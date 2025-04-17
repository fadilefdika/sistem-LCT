<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nonconformity Report Notification</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; border:1px solid #e0e0e0; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="color:#2c3e50; margin-bottom: 20px; font-size: 22px;">📋 New Nonconformity Report Submitted</h2>

                            <p style="font-size:15px; color:#333333; margin-bottom: 20px;">
                                A new report has been submitted by <strong>{{ $laporan->user->fullname }}</strong>. Below are the details:
                            </p>

                            <table width="100%" cellpadding="8" cellspacing="0" border="0" style="border-collapse: collapse; margin-top: 10px;">
                                @php
                                    $rows = [
                                        ['Report ID', $laporan->id_laporan_lct],
                                        ['Finding Date', \Carbon\Carbon::parse($laporan->tanggal_temuan)->translatedFormat('F j, Y')],
                                        ['Area/Detail Area', $laporan->area->nama_area . '/' . $laporan->detail_area],
                                        ['Category', $laporan->kategori->nama_kategori ?? '-'],
                                        ['Finding', $laporan->temuan_ketidaksesuaian],
                                        ['Safety Recommendation', $laporan->rekomendasi_safety],
                                        ['Status', strtoupper($laporan->status_lct)],
                                    ];
                                @endphp

                                @foreach ($rows as $index => [$label, $value])
                                    <tr style="background-color: {{ $index % 2 === 0 ? '#f9f9f9' : '#ffffff' }};">
                                        <td style="border:1px solid #dcdcdc; font-weight:bold; color:#2c3e50;">{{ $label }}</td>
                                        <td style="border:1px solid #dcdcdc; color:#333333;">{{ $value }}</td>
                                    </tr>
                                @endforeach
                            </table>
                            @if ($laporan->bukti_temuan)
                                <div style="margin-top: 20px;">
                                    <p style="font-size: 14px; font-weight: bold; color: #333;">📷 Repair Photo:</p>
                                    <img src="{{ asset('storage/' . $laporan->bukti_temuan) }}" alt="Repair Photo" style="max-width: 100%; border: 1px solid #ccc; border-radius: 4px;">
                                </div>
                            @endif
                            <div style="margin-top:30px; text-align: center;">
                                <a href="{{ url('/laporan-lct/' . $laporan->id_laporan_lct) }}"
                                   style="display:inline-block; background-color:#007bff; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:6px; font-size:14px; font-weight: 600;">
                                    🔍 View Full Report
                                </a>
                            </div>

                            <p style="font-size:12px; color:#999999; margin-top:40px; text-align: center;">
                                This message was sent automatically by the Nonconformity Report System.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
