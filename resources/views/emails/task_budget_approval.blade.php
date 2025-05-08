<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget Approval Request</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f6f6f6; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border: 1px solid #ddd;">
        <h2 style="color: #333;">📢 Budget Approval Request</h2>

        <p>Dear Manager,</p>

        <p>A new budget estimation has been submitted and is awaiting your approval.</p>

        <hr>

        <p><strong>Submitted By:</strong> {{ $laporan->picUser->fullname }}</p>
        <p><strong>Finding:</strong> {{ $laporan->temuan_ketidaksesuaian }}</p>
        <p><strong>Estimated Budget:</strong> Rp {{ number_format($laporan->estimated_budget, 0, ',', '.') }}</p>

        <hr>

        <h3>📋 Task List</h3>
          <table style="width: 100%; border-collapse: collapse; margin-top: 10px;" border="1" cellpadding="8">
            <thead style="background-color: #f0f0f0;">
                <tr>
                    <th style="text-align: left;">Task</th>
                    <th style="text-align: left;">PIC</th>
                    <th style="text-align: left;">Due Date</th>
                    <th style="text-align: left;">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                    <tr>
                        <td>{{ $task->task_name }}</td>
                        <td>{{ $task->pic->name ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                        <td>{{ $task->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>      

        <hr>

        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/budget-approval/' . $laporan->id_laporan_lct) }}"
               style="background-color: #3490dc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
                View LCT Report
            </a>
        </p>

        <p>Thanks,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
