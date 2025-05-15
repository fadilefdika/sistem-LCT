<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LaporanLct;
use Livewire\WithPagination;
use App\Models\LctDepartement;
use Illuminate\Support\Carbon;
use App\Exports\LaporanLctExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Style\Alignment;

class ProgressPerbaikanTable extends Component
{
    use WithPagination;

    public $riskLevel = '';
    public $statusLct = '';
    public $tanggalAwal = null;
    public $tanggalAkhir = null;
    public $departemenId = '';
    public $areaId = '';
    public $search = '';
    public $rangeType = 'daily';

    private function filterData()
    {
        if (Auth::guard('ehs')->check()) {
            // Jika pengguna adalah EHS
            $user = Auth::guard('ehs')->user();
            $role = optional($user->roles->first())->name;  // Ambil role dari model EhsUser
        } else {
            // Jika pengguna adalah User biasa (guard 'web')
            $user = Auth::user();
            $role = optional($user->roleLct->first())->name;  // Ambil role dari model User
        }

        // === Status progress dan closed ===
        $progressStatuses = [
            'in_progress', 'progress_work', 'waiting_approval', 'approved', 'revision',
            'waiting_approval_temporary', 'approved_temporary', 'temporary_revision',
            'approved_taskbudget', 'waiting_approval_taskbudget', 'taskbudget_revision',
            'work_permanent', 'waiting_approval_permanent', 'approved_permanent', 'permanent_revision', 'closed'
        ];

        if ($role == 'user') {
            array_unshift($progressStatuses, 'review', 'open'); // Bisa juga pakai array_merge()
        }
        // === Base query untuk semua data (progress dan closed) ===
        $query = LaporanLct::query()
            ->select('*', DB::raw("
                CASE 
                    WHEN status_lct = 'closed' THEN 1
                    ELSE 0
                END as order_type,
                CASE 
                    WHEN status_lct = 'closed' AND CAST(catatan_ehs AS VARCHAR(MAX)) IS NOT NULL AND CAST(catatan_ehs AS VARCHAR(MAX)) != '' THEN 1
                    ELSE 0
                END as order_note
            "));



        // === Filter berdasarkan role ===
        if ($role === 'user') { 
            $query->where('user_id', $user->id);
        } elseif ($role === 'manajer') {
            $departemenId = \App\Models\LctDepartement::where('user_id', $user->id)->value('id');
            if ($departemenId) {
                $query->where('departemen_id', $departemenId);
            } else {
                $query->whereRaw('1=0');
            }
        } elseif (!in_array($role, ['ehs'])) {
            $picId = \App\Models\Pic::where('user_id', $user->id)->value('id');
            $query->where('pic_id', $picId);
        }

        // === Filter status sesuai dengan array $progressStatuses ===
        $query->whereIn('status_lct', $progressStatuses);

        // === Filter tambahan ===
        if ($this->riskLevel) {
            $query->where('tingkat_bahaya', $this->riskLevel);
        }

        if ($this->statusLct) {
            $statuses = explode(',', $this->statusLct);
            $query->whereIn('status_lct', $statuses);
        }

        if ($this->tanggalAwal && $this->tanggalAkhir) {
            $startDate = \Carbon\Carbon::parse($this->tanggalAwal)->startOfDay(); // 00:00:00
            $endDate = \Carbon\Carbon::parse($this->tanggalAkhir)->endOfDay(); // 23:59:59
        
            $query->whereBetween('tanggal_temuan', [$startDate, $endDate]);
        }        
        
        if ($this->departemenId) {
            $query->where('departemen_id', $this->departemenId);
        }
        
        if ($this->areaId) {
            $query->where('id', $this->areaId);
        }
        
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('judul', 'like', '%' . $this->search . '%')
                ->orWhere('lokasi', 'like', '%' . $this->search . '%');
            });
        }        

        // === Urutan: Progress (order_type 0) dulu, lalu Closed (order_type 1) ===
        return $query->orderBy('order_type')       // closed di akhir
             ->orderBy('order_note')       // catatan_ehs yang tidak kosong di paling akhir
             ->orderByDesc('updated_at')   // urut dari yang terbaru
             ->paginate(10);

    }

    public function updatedRiskLevel($value)
    {
        $this->resetPage(); 
    }

    public function applyFilter()
    {
        $this->resetPage(); // Reset halaman pagination ketika filter diterapkan
    }

    public function resetFilters()
    {
        $this->riskLevel = '';
        $this->statusLct = '';
        $this->tanggalAwal = null;
        $this->tanggalAkhir = null;
        $this->departemenId = '';
        $this->areaId = '';
        $this->search = '';
        $this->resetPage(); // Reset halaman pagination
    }

    public function render()
    {
        $departments = \App\Models\LctDepartement::pluck('id', 'nama_departemen');

        $area = \App\Models\AreaLct::pluck('id', 'nama_area');
        // Defining $statusGroups in the render method
        $statusGroups = [
            'In Progress' => ['in_progress', 'progress_work', 'waiting_approval'],
            'Approved' => ['approved', 'approved_temporary', 'approved_taskbudget'],
            'Closed' => ['closed'],
            // Tambahkan kelompok status lainnya sesuai kebutuhan
        ];

        return view('livewire.progress-perbaikan-table', [
            'laporans' => $this->filterData(),
            'departments' => $departments,
            'statusGroups' => $statusGroups, 
            'areas' => $area 
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
                $start = $this->tanggalAwal ? Carbon::parse($this->tanggalAwal) : $today;
                $end = $this->tanggalAkhir ? Carbon::parse($this->tanggalAkhir) : $today;
                break;

            default: // daily
                $start = $today->copy()->subDay();
                $end = $today;
                break;
        }

        return Excel::download(
            new LaporanLctExport($start, $end),
            'laporan_lct_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}


