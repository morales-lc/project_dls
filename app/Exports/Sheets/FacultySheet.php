<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use App\Models\StudentFaculty;

class FacultySheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return StudentFaculty::with(['user', 'program'])
            ->where('role', 'faculty')
            ->get()
            ->map(function ($faculty) {
                return [
                    'school_id' => $faculty->school_id,
                    'first_name' => $faculty->first_name,
                    'last_name' => $faculty->last_name,
                    'email' => $faculty->user->email ?? '',
                    'username' => $faculty->user->username ?? '',
                    'program' => $faculty->program->name ?? '',
                    'birthdate' => $faculty->birthdate,
                ];
            });
    }

    public function title(): string
    {
        return 'Faculty';
    }

    public function headings(): array
    {
        return [
            'School ID',
            'First Name',
            'Last Name',
            'Email',
            'Username',
            'Program',
            'Birthdate',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E91E63']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 20,
            'D' => 30,
            'E' => 20,
            'F' => 30,
            'G' => 15,
        ];
    }
}
