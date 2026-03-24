<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StaffActivityLogsExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $rows)
    {
    }

    public function headings(): array
    {
        return [
            'When',
            'User',
            'Email',
            'Role',
            'Method',
            'Action',
            'Activity',
            'Subject Type',
            'Subject ID',
            'Status',
            'IP',
        ];
    }

    public function array(): array
    {
        return array_map(function ($row) {
            return [
                $row['When'] ?? null,
                $row['User'] ?? null,
                $row['Email'] ?? null,
                $row['Role'] ?? null,
                $row['Method'] ?? null,
                $row['Action'] ?? null,
                $row['Activity'] ?? null,
                $row['Subject Type'] ?? null,
                $row['Subject ID'] ?? null,
                $row['Status'] ?? null,
                $row['IP'] ?? null,
            ];
        }, $this->rows);
    }
}
