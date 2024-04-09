<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SearchResultsExport implements FromCollection, WithMapping, ShouldAutoSize
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function map($row): array
    {
        return [
            $row['title'],
            $row['link'],
            $row['snippet'] ?? ''
        ];
    }
}
