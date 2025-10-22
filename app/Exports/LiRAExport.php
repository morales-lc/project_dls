<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\LiRAMetadataSheet;
use App\Exports\Sheets\LiRARequestsSheet;

class LiRAExport implements WithMultipleSheets
{
    /**
     * @param array $meta        Metadata for the export (labels, filters, timeframe)
     * @param array $requests    Array of associative arrays representing LiRA rows
     */
    public function __construct(
        protected array $meta,
        protected array $requests,
    ) {}

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new LiRAMetadataSheet($this->meta);
        $sheets[] = new LiRARequestsSheet($this->meta, $this->requests);
        return $sheets;
    }
}
