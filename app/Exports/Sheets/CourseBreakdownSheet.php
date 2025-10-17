<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CourseBreakdownSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    private array $programSpans = [];

    public function __construct(protected array $meta, protected array $courseBreakdown) {}

    public function title(): string
    {
        return 'Course Breakdown';
    }

    public function array(): array
    {
        // Expecting $courseBreakdown as [ programName => [ [course, mides, sidlak, total], ... ] ]
        $data = [];
        // Header
        $data[] = ['Program', 'Course', 'MIDES', 'SIDLAK', 'Total'];

        $currentRow = 2; // data starts at row 2 (row 1 is header)
        foreach ($this->courseBreakdown as $program => $rows) {
            $rowCount = count($rows);
            if ($rowCount === 0) continue;
            // track span for merging Program column later in AfterSheet
            $this->programSpans[] = [$currentRow, $currentRow + $rowCount - 1];

            $first = true;
            foreach ($rows as $r) {
                $data[] = [
                    $first ? $program : '',
                    $r['course'] ?? ($r[0] ?? ''),
                    (int)($r['mides'] ?? ($r[1] ?? 0)),
                    (int)($r['sidlak'] ?? ($r[2] ?? 0)),
                    (int)($r['total'] ?? ($r[3] ?? 0)),
                ];
                $first = false;
                $currentRow++;
            }
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = 'E';
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEF2F7');

        // Borders for all cells in used range
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:{$lastCol}{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // Center align data cells
        if ($highestRow >= 2) {
            $sheet->getStyle("A2:{$lastCol}{$highestRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
        }
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Merge the Program column cells per group and vertically center
                $palette = [
                    ['E3F2FD','F0F7FF'], // blue
                    ['E8F5E9','F1FAF2'], // green
                    ['FFF3E0','FFF8EB'], // orange
                    ['F3E5F5','F8EFFA'], // purple
                    ['E0F2F1','EBF7F6'], // teal
                    ['FFFDE7','FFFEEE'], // yellow
                    ['FCE4EC','FFF0F5'], // pink
                ];
                $colorIndex = 0;
                foreach ($this->programSpans as [$start, $end]) {
                    if ($end > $start) {
                        $event->sheet->getDelegate()->mergeCells("A{$start}:A{$end}");
                    }
                    $event->sheet->getStyle("A{$start}:A{$end}")
                        ->getAlignment()
                        ->setVertical(Alignment::VERTICAL_CENTER)
                        ->setWrapText(true);

                    // Base background per program group
                    [$base, $alt] = $palette[$colorIndex % count($palette)];
                    $event->sheet->getStyle("A{$start}:E{$end}")
                        ->getFill()->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($base);

                    // Alternating row shading within the group
                    for ($r = $start; $r <= $end; $r++) {
                        if ((($r - $start) % 2) === 1) {
                            $event->sheet->getStyle("A{$r}:E{$r}")
                                ->getFill()->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB($alt);
                        }
                    }

                    // Add a thicker separator line after each group
                    $event->sheet->getStyle("A{$end}:E{$end}")
                        ->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

                    $colorIndex++;
                }

                // Wrap text for Program and Course columns across all used rows
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                $event->sheet->getStyle("A1:B{$highestRow}")->getAlignment()->setWrapText(true);
            }
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 40,
        ];
    }
}
