<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MemStatementReportExport extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements FromView, WithCustomValueBinder, WithHeadings
{
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }


    public function headings(): array
    {
        return [
            [
                'Dairy Management System'
            ],
            [
                'Member Statement Report'
            ]
        ];
    }

    public function view(): View
    {
        if(isset($this->data['balSheet']['type'])){
        return view('report.memStatementReport3', $this->data);
        }
        return view('report.memStatementReport2', $this->data);
    }
    public function view3(): View3
    {
    }
}
