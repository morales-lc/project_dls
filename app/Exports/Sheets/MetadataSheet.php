<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MetadataSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(protected array $meta) {}

    public function title(): string
    {
        return 'Metadata';
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['Analytics Export'];
        $rows[] = ['Timeframe', $this->meta['label'] ?? ''];
        $rows[] = ['Mode', ucfirst($this->meta['mode'] ?? '')];
        if (!empty($this->meta['year'])) $rows[] = ['Year', $this->meta['year']];
        $rows[] = ['Start date', $this->meta['start'] ?? ''];
        $rows[] = ['End date', $this->meta['end'] ?? ''];
        if (!empty($this->meta['documents'])) $rows[] = ['Included documents', $this->meta['documents']];
        if (!empty($this->meta['program_count'])) $rows[] = ['Programs counted', $this->meta['program_count']];
        $rows[] = ['Generated at', $this->meta['generated_at'] ?? ''];
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
    // Header row (first row) bold + fill
    $sheet->getStyle('A1:B1')->getFont()->setBold(true);
    $sheet->getStyle('A1:B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEF2F7');

    // Apply borders to all used cells and align right
    $highestRow = $sheet->getHighestRow();
    $highestCol = $sheet->getHighestColumn();
    $range = "A1:" . $highestCol . $highestRow;
    $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 60,
        ];
    }
}
