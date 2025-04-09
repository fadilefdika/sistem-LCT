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

    <p>Please review the above and make the necessary changes as soon as possible.</p>

    <p>Feel free to contact the administrator if you need any assistance.</p>

    <p>Best regards,<br>
    Manajer</p>
</body>
</html>
