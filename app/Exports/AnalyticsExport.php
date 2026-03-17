<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\ProgramTotalsSheet;
use App\Exports\Sheets\MonthlyByProgramSheet;
use App\Exports\Sheets\CourseBreakdownSheet;
use App\Exports\Sheets\MetadataSheet;

class AnalyticsExport implements WithMultipleSheets
{
    public function __construct(
        protected array $meta,
        protected array $programTotals,
        protected ?array $monthlyByProgram, // null when not year mode
        protected array $courseBreakdown
    ) {}

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new MetadataSheet($this->meta);
        $sheets[] = new ProgramTotalsSheet($this->meta, $this->programTotals);
        if (!empty($this->monthlyByProgram)) {
            $sheets[] = new MonthlyByProgramSheet($this->meta, $this->monthlyByProgram);
        }
        $sheets[] = new CourseBreakdownSheet($this->meta, $this->courseBreakdown);
        return $sheets;
    }
}
