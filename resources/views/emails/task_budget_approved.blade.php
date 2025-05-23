<!DOCTYPE html>
<html>
<head>
    <title>Task & Budget Approval Notification</title>
</head>
<body>
    <h2>Hello {{ $laporan->user->fullname }},</h2>

    <p>We would like to inform you that the task and budget request with the following details has been <strong>approved by the Manager</strong>:</p>

    <p><strong>Report Information:</strong></p>
    <ul>
        <li><strong>Report ID:</strong> {{ $laporan->id_laporan_lct }}</li>
        <li><strong>Hazard Level:</strong> {{ ucfirst(strtolower($laporan->tingkat_bahaya)) }}</li>
        <li><strong>Status:</strong> {{ str_replace('_', ' ', ucfirst($laporan->status_lct)) }}</li>
    </ul>


    <p>Best regards,<br>
    Manajer</p>

    <div style="margin-top: 20px;">
        <p style="font-size: 14px; font-weight: bold; color: #333;">🔗 Report Detail Link:</p>
        <a href="{{ config('app.url') . '/manajemen-lct/' . $laporan->id_laporan_lct }}" 
           style="font-size: 14px; color: #007BFF; text-decoration: underline; word-wrap: break-word;">
            {{ config('app.url') . '/manajemen-lct/' . $laporan->id_laporan_lct }}
        </a>
    </div>
</body>
</html>
