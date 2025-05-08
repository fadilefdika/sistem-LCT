<!DOCTYPE html>
<html>
<head>
    <title>Task & Budget Revision Required</title>
</head>
<body>
    <h2>Hello {{ $laporan->user->name }},</h2>

    <p>We regret to inform you that your task and budget request (ID: <strong>{{ $laporan->id_laporan_lct }}</strong>) requires revision.</p>

    <p><strong>Reason for Rejection:</strong></p>
    <blockquote>
        {{ $alasanReject->alasan_reject }}
    </blockquote>

    <p><strong>Details:</strong></p>
    <ul>
        <li><strong>Hazard Level:</strong> {{ ucfirst(strtolower($laporan->tingkat_bahaya)) }}</li>
        <li><strong>Status:</strong> Task & Budget Revision Required</li>
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
