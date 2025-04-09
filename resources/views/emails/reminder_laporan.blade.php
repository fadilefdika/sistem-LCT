<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LCT Report Reminder</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f7; font-family: Arial, sans-serif; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 6px; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">
                    <tr>
                        <td align="center" style="padding-bottom: 20px;">
                            <h2 style="margin: 0; font-size: 24px; color: #111;">
                                @switch($status)
                                    @case('reminder_2') ⏳ Reminder: 2 Days Before Due Date @break
                                    @case('reminder_1') ⏳ Reminder: 1 Day Before Due Date @break
                                    @case('due_today') 🚨 Reminder: Due Today @break
                                    @case('overdue') ⚠️ Overdue Notice @break
                                @endswitch
                            </h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-bottom: 20px; font-size: 16px;">
                            <p>Dear <strong>{{ $laporan->picUser->fullname ?? 'User' }}</strong>,</p>

                            @switch($status)
                                @case('reminder_2')
                                    <p>This is a friendly reminder that your LCT report is due in <strong>2 days</strong>.</p>
                                    @break
                                @case('reminder_1')
                                    <p>This is a reminder that your LCT report is due <strong>tomorrow</strong>.</p>
                                    @break
                                @case('due_today')
                                    <p>Your LCT report is <strong>due today</strong>. Please complete it as soon as possible.</p>
                                    @break
                                @case('overdue')
                                    <p><strong>Attention:</strong> Your LCT report is <strong>overdue</strong>. Immediate action is required to complete the report.</p>
                                    @break
                            @endswitch
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-bottom: 20px;">
                            <p><strong>Finding:</strong> {{ $laporan->temuan_ketidaksesuaian }}</p>
                            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($laporan->due_date)->format('d M Y') }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 20px 0;">
                            <a href="{{ url('/manajemen-lct/' . $laporan->id_laporan_lct) }}" style="background-color: #4CAF50; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold;">View Report</a>
                        </td>
                    </tr>

                    <tr>
                        <td style="font-size: 14px; color: #777;">
                            <p>Thank you,<br>Sistem LCT</p>
                        </td>
                    </tr>
                </table>

                <p style="font-size: 12px; color: #aaa; margin-top: 20px;">
                    This is an automated message. Please do not reply.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
