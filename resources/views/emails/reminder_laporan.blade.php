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
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; padding: 32px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
                    <tr>
                        <td align="center" style="padding-bottom: 24px;">
                            <h2 style="margin: 0; font-size: 24px; color: #222;">
                                @switch($status)
                                    @case('reminder_2') ⏳ Reminder: 2 Days Before Due Date @break
                                    @case('reminder_1') ⏳ Reminder: 1 Day Before Due Date @break
                                    @case('due_today') 🚨 Reminder: Due Today @break
                                    @case('overdue') ⚠️ Overdue Notice @break
                                    @case('overdue_manager') ❗ Manager Alert: Overdue Report more than 2 Days @break
                                @endswitch
                            </h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-bottom: 24px; font-size: 16px; line-height: 1.6;">
                            @if ($status === 'overdue_manager')
                                <p>Dear <strong>Manager</strong>,</p>
                                <p>
                                    This is to inform you that your assigned PIC,
                                    <strong>{{ $laporan->picUser->fullname ?? 'Unknown PIC' }}</strong>,
                                    has not resolved their LCT report which has been overdue for <strong>2 or more days</strong>.
                                </p>
                                <p>
                                    Please follow up with the PIC to ensure timely resolution of this issue.
                                </p>
                            @else
                                <p>Dear <strong>{{ $laporan->picUser->fullname ?? 'User' }}</strong>,</p>
                                @switch($status)
                                    @case('reminder_2')
                                        <p>This is a friendly reminder that your LCT report is due in <strong>2 days</strong>.</p>
                                        @break
                                    @case('reminder_1')
                                        <p>This is a reminder that your LCT report is due <strong>tomorrow</strong>.</p>
                                        @break
                                    @case('due_today')
                                        <p>Your LCT report is <strong>due today</strong>. Please ensure it is completed without delay.</p>
                                        @break
                                    @case('overdue')
                                        <p><strong>Attention:</strong> Your LCT report is <strong>overdue</strong>. Immediate action is required.</p>
                                        @break
                                @endswitch
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-bottom: 20px; font-size: 15px; line-height: 1.5;">
                            <p><strong>Finding:</strong> {{ $laporan->temuan_ketidaksesuaian }}</p>

                            @php
                                $displayDueDate = $laporan->tingkat_bahaya === 'Low'
                                    ? $laporan->due_date
                                    : ($laporan->status_lct === 'work_permanent'
                                        ? $laporan->due_date_perm
                                        : $laporan->due_date_temp);
                            @endphp

                            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($displayDueDate)->format('d M Y') }}</p>
                            <p><strong>Hazard Level:</strong> {{ ucfirst($laporan->tingkat_bahaya) }}</p>
                            @if ($status === 'overdue_manager')
                            <p><strong>PIC Name:</strong> {{ $laporan->picUser->fullname ?? '-' }}</p>
                            <p><strong>PIC Email:</strong> {{ $laporan->picUser->email ?? '-' }}</p>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 30px 0;">
                            <a href="{{ url('/manajemen-lct/' . $laporan->id_laporan_lct) }}"
                               style="background-color: #007B55; color: #fff; padding: 12px 28px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                                View Report
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="font-size: 14px; color: #555; text-align: center;">
                            <p>Thank you,<br><strong>LCT System</strong></p>
                        </td>
                    </tr>
                </table>

                <p style="font-size: 12px; color: #999; margin-top: 20px; text-align: center;">
                    This is an automated message. Please do not reply directly to this email.
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
