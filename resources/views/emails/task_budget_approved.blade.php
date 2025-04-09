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

    <p>You are kindly requested to proceed with the necessary actions as the assigned Person in Charge (PIC).</p>

    <p>If you have any questions or require further clarification, feel free to reach out.</p>

    <p>Best regards,<br>
    Manajer</p>
</body>
</html>
