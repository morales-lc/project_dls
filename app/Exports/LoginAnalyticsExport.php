<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LoginAnalyticsExport implements WithMultipleSheets
{
    public function __construct(
        private string $timeLabel,
        private Carbon $start,
        private Carbon $end,
        private array $summaryRows,
        private array $studentRows,
        private array $facultyRows,
        private array $recentRows
    )
    {
    }

    public function sheets(): array
    {
        return [
            new LoginAnalyticsSheetExport(
                'Summary',
                [
                    ['Timeframe', $this->timeLabel],
                    ['Start date', $this->start->format('M d, Y')],
                    ['End date', $this->end->format('M d, Y')],
                    ['Generated at', now()->format('M d, Y h:i A')],
                    [],
                    ...$this->summaryRows,
                ],
                ['Metric', 'Value']
            ),
            new LoginAnalyticsSheetExport(
                'Students By Program',
                $this->studentRows,
                ['Program', 'Course', 'Unique Students', 'Login Events']
            ),
            new LoginAnalyticsSheetExport(
                'Faculty By Program',
                $this->facultyRows,
                ['Program', 'Unique Faculty', 'Login Events']
            ),
            new LoginAnalyticsSheetExport(
                'Recent Logs',
                $this->recentRows,
                ['Date and Time', 'User', 'Role', 'Program', 'Course', 'IP']
            ),
        ];
    }
}
