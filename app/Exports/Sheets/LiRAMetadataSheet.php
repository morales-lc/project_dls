<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LiRAMetadataSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(protected array $meta) {}

    public function title(): string
    {
        return 'Metadata';
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['LiRA Management Export'];
        if (!empty($this->meta['status_label'])) $rows[] = ['Status', $this->meta['status_label']];
        if (!empty($this->meta['email'])) $rows[] = ['Email filter', $this->meta['email']];
        if (!empty($this->meta['timeframe_label'])) $rows[] = ['Timeframe', $this->meta['timeframe_label']];
        if (!empty($this->meta['start'])) $rows[] = ['Start date', $this->meta['start']];
        if (!empty($this->meta['end'])) $rows[] = ['End date', $this->meta['end']];
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
            'A' => 25,
            'B' => 60,
        ];
    }
}
