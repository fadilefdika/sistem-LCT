<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\LaporanLct;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
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
        $bukti_temuan_array = is_string($laporan->bukti_temuan)
            ? json_decode($laporan->bukti_temuan, true)
            : (is_array($laporan->bukti_temuan) ? $laporan->bukti_temuan : []);

        $bukti_temuan = '-';
        if (!empty($bukti_temuan_array[0])) {
            $bukti_temuan = '=HYPERLINK("' . asset('storage/' . $bukti_temuan_array[0]) . '", "Lihat Gambar")';
        }

        $bukti_perbaikan_array = is_string($laporan->bukti_perbaikan)
            ? json_decode($laporan->bukti_perbaikan, true)
            : (is_array($laporan->bukti_perbaikan) ? $laporan->bukti_perbaikan : []);

        $bukti_perbaikan = '-';
        if (!empty($bukti_perbaikan_array[0])) {
            $bukti_perbaikan = '=HYPERLINK("' . asset('storage/' . $bukti_perbaikan_array[0]) . '", "Lihat Gambar")';
        }

        $tingkatBahaya = strtolower($laporan->tingkat_bahaya);
        $today = Carbon::now();

        $due_date = null;
        $due_date_temp = '-';
        $due_date_perm = null;
        $date_completion = '-';
        $date_completion_temp = '-';
        $date_completion_perm = '-';

        if ($tingkatBahaya === 'low') {
            $due_date = $laporan->due_date ? Carbon::parse($laporan->due_date) : null;
            $date_completion = $laporan->date_completion ?? '-';
        } else {
            $due_date_temp = $laporan->due_date_temp ?? '-';
            $due_date_perm = $laporan->due_date_perm ? Carbon::parse($laporan->due_date_perm) : null;
            $date_completion_temp = $laporan->date_completion_temp ?? '-';
            $date_completion_perm = $laporan->date_completion_perm ?? '-';
        }

        // Days Overdue logic
        $overdueDays = '-';
        if ($tingkatBahaya === 'low' && $due_date instanceof \Carbon\Carbon) {
            // Jangan hitung jika sudah selesai (ada date_completion)
            if (empty($laporan->date_completion)) {
                $overdueDays = $due_date->lt($today) ? (int) $due_date->diffInDays($today) : 0;
            }
        } elseif (in_array($tingkatBahaya, ['medium', 'high']) && $due_date_perm instanceof \Carbon\Carbon) {
            // Jangan hitung jika sudah selesai (ada date_completion_perm)
            if (empty($laporan->date_completion_perm)) {
                $overdueDays = $due_date_perm->lt($today) ? (int) $due_date_perm->diffInDays($today) : 0;
            }
        }


        return [
            $laporan->id_laporan_lct,
            $laporan->tanggal_temuan,
            $laporan->temuan_ketidaksesuaian ?? '-',
            $bukti_temuan,
            $laporan->tingkat_bahaya ?? '-',
            $laporan->kategori->nama_kategori ?? '-',
            $laporan->area->nama_area ?? '-',
            $laporan->detail_area ?? '-',
            ucwords(str_replace('_', ' ', $laporan->status_lct)),
            $laporan->picUser->fullname ?? '-',
            $laporan->departemen->user->fullname ?? '-',
            $laporan->departemen->nama_departemen ?? '-',
            $due_date ? $due_date->format('Y-m-d') : '-',
            $due_date_temp,
            $due_date_perm ? $due_date_perm->format('Y-m-d') : '-',
            $date_completion,
            $date_completion_temp,
            $date_completion_perm,
            $laporan->estimated_budget ?? '-',
            $bukti_perbaikan,
            $overdueDays,
        ];
    }

    public function headings(): array
    {
        return [
            'ID LCT',
            'Tanggal Temuan',
            'Temuan',
            'Foto Temuan',
            'Tingkat Bahaya',
            'Jenis Temuan',
            'Lokasi Temuan',
            'Detail Lokasi',
            'Status',
            'PIC',
            'Manager',
            'Departemen',
            'Due Date (Low)',
            'Due Date Temporary',
            'Date Date Permanent',
            'Date of Completion (Low)',
            'Date of Completion Temporary',
            'Date of Completion Permanent',
            'Estimated Budget',
            'Foto Closed',
            'Days Overdue',
        ];
    }
}
