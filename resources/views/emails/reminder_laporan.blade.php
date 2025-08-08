@php
    // $recipientType dari Mailable sudah diteruskan ke view
    // Pastikan huruf kecil untuk konsistensi perbandingan
    $roleName = strtolower($recipientType);

    $routeName = ($roleName === 'pic') ? 'manajemen-lct' : (($roleName === 'manager') ? 'reporting' : 'manajemen-lct');
@endphp


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
                <table width="700" cellpadding="0" cellspacing="0" 
                    style="background-color: #ffffff; border-radius: 8px; padding: 32px; 
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);">
                    <tr>
                        <td align="center" style="padding-bottom: 24px;">
                            <h2 style="margin: 0; font-size: 24px; color: #222;">
                                @switch($status)
                                    @case('reminder_2') ⏳ Reminder: Finding Report Due in 2 Days - [EHSight] @break
                                    @case('reminder_1') ⏳ Reminder: Finding Report Due in 1 Day - [EHSight] @break
                                    @case('due_today') 🚨 Reminder: Finding Report Due Today - [EHSight] @break
                                    @case('overdue') ⚠️ Overdue Notice: Finding Report Past Due Date - [EHSight] @break
                                    @case('overdue_manager') ❗ Manager Alert: Finding Report Overdue More Than 2 Days - [EHSight] @break
                                @endswitch
                            </h2>
                        </td>
                    </tr> 

                    <tr>
                        <td style="padding-bottom: 24px; font-size: 16px; line-height: 1.6;">
                            @if ($status === 'overdue_manager')
                                <p>Dear <strong>{{ $laporans->first()->departemen->user->fullname ?? 'User' }}</strong>,</p>
                                <p>
                                    This is to inform you that your assigned PIC,
                                    <strong>{{ $laporans->first()->picUser->fullname ?? 'Unknown PIC' }}</strong>,
                                    has not resolved their Finding which has been overdue for <strong>2 or more days</strong>.
                                </p>
                                <p>
                                    Please follow up with the PIC to ensure timely resolution of this issue.
                                </p>
                            @else
                                <p>Dear <strong>{{ $laporans->first()->picUser->fullname ?? 'User' }}</strong>,</p>
                                @switch($status)
                                    @case('reminder_2')
                                        <p>This is a reminder that your Finding is due in <strong>2 days</strong>.</p>
                                        <p>Please complete and submit it through <strong>EHSight</strong> before the deadline.</p>
                                        @break
                                    @case('reminder_1')
                                        <p>This is a reminder that your Finding is due <strong>tomorrow</strong>.</p>
                                        <p>Please complete and submit it through <strong>EHSight</strong> before the deadline.</p>
                                        @break
                                    @case('due_today')
                                        <p>Your Finding is <strong>due today</strong>. Please ensure it is completed without delay.</p>
                                        <p>Please complete and submit it through <strong>EHSight</strong> before the deadline.</p>
                                        @break
                                    @case('overdue')
                                        <p>Your Finding is <strong>overdue</strong>. Immediate action is required.</p>
                                        <p>Please complete and submit it through <strong>EHSight</strong> as soon as possible.</p>
                                        @break
                                @endswitch                            
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-bottom: 20px; font-size: 15px; line-height: 1.5;">
                            <table width="100%" cellpadding="6" cellspacing="0" border="1" 
                                style="border-collapse: collapse; border-color: #ddd; font-size: 14px;">
                                <thead style="background-color: #f4f4f4;">
                                    <tr>
                                        <th align="center">No</th>
                                        <th align="center">No ID</th>
                                        <th align="center">Hazard Level</th>
                                        <th align="center">Area</th>
                                        <th align="center">Category</th>
                                        @if ($status === 'overdue_manager')
                                            <th align="center">PIC</th>
                                        @endif
                                        {{-- Hapus kolom Action --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($laporans as $index => $laporan)
                                        @php
                                            $url = url("/{$routeName}/{$laporan->id_laporan_lct}");
                                            if (!auth()->check()) {
                                                $path = "/{$routeName}/{$laporan->id_laporan_lct}";
                                                $url = route('login') . '?redirect_to=' . urlencode($path);
                                            }
                                        @endphp
                                        <tr>
                                            <td align="center">{{ $index + 1 }}</td>
                                            <td align="center">
                                                <a href="{{ $url }}">
                                                    {{ $laporan->id_laporan_lct }}
                                                </a>
                                            </td>
                                            <td align="center">{{ ucfirst($laporan->tingkat_bahaya) }}</td>
                                            <td>{{ $laporan->area }} - {{ $laporan->detail_area }}</td>
                                            <td>{{ $laporan->kategori->nama_kategori }}</td>
                                            @if ($status === 'overdue_manager')
                                                <td>{{ $laporan->picUser->fullname ?? '-' }}</td>
                                            @endif
                                            {{-- Kolom Action dihapus --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                                
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p style="font-size: 12px; color: #999; margin-top: 20px;">
                                If the report has already been submitted, please ignore this message.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #555;">
                            <p>Best regards,</p>
                            <p>EHSight System</p>
                            <p>Spot it. Report it. Track it. Fix it</p>
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
