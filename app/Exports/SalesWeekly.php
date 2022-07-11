<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SalesWeekly implements FromView, ShouldAutoSize, WithStyles, WithColumnFormatting
{

    use Exportable;
    public $sales;
    public $totales;
    public $from;
    public $to;

    public function __construct($sales, $totales, $from, $to)
    {
        $this->sales = $sales;
        $this->totales = $totales;
        $this->from = Carbon::parse($from)->format('d/m/Y');
        $this->to = Carbon::parse($to)->format('d/m/Y');
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 18]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER_00,
            'C' => NumberFormat::FORMAT_NUMBER_00,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER,
            'F' => NumberFormat::FORMAT_NUMBER,
            'G' => NumberFormat::FORMAT_NUMBER,
            'H' => NumberFormat::FORMAT_NUMBER,
            'I' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function view(): View
    {
        return view('excels.sales', [
            'sales' => $this->sales, 
            'totales' => $this->totales,
            'to' => $this->to,
            'from' => $this->from,
        ]);
    }   
}
