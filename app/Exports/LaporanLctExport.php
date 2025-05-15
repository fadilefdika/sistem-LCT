<?php

namespace App\Exports;

use App\Models\LaporanLct;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanLctExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        return LaporanLct::with(['kategori', 'area', 'picUser'])
            ->whereBetween('tanggal_temuan', [$this->start, $this->end])
            ->get();
    }

    public function map($laporan): array
    {
        $bukti_temuan = collect(json_decode($laporan->bukti_temuan, true))
            ->map(fn($path) => '=HYPERLINK("' . asset('storage/' . $path) . '", "Lihat Gambar")')
            ->implode(', ');

        $bukti_perbaikan = collect(json_decode($laporan->bukti_perbaikan, true))
            ->map(fn($path) => '=HYPERLINK("' . asset('storage/' . $path) . '", "Lihat Gambar")')
            ->implode(', ');

        return [
            $laporan->tanggal_temuan,
            $laporan->temuan_ketidaksesuaian ?? '-',
            $bukti_temuan ?: '-',
            $laporan->kategori->nama_kategori ?? '-',
            $laporan->area->nama_area ?? '-',
            $laporan->detail_area ?? '-',
            ucwords(str_replace('_', ' ', $laporan->status_lct)),
            $laporan->picUser->fullname ?? '-',
            $laporan->due_date ?? '-',
            $laporan->date_completion ?? '-',
            $bukti_perbaikan ?: '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal Temuan',
            'Temuan',
            'Foto Temuan',
            'Jenis Temuan',
            'Lokasi Temuan',
            'Detail Lokasi',
            'Status',
            'PIC',
            'Due Date',
            'Date of Completion',
            'Foto Closed',
        ];
    }
}
