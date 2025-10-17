<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MonthlyByProgramSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(protected array $meta, protected array $monthlyByProgram) {}

    public function title(): string
    {
        return 'Monthly by Program';
    }

    public function array(): array
    {
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $data = [];
        // Header
        $data[] = array_merge(['Program'], $months, ['Total']);
        // Rows
        foreach ($this->monthlyByProgram as $program => $vals) {
            $row = [$program];
            $sum = 0;
            for ($m = 1; $m <= 12; $m++) { $v = (int)($vals[$m] ?? 0); $row[] = $v; $sum += $v; }
            $row[] = $sum;
            $data[] = $row;
        }
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = 'N'; // Program + 12 months + Total = 14 columns -> N
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEF2F7');

        // Borders for all cells in used range
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:{$lastCol}{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // Center align data cells (rows 2..N)
        if ($highestRow >= 2) {
            $sheet->getStyle("A2:{$lastCol}{$highestRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 45,
        ];
    }
}
