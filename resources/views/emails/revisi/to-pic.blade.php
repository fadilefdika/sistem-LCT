@component('mail::message')
# Non-Conformance Report Revision

Hello {{ $laporan->user->fullname }},

The report with ID **{{ $laporan->id_laporan_lct }}** you submitted has been revised by the EHS team.

**Report Details:**
- **Finding Date:** {{ $laporan->tanggal_temuan->format('d M Y') }}
- **Category:** {{ $laporan->kategori->nama_kategori }}
- **Area:** {{ $laporan->area }} - {{ $laporan->detail_area }}

**Latest Revision Reasons @if($laporan->tingkat_bahaya == 'Low')(Low Level)@elseif($laporan->status_lct == 'temporary_revision')(Temporary)@else(Permanent)@endif:**

@foreach ($alasanRevisi as $revisi)
- {{ $revisi->alasan_reject }}  
  <small><i>{{ \Carbon\Carbon::parse($revisi->created_at)->format('d M Y H:i') }}</i></small>
@endforeach

@component('mail::button', ['url' => url('/manajemen-lct/' . $laporan->id_laporan_lct)])
View Report
@endcomponent

Thank you,<br>
{{ config('app.name') }}
@endcomponent
