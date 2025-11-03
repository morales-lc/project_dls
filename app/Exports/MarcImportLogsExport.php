<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MarcImportLogsExport implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(protected $logs) {}

    public function title(): string
    {
        return 'MARC Import Logs';
    }

    public function array(): array
    {
        $data = [];
        // Header
        $data[] = [
            'Date & Time',
            'Imported By',
            'Email',
            'Filename',
            'Total Parsed',
            'New Records Added',
            'Existing Records Updated',
            'Records Unchanged',
            'Records Deleted',
            'Deletion Enabled',
            'Errors Encountered',
        ];

        foreach ($this->logs as $log) {
            $data[] = [
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user->name ?? 'Unknown',
                $log->user->email ?? '',
                $log->filename,
                $log->total_parsed,
                $log->records_added,
                $log->records_updated,
                $log->records_unchanged,
                $log->records_deleted,
                $log->deletion_enabled ? 'Yes' : 'No',
                $log->records_errors,
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = 'K'; // 11 columns -> K
        
        // Header styling
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('D81B60'); // Pink header
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->getColor()->setRGB('FFFFFF'); // White text

        // Borders
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:{$lastCol}{$highestRow}")
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Align header center, data left/top
        $sheet->getStyle("A1:{$lastCol}1")
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        
        if ($highestRow >= 2) {
            $sheet->getStyle("A2:{$lastCol}{$highestRow}")
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_TOP);
            
            // Center align numbers
            $sheet->getStyle("E2:K{$highestRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Date & Time
            'B' => 25, // Imported By
            'C' => 30, // Email
            'D' => 40, // Filename
            'E' => 15, // Total Parsed
            'F' => 20, // New Records Added
            'G' => 25, // Existing Records Updated
            'H' => 20, // Records Unchanged
            'I' => 18, // Records Deleted
            'J' => 18, // Deletion Enabled
            'K' => 20, // Errors Encountered
        ];
    }
}
