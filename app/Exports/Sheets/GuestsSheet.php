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
use App\Models\User;

class GuestsSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection()
    {
        return User::where('role', 'guest')
            ->get()
            ->map(function ($guest) {
                return [
                    'name' => $guest->name,
                    'email' => $guest->email,
                    'username' => $guest->username,
                    'contact_number' => $guest->contact_number,
                    'address' => $guest->address,
                ];
            });
    }

    public function title(): string
    {
        return 'Guests';
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Username',
            'Contact Number',
            'Address',
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
            'A' => 30,
            'B' => 30,
            'C' => 20,
            'D' => 20,
            'E' => 40,
        ];
    }
}
