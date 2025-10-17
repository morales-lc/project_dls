<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProgramTotalsSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(protected array $meta, protected array $rows) {}

    public function title(): string
    {
        return 'Program totals';
    }

    public function array(): array
    {
        $data = [];
        // Header
        $data[] = ['Program', 'MIDES', 'SIDLAK', 'Total'];
        // Rows
        foreach ($this->rows as $r) {
            $data[] = [$r['program'], $r['mides'], $r['sidlak'], $r['total']];
        }
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling: bold, fill
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEF2F7');

        // Borders for all cells in used range
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:D{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // Center align data cells (rows 2..N)
        if ($highestRow >= 2) {
            $sheet->getStyle("A2:D{$highestRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 45,
            'B' => 14,
            'C' => 14,
            'D' => 14,
        ];
    }
}
