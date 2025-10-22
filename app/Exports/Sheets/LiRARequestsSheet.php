<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LiRARequestsSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(protected array $meta, protected array $rows, protected string $sheetTitle = 'Requests') {}

    public function title(): string
    {
        return $this->sheetTitle ?: 'Requests';
    }

    public function array(): array
    {
        $data = [];
        // Header
        $data[] = [
            'Submitted At', 'First Name', 'Middle Name', 'Last Name', 'Email', 'Designation', 'Department',
            'Action', 'Assistance Types', 'Resource Types', 'Titles Of', 'For Borrow/Scan', 'For List', 'For Videos',
            'Status', 'Decision At', 'Decision Reason', 'Response Sent At'
        ];
        foreach ($this->rows as $r) {
            $data[] = [
                $r['created_at'] ?? '',
                $r['first_name'] ?? '',
                $r['middle_name'] ?? '',
                $r['last_name'] ?? '',
                $r['email'] ?? '',
                $r['designation'] ?? '',
                $r['department'] ?? '',
                $r['action'] ?? '',
                $r['assistance_types'] ?? '',
                $r['resource_types'] ?? '',
                $r['titles_of'] ?? '',
                $r['for_borrow_scan'] ?? '',
                $r['for_list'] ?? '',
                $r['for_videos'] ?? '',
                $r['status'] ?? '',
                $r['processed_at'] ?? '',
                $r['decision_reason'] ?? '',
                $r['response_sent_at'] ?? '',
            ];
        }
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = 'R'; // 18 columns -> R
        // Header styling
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EEF2F7');

        // Borders
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:{$lastCol}{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    // Align header center, data top-left; enable text wrap for data rows
        $sheet->getStyle("A1:{$lastCol}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        if ($highestRow >= 2) {
            $sheet->getStyle("A2:{$lastCol}{$highestRow}")
                ->getAlignment()
        ->setVertical(Alignment::VERTICAL_TOP)
        ->setWrapText(true);
        }
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 18,
            'C' => 18,
            'D' => 18,
            'E' => 28,
            'F' => 16,
            'G' => 18,
            'H' => 14,
            'I' => 26,
            'J' => 26,
            'K' => 28,
            'L' => 30,
            'M' => 28,
            'N' => 28,
            'O' => 12,
            'P' => 20,
            'Q' => 30,
            'R' => 22,
        ];
    }
}
