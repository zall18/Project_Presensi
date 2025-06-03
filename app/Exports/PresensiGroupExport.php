<?php

namespace App\Exports;

use App\Models\Presensi;
use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PresensiGroupExport implements FromCollection, WithColumnFormatting, WithHeadings, WithTitle, WithStyles, WithEvents, WithColumnWidths
{
    protected $groupId;
    protected $groupName;

    public function __construct($groupId, $groupName)
    {
        $this->groupId = $groupId;
        $this->groupName = $groupName;
    }

    public function collection()
    {
        $group = Group::find($this->groupId);

        if (!$group) {
            return new Collection([]);
        }

        $this->groupName = $group->nama;

        $presensi = Presensi::with('participant', 'shift')
            ->whereHas('participant.groupParticipants.group', function ($query) use ($group) {
                $query->where('id_group', $group->id);
            })
            ->get();

        $shift = $presensi->pluck('shift.tanggal_mulai');
        $today = Carbon::today();
        $totalHari = $shift->map(function ($date) use ($today) {
            $tanggalMulai = Carbon::parse($date);
            return $tanggalMulai->diffInDays($today) + 1;
        });

            $dataPresensi = $presensi->map(function ($data, $index) use ($totalHari, $presensi) {
                $totalMasuk = $presensi->where('id_participant', $data->participant->id)->count();
                $totalTelat = $presensi->where('id_participant', $data->participant->id)->where('status_terlambat', true)->count();
                $totalTidakCO = $presensi->where('id_participant', $data->participant->id)->where('status_check_out', false)->count();

                return [
                    $data->participant->nama,
                    isset($totalHari[$index]) ? (int) $totalHari[$index] : 0,
                    max(0, (int) $totalMasuk),
                    max(0, (int) $totalTelat),
                    max(0, (int) ($totalHari[$index] ?? 0) - $totalMasuk),
                    max(0, (int) $totalTidakCO),
                ];
            })->values();

        return new Collection($dataPresensi);
    }

    public function headings(): array
    {
        return [
            ['Recap Presensi Grup ' . $this->groupName], // Judul
            [], // baris kosong setelah judul
            ['Nama Peserta', 'Total Hari', 'Total Masuk', 'Total Terlambat', 'Total Tidak Masuk', 'Total Tidak Check-Out']
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_NUMBER,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function title(): string
    {
        return 'Rekap Grup ' . $this->groupName;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]], // Judul
            3 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center']], // Header tabel
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 30,
            'C' => 30,
            'D' => 30,
            'E' => 30,
            'F' => 30,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Merge untuk judul
                $event->sheet->mergeCells('A1:F1');

                // Border semua data mulai dari baris ke-4 (data dimulai dari row 4)
                $highestRow = $event->sheet->getHighestRow();
                $event->sheet->getStyle("A3:F$highestRow")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                    ],
                ]);

                // Styling header background
                $event->sheet->getStyle("A3:F3")->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => [
                            'rgb' => 'D9E1F2',
                        ],
                    ],
                ]);
            },
        ];
    }
}
