<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        td, th {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }
        .title {
            font-size: 18pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }
        .section-title {
            font-weight: bold;
            text-align: center;
            background: #f2f2f2;
        }
        .img-box {
            text-align: center;
            vertical-align: middle;
            width: 50%; /* bagi rata */
        }

        .img-box img {
            max-width: 450px;   /* batasi lebar maksimum */
            max-height: 400px;  /* batasi tinggi maksimum */
            width: auto;        /* biar tidak dipaksa full */
            height: auto;
            object-fit: contain;
            border: 1px solid #ccc; /* opsional biar lebih rapih */
            padding: 3px;
        }

    </style>
</head>
<body>
    @foreach($laporans as $laporan)
        <div class="title">SAFETY PATROL REPORT</div>

        <table>
            <tr>
                <td>Observation No/Location</td>
                <td>{{ $laporan->area->nama_area ?? '-' }} ({{ $laporan->detail_area }})</td>
                <td>Category</td>
                <td>{{ $laporan->kategori->nama_kategori ?? '-' }}</td>
            </tr>
            <tr>
                <td>Observation date</td>
                <td>{{ \Carbon\Carbon::parse($laporan->tanggal_temuan ?? $laporan->created_at)->format('d/m/Y') }}</td>
                <td>Due Date</td>
                <td>{{ $laporan->due_date ? \Carbon\Carbon::parse($laporan->due_date)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Responsibility (PIC)</td>
                <td>{{ $laporan->picUser->fullname ?? '-' }}</td>
                <td>Status</td>
                <td>{{ $statusMapping[$laporan->status_lct]['label'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Risk Hazard</td>
                <td>{{ $laporan->tingkat_bahaya ?? '-' }}</td>
                <td>Recommendation</td>
                <td>{{ $laporan->rekomendasi ?? '-' }}</td>
            </tr>
            <tr>
                <td>Finding Item</td>
                <td colspan="3">{{ $laporan->temuan_ketidaksesuaian ?? '-' }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <th class="section-title">BEFORE</th>
                <th class="section-title">AFTER</th>
                {{-- Debug path --}}

            </tr>
            <tr>
                <td class="img-box">
                    @php
                        $bukti = is_array($laporan->bukti_temuan) 
                            ? $laporan->bukti_temuan 
                            : json_decode($laporan->bukti_temuan, true);
            
                        $beforePath = $bukti[0] ?? null;
                    @endphp
            
                    @if($beforePath)
                        <img src="file://{{ public_path('storage/'.$beforePath) }}" alt="Before" >
                    @else
                        <p>Before image not found</p>
                    @endif
                </td>
                <td class="img-box">
                    @php
                        $tindakan = is_array($laporan->tindakan_perbaikan) 
                            ? $laporan->tindakan_perbaikan 
                            : json_decode($laporan->tindakan_perbaikan, true);
            
                        $afterPath = $tindakan[0]['bukti'][0] ?? null;
                    @endphp
            
                    @if($afterPath)
                        <img src="file://{{ public_path('storage/'.$afterPath) }}" alt="After">
                    @else
                        <p>After image not found</p>
                    @endif
                </td>
            </tr>
            
            
        </table>

        <div style="margin-top:20px;">
            Verification by EHS: ______________________
        </div>

        <div style="page-break-after: always;"></div>
    @endforeach
</body>
</html>
