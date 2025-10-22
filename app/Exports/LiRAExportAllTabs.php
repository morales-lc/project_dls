<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\LiRAMetadataSheet;
use App\Exports\Sheets\LiRARequestsSheet;

class LiRAExportAllTabs implements WithMultipleSheets
{
    /**
     * @param array $meta           Metadata for the export
     * @param array $tabbedRequests Associative array: sheetTitle => rows[]
     */
    public function __construct(
        protected array $meta,
        protected array $tabbedRequests,
    ) {}

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new LiRAMetadataSheet($this->meta);
        foreach ($this->tabbedRequests as $title => $rows) {
            $sheets[] = new LiRARequestsSheet($this->meta, $rows, $title);
        }
        return $sheets;
    }
}
