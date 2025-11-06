<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\StudentsSheet;
use App\Exports\Sheets\FacultySheet;
use App\Exports\Sheets\AdminsSheet;
use App\Exports\Sheets\LibrariansSheet;
use App\Exports\Sheets\GuestsSheet;

class UserManagementExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new StudentsSheet(),
            new FacultySheet(),
            new AdminsSheet(),
            new LibrariansSheet(),
            new GuestsSheet(),
        ];
    }
}
