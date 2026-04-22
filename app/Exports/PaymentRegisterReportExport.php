<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentRegisterReportExport extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements FromView, WithCustomValueBinder, WithHeadings
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
                'Payment Register Report'
            ]
        ];
    }

    public function view(): View
    {
        return view('report.paymentRegister', $this->data);
    }
}
