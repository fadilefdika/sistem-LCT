<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
    <title>Budget Approval Request</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f7; font-family: Arial, sans-serif; color: #000;">

    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 25px 30px; border: 1px solid #ddd; border-radius: 6px; box-shadow: 0 0 8px rgba(0,0,0,0.05);">
        
        <h2 style="color: #000000; font-weight: 700; margin-bottom: 20px;">📝 Budget Approval Request – Finding Reports [EHSight]</h2>

        <p style="font-size: 16px; line-height: 1.5; margin-bottom: 15px;">
            Dear {{ $laporan->departemen->user->fullname ?? 'User' }},
        </p>

        <p style="font-size: 15px; line-height: 1.5; margin-bottom: 25px;">
            The following budget estimations have been submitted and are awaiting your approval in <strong>EHSight</strong>.
        </p>

        <hr style="border-color: #ddd; margin-bottom: 25px;">

        <p style="font-size: 15px; line-height: 1.5; margin: 6px 0;">
            <strong>Submitted By:</strong> {{ $laporan->picUser->fullname }}
        </p>
        <p style="font-size: 15px; line-height: 1.5; margin: 6px 0;">
            <strong>Finding:</strong> {{ $laporan->temuan_ketidaksesuaian }}
        </p>
        <p style="font-size: 15px; line-height: 1.5; margin: 6px 0 25px;">
            <strong>Estimated Budget:</strong> Rp {{ number_format($laporan->estimated_budget, 0, ',', '.') }}
        </p>

        <h3 style="font-weight: 600; margin-bottom: 10px; color: #000000;">📋 Task List</h3>

        <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; color: #000;">
            <thead style="background-color: #f9f9f9;">
                <tr>
                    <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Task</th>
                    <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">PIC</th>
                    <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Due Date</th>
                    <th style="text-align: left; padding: 10px; border-bottom: 2px solid #ddd;">Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                    <tr>
                        <td style="padding: 8px 10px; border-bottom: 1px solid #eee;">{{ $task->task_name }}</td>
                        <td style="padding: 8px 10px; border-bottom: 1px solid #eee;">{{ $task->pic->name ?? '-' }}</td>
                        <td style="padding: 8px 10px; border-bottom: 1px solid #eee;">{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}</td>
                        <td style="padding: 8px 10px; border-bottom: 1px solid #eee;">{{ $task->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr style="border-color: #ddd; margin: 30px 0;">

        <p style="text-align: center; margin-bottom: 30px;">
            <a href="{{ url('/budget-approval/' . $laporan->id_laporan_lct) }}"
               style="background-color: #007bff; color: #ffffff; padding: 12px 28px; text-decoration: none; border-radius: 5px; font-weight: 600; display: inline-block;">
                View LCT Report
            </a>
        </p>

        <p style="font-size: 15px; line-height: 1.5; margin-bottom: 6px;">
            Please log in to <strong>EHSight</strong> to review and approve the requests.
        </p>

        <p style="font-size: 15px; line-height: 1.5; margin-bottom: 6px;">
            Thank you,
        </p>

        <p style="font-size: 15px; line-height: 1.5; margin-bottom: 6px;">
            EHSight System
        </p>

        <p style="font-size: 13px; color: #666; margin-top: 30px;">
            Spot it. Report it. Track it. Fix it.
        </p>
    </div>

</body>
</html>
