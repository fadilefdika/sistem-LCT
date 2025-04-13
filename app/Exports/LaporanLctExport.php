<?php
namespace App\Exports;

use App\Models\LaporanLct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class LaporanLctExport implements FromCollection, WithHeadings
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
        return LaporanLct::with('picUser')
        ->whereBetween('tanggal_temuan', [$this->start, $this->end])
        ->get()
        ->map(function ($laporan) {
            return [
                'Report ID' => $laporan->id_laporan_lct,
                'Photo Non-Conformity Findings' => $laporan->foto_temuan ?? '-', // sesuaikan field
                'Date of Finding' => $laporan->tanggal_temuan,
                'Non-Conformity Findings' => $laporan->temuan_ketidaksesuaian,
                'Status' => ucwords(str_replace('_', ' ', $laporan->status_lct)),
                'Location' => $laporan->lokasi_temuan ?? '-',
                'Due Date' => $laporan->due_date ?? '-',
                'SVP Name' => $laporan->picUser->fullname ?? '-',
                'Hazard Level' => $laporan->tingkat_bahaya,
                'Completion Date' => $laporan->date_completion ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Report ID',
            'Photo Non-Conformity Findings',
            'Date of Finding',
            'Non-Conformity Findings',
            'Status',
            'Location',
            'Due Date',
            'SVP Name',
            'Hazard Level',
            'Completion Date',
        ];
    }
}
