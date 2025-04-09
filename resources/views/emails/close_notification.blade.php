<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Closed Notification</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #fff; padding: 20px; border: 1px solid #ddd;">
        <h2 style="color: #dc3545;">🚫 Report Closed</h2>
        <p>Below is a summary of the report that has been closed:</p>

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
                <td><strong>Status</strong></td>
                <td>{{ $laporan->status_lct }}</td>
            </tr>
            <tr>
                <td><strong>Closed At</strong></td>
                <td>{{ now()->format('F j, Y H:i') }}</td>
            </tr>
        </table>

        <p style="margin-top: 20px;">
            🔗 Report details: <br>
            <a href="{{ url('/manajemen-lct/' . $laporan->id_laporan_lct) }}" style="color: #007bff;">
                {{ url('/manajemen-lct/' . $laporan->id_laporan_lct) }}
            </a>
        </p>

        <p style="margin-top: 20px; font-style: italic; color: #666;">
            This email was sent automatically as a notification of the LCT report closure.
        </p>
    </div>
</body>
</html>
