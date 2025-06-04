<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\EhsUser;
use Livewire\Component;
use App\Models\LaporanLct;
use Livewire\WithPagination;
use App\Models\LctDepartement;
use Illuminate\Support\Carbon;
use App\Exports\LaporanLctExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Font;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Alignment;


class RiwayatLctTable extends Component
{
    use WithPagination;
    public $rangeType = 'daily';
    public $startDate;
    public $endDate;

    public function render()
    {
        if (Auth::guard('ehs')->check()) {
            $user = Auth::guard('ehs')->user();
            $role = 'ehs';
        } else {
            $user = Auth::guard('web')->user();
            // Ambil dari session terlebih dahulu, fallback ke relasi jika tidak ada
            $role = session('active_role') ?? optional($user->roleLct->first())->name ?? 'guest';
        }
        


        $query = LaporanLct::where('status_lct', 'closed');

        if ($role === 'user') {
            $query->where('user_id', $user->id);
        } elseif ($role === 'manajer') {
            // Ambil departemen yang di-manage oleh user ini
            $departemenId = \App\Models\LctDepartement::where('user_id', $user->id)->value('id');
    
            // Filter berdasarkan departemen_id
            if ($departemenId) {
                $query->where('departemen_id', $departemenId);
            } else {
                // Jika tidak ditemukan, pastikan tidak ada data yang tampil
                $query->whereRaw('1 = 0');
            }
    
        } elseif (!in_array($role, ['ehs'])) {
            // Fallback jika bukan ehs, manajer, atau user
            $picId = \App\Models\Pic::where('user_id', $user->id)->value('id');
            $query->where('pic_id', $picId);
        }

        $laporans = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.riwayat-lct-table', [
            'laporans' => $laporans,
            'role' => $role,
        ]);
    }


    public function exportToPPT()
    {
        $laporans = LaporanLct::where('status_lct', 'closed')
            ->whereDate('date_completion', now()->toDateString())
            ->get();

        if ($laporans->isEmpty()) {
            dd('No report data available for today.');
        }

        $ppt = new \PhpOffice\PhpPresentation\PhpPresentation();
        $ppt->removeSlideByIndex(0); // Remove default slide

        foreach ($laporans as $laporan) {
            $slide = $ppt->createSlide();

            // Add company logo
            $logoPath = public_path('images/LOGO-AVI-OFFICIAL.png');
            if (file_exists($logoPath)) {
                $logo = new \PhpOffice\PhpPresentation\Shape\Drawing\File();
                $logo->setPath($logoPath)
                    ->setHeight(60)
                    ->setWidth(120)
                    ->setOffsetX(720)
                    ->setOffsetY(20);
                $slide->addShape($logo);
            }

            // Title
            $titleShape = $slide->createRichTextShape()
                ->setHeight(50)
                ->setWidth(400)
                ->setOffsetX(20)
                ->setOffsetY(10);
            $titleShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $titleText = $titleShape->createParagraph()->createTextRun("GENBA REPORT");
            $titleText->getFont()->setBold(true)->setSize(20)->setColor(new Color('FF000000'));

            // Data
            $data = [
                "Report ID" => $laporan->id_laporan_lct,
                "Area" => "{$laporan->area->nama_area}/{$laporan->detail_area}",
                "Finding Date" => $laporan->tanggal_temuan,
                "Risk Hazard" => $laporan->tingkat_bahaya,
                "Finding Item" => $laporan->temuan_ketidaksesuaian,
                "Recommendation" => $laporan->rekomendasi,
                "Status" => $laporan->status_lct,
                "Category" => $laporan->kategori->nama_kategori ?? '',
                "Due Date" => $laporan->due_date,
                "Date of Completion" => $laporan->date_completion,
                "Due Date Action Permanent" => $laporan->due_date,
            ];

            $half = ceil(count($data) / 2);
            $dataLeft = array_slice($data, 0, $half, true);
            $dataRight = array_slice($data, $half, null, true);

            // LEFT Table
            $tableLeft = $slide->createTableShape(2)->setOffsetX(40)->setOffsetY(80);
            foreach ($dataLeft as $label => $value) {
                $row = $tableLeft->createRow();
                $row->setHeight(25);
                $value = wordwrap($value, 40, "\n", true);

                $row->getCells()[0]->setWidth(130);
                $row->getCells()[1]->setWidth(270);

                $cell1 = $row->getCells()[0];
                $cell1->createTextRun($label)->getFont()->setBold(true);

                $cell2 = $row->getCells()[1];
                $cell2->createTextRun($value);

                foreach ($row->getCells() as $cell) {
                    $cell->getBorders()->getTop()->setLineStyle(Border::LINE_SINGLE);
                    $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_SINGLE);
                    $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_SINGLE);
                    $cell->getBorders()->getRight()->setLineStyle(Border::LINE_SINGLE);
                }
            }

            // RIGHT Table
            $tableRight = $slide->createTableShape(2)->setOffsetX(460)->setOffsetY(80);
            foreach ($dataRight as $label => $value) {
                $row = $tableRight->createRow();
                $row->setHeight(25);
                $value = wordwrap($value, 40, "\n", true);

                $row->getCells()[0]->setWidth(130);
                $row->getCells()[1]->setWidth(270);

                $cell1 = $row->getCells()[0];
                $cell1->createTextRun($label)->getFont()->setBold(true);

                $cell2 = $row->getCells()[1];
                $cell2->createTextRun($value);

                foreach ($row->getCells() as $cell) {
                    $cell->getBorders()->getTop()->setLineStyle(Border::LINE_SINGLE);
                    $cell->getBorders()->getBottom()->setLineStyle(Border::LINE_SINGLE);
                    $cell->getBorders()->getLeft()->setLineStyle(Border::LINE_SINGLE);
                    $cell->getBorders()->getRight()->setLineStyle(Border::LINE_SINGLE);
                }
            }

            // Add image with label
            $addImageWithLabel = function ($path, $label, $offsetX, $offsetY) use ($slide) {
                $labelShape = $slide->createRichTextShape()
                    ->setHeight(20)
                    ->setWidth(250)
                    ->setOffsetX($offsetX)
                    ->setOffsetY($offsetY - 25);
                $labelShape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $labelShape->createParagraph()->createTextRun($label)->getFont()->setBold(true)->setSize(14);

                if (file_exists($path)) {
                    $image = new \PhpOffice\PhpPresentation\Shape\Drawing\File();
                    $image->setPath($path)
                        ->setHeight(150)
                        ->setWidth(250)
                        ->setOffsetX($offsetX)
                        ->setOffsetY($offsetY);
                    $slide->addShape($image);
                } else {
                    $warning = $slide->createRichTextShape()
                        ->setHeight(40)
                        ->setWidth(250)
                        ->setOffsetX($offsetX)
                        ->setOffsetY($offsetY + 60);
                    $warning->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $warning->createParagraph()->createTextRun('Image not found')->getFont()->setItalic(true)->setSize(12)->setColor(new Color('FF0000'));
                }
            };

            $buktiTemuan = is_string($laporan->bukti_temuan)
                ? json_decode($laporan->bukti_temuan, true)[0] ?? null
                : ($laporan->bukti_temuan[0] ?? null);
            $buktiTemuanPath = $buktiTemuan ? storage_path('app/public/' . $buktiTemuan) : null;

            $buktiPerbaikan = is_string($laporan->bukti_perbaikan)
                ? json_decode($laporan->bukti_perbaikan, true)[0] ?? null
                : ($laporan->bukti_perbaikan[0] ?? null);
            $buktiPerbaikanPath = $buktiPerbaikan ? storage_path('app/public/' . $buktiPerbaikan) : null;

            if ($buktiTemuanPath) {
                $addImageWithLabel($buktiTemuanPath, 'Finding Photo', 50, 420);
            }
            if ($buktiPerbaikanPath) {
                $addImageWithLabel($buktiPerbaikanPath, 'Corrective Action Photo', 370, 420);
            }
        }

        $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
        $fileName = 'laporan_lct_' . now()->format('Ymd') . '.pptx';
        $filePath = public_path($fileName);
        $writer->save($filePath);

        return response()->download($filePath);
    }
    
    public function exportToExcel()
    {
        $today = now();

        switch ($this->rangeType) {
            case 'weekly':
                $start = $today->copy()->subWeek();
                $end = $today;
                break;

            case 'monthly':
                $start = $today->copy()->subMonth();
                $end = $today;
                break;

            case 'semester':
                $start = $today->copy()->subMonths(6);
                $end = $today;
                break;

            case 'yearly':
                $start = $today->copy()->subYear();
                $end = $today;
                break;

            case 'custom':
                $start = $this->startDate ? Carbon::parse($this->startDate) : $today;
                $end = $this->endDate ? Carbon::parse($this->endDate) : $today;
                break;

            default: // daily
                $start = $today->copy()->subDay();
                $end = $today;
                break;
        }

    
        return Excel::download(new LaporanLctExport($start, $end), 'laporan_lct_' . now()->format('Ymd_His') . '.xlsx');
    } 
}

