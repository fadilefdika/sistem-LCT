<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approval Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #fff; padding: 20px; border: 1px solid #ddd;">
        <h2 style="color: #28a745;">✅ Report Approved</h2>
        <p>Below is a summary of the approved report:</p>

        <table style="width: 100%; border-collapse: collapse;" border="1" cellpadding="8">
            <tr>
                <td><strong>PIC</strong></td>
                <td>{{ $laporan->picUser->fullname }}</td>
            </tr>
            <tr>
                <td><strong>Finding</strong></td>
                <td>{{ $laporan->temuan_ketidaksesuaian }}</td>
            </tr>
            <tr>
                <td><strong>Corrective Action</strong></td>
                <td>{{ $laporan->tindakan_perbaikan }}</td>
            </tr>
            <tr>
                <td><strong>Current Status</strong></td>
                <td>{{ $laporan->status_lct }}</td>
            </tr>
            <tr>
                <td><strong>Approval Date</strong></td>
                <td>{{ \Carbon\Carbon::now()->format('F j, Y, g:i a') }}</td>
            </tr>            
        </table>

        <p style="margin-top: 20px;">
            🔗 You can view the full report details at: <br>
            <a href="{{ url('/manajemen-lct/' . $laporan->id_laporan_lct) }}" style="color: #007bff;">
                {{ url('/manajemen-lct/' . $laporan->id_laporan_lct) }}
            </a>
        </p>

        <p style="margin-top: 20px; font-style: italic; color: #666;">
            This email was sent automatically as a notification regarding the approval of a repair report.
        </p>
    </div>
</body>
</html>
