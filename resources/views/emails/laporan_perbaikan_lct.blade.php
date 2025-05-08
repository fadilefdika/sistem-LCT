<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Report</title>
</head>
<body style="background-color: #f3f4f6; font-family: Arial, sans-serif; padding: 20px; margin: 0;">

    <div style="max-width: 600px; background-color: #ffffff; padding: 20px; margin: auto; border: 1px solid #ddd;">
        @if ($laporan->tingkat_bahaya === 'Medium' || $laporan->tingkat_bahaya === 'High')
            <div style="background-color: #fff3cd; padding: 12px; border: 1px solid #ffeeba; border-radius: 5px; margin-bottom: 16px;">
                <strong style="color: #856404;">⚠️ Note:</strong> This repair report is marked as <strong>{{ strtoupper($laporan->tingkat_bahaya) }}</strong> risk level. Temporary approval is required from EHS.
            </div>
        @endif

        <h2 style="font-size: 18px; font-weight: bold; color: #333; margin-bottom: 16px;">
            🔧 Repair Report Submitted by PIC 
            <span style="color: #007BFF;">{{ $laporan->picUser->fullname }}</span>
        </h2>

        <!-- Report Table -->
        <div style="overflow-x: auto;">
            <table width="100%" border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; border: 1px solid #000;">
                <thead>
                    <tr bgcolor="#f0f0f0">
                        <th style="border: 1px solid #000; text-align: left; padding: 8px; font-weight: bold;">Category</th>
                        <th style="border: 1px solid #000; text-align: left; padding: 8px; font-weight: bold;">Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Finding</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->temuan_ketidaksesuaian }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Hazard Level</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->tingkat_bahaya }}</td>
                    </tr>                    
                    <tr>
                        <td style="border: 1px solid #000; padding: 8px; font-weight: bold;">Completion Date</td>
                        <td style="border: 1px solid #000; padding: 8px;">{{ $laporan->date_completion }}</td>
                    </tr>
                    @if ($laporan->bukti_perbaikan)
                        <div style="margin-top: 20px;">
                            <p style="font-size: 14px; font-weight: bold; color: #333;">📷 Repair Photo:</p>
                            <img src="{{ asset('storage/' . $laporan->foto_perbaikan) }}" alt="Repair Photo" style="max-width: 100%; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Link to Detailed Report -->
        <div style="margin-top: 20px;">
            <p style="font-size: 14px; font-weight: bold; color: #333;">🔗 Report Details:</p>
            <a href="{{ url('/ehs/progress-perbaikan/' . $laporan->id_laporan_lct) }}" 
               style="font-size: 14px; color: #007BFF; text-decoration: underline; word-wrap: break-word;">
                {{ url('/ehs/progress-perbaikan/' . $laporan->id_laporan_lct) }}
            </a>
        </div>

        <p style="margin-top: 20px; font-size: 13px; color: #666; font-style: italic;">
            Please review the repair report and confirm its completion. ✅
        </p>
    </div>

</body>
</html>
