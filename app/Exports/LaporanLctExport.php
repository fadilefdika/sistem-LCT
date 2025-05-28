<?php

namespace App\Exports;

use App\Models\LaporanLct;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanLctExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $laporans;

    public function __construct($laporans)
    {
        $this->laporans = $laporans;
    }

    public function collection()
    {
        return $this->laporans;
    }

    public function map($laporan): array
    {
        // Ambil gambar pertama dari bukti_temuan
        $bukti_temuan_array = is_string($laporan->bukti_temuan)
            ? json_decode($laporan->bukti_temuan, true)
            : (is_array($laporan->bukti_temuan) ? $laporan->bukti_temuan : []);

        $bukti_temuan = '-';
        if (!empty($bukti_temuan_array[0])) {
            $url = asset('storage/' . $bukti_temuan_array[0]);
            $bukti_temuan = '=HYPERLINK("' . asset('storage/' . $bukti_temuan_array[0]) . '", "Lihat Gambar")';
        }

        // Ambil gambar pertama dari bukti_perbaikan
        $bukti_perbaikan_array = is_string($laporan->bukti_perbaikan)
            ? json_decode($laporan->bukti_perbaikan, true)
            : (is_array($laporan->bukti_perbaikan) ? $laporan->bukti_perbaikan : []);

        $bukti_perbaikan = '-';
        if (!empty($bukti_perbaikan_array[0])) {
            $url = asset('storage/' . $bukti_perbaikan_array[0]);
            $bukti_perbaikan = '=HYPERLINK("' . asset('storage/' . $bukti_perbaikan_array[0]) . '", "Lihat Gambar")';
        }

        return [
            $laporan->tanggal_temuan,
            $laporan->temuan_ketidaksesuaian ?? '-',
            $bukti_temuan,
            $laporan->tingkat_bahaya ?? '-',
            $laporan->kategori->nama_kategori ?? '-',
            $laporan->area->nama_area ?? '-',
            $laporan->detail_area ?? '-',
            ucwords(str_replace('_', ' ', $laporan->status_lct)),
            $laporan->picUser->fullname ?? '-',
            $laporan->due_date ?? '-',
            $laporan->date_completion ?? '-',
            $bukti_perbaikan,
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal Temuan',
            'Temuan',
            'Foto Temuan',
            'Tingkat Bahaya',
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
