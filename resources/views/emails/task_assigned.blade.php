<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Task Assigned</title>
</head>
<body>
    <h2>Hello, {{ $task->pic->user->fullname ?? 'PIC' }}</h2>

    <p>You have been assigned the following task related to an LCT report:</p>

    <ul>
        <li><strong>Task Name:</strong> {{ $task->task_name }}</li>
        <li><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</li>
        <li><strong>Report ID:</strong> {{ $task->id_laporan_lct }}</li>
        <li><strong>Finding:</strong> {{ $task->laporan->temuan_ketidaksesuaian ?? '-' }}</li>
        <li><strong>Hazard Level:</strong> {{ ucfirst($task->laporan->tingkat_bahaya) ?? '-' }}</li>
        <li><strong>Main PIC:</strong> {{ $task->laporan->user->fullname ?? '-' }}</li>
    </ul>

    <p>Please log into the system to view more details and take the necessary actions:</p>

    <p><a href="{{ url('login') }}?redirect_to={{ route('admin.manajemen-lct.show', $task->laporan->id_laporan_lct, false) }}">
        View Task
    </a></p>

    <br>
    <p>Thank you.</p>
</body>
</html>
