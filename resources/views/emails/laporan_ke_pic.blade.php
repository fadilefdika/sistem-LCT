<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Finding Report</title>
</head>
<body style="background-color: #f3f4f6; font-family: Arial, sans-serif; padding: 20px; margin: 0;">

    <div style="max-width: 600px; background-color: #ffffff; padding: 20px; margin: auto; border: 1px solid #ddd;">
        <h2 style="font-size: 18px; font-weight: bold; color: #333; margin-bottom: 16px;">
            Dear {{ $laporan->picUser->fullname }},
        </h2>

        <!-- Report Table -->
        <div style="overflow-x: auto;">
            <table 
                width="100%" 
                border="1" 
                cellpadding="8" 
                cellspacing="0" 
                style="border-collapse: collapse; border: 1px solid #000;"
            >
                <thead>
                    <tr bgcolor="#f0f0f0">
                        <th style="border: 1px solid #000; text-align: left; padding: 8px; font-weight: bold;">
                            Category
                        </th>
                        <th style="border: 1px solid #000; text-align: left; padding: 8px; font-weight: bold;">
                            Details
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Finding</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->temuan_ketidaksesuaian }}</td>
                    </tr>
                    <tr bgcolor="#f9f9f9">
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Finding Date</td>
                        <td style="border: 1px solid #000; padding: 8px;">
                            {{ \Carbon\Carbon::parse($laporan->tanggal_temuan)->format('d M Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Area</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->area->nama_area }}</td>
                    </tr>
                    <tr bgcolor="#f9f9f9">
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Detail Area</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->detail_area }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Hazard Level</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->tingkat_bahaya }}</td>
                    </tr>
                    <tr bgcolor="#f9f9f9">
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Recommendation</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->rekomendasi }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Due Date</td>
                        <td style="border: 1px solid #000; padding: 8px;">
                            {{ \Carbon\Carbon::parse($laporan->due_date)->format('d M Y') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Report Detail Link -->
        <div style="margin-top: 20px;">
            <p style="font-size: 14px; font-weight: bold; color: #333;">
                🔗 Report Detail Link:
            </p>
            <a href="{{ config('app.url') . '/manajemen-lct/' . $laporan->id_laporan_lct }}" 
               style="font-size: 14px; color: #007BFF; text-decoration: underline; word-wrap: break-word;">
                {{ config('app.url') . '/manajemen-lct/' . $laporan->id_laporan_lct }}
            </a>
        </div>

        <p style="margin-top: 20px; font-size: 13px; color: #666; font-style: italic;">
            Please review and follow up accordingly in <strong>EHSight</strong>.
        </p>

        <p>Best regards,</p>
        <p>EHSight System</p>
        <p>Spot it. Report it. Track it. Fix it</p>
    </div>

</body>
</html>
