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

class SaleUtilities implements FromView, ShouldAutoSize, WithStyles, WithColumnFormatting
{

    use Exportable;
    public $totales;
    public $from;
    public $to;

    public function __construct($totales, $from, $to)
    {
        $this->totales = $totales;
        $this->from = Carbon::parse($from)->format('d/m/Y');
        $this->to = Carbon::parse($to)->format('d/m/Y');
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 20]],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'B' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'C' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            
        ];
    }

    public function view(): View
    {
        return view('excels.utilities', [
            'totales' => $this->totales,
            'to' => $this->to,
            'from' => $this->from,
        ]);
    }   
}
