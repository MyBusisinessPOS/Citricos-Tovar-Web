<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PurchasesWeekly implements FromView, ShouldAutoSize, WithStyles, WithColumnFormatting, WithEvents
{
    use Exportable;
    public $purchases;
    public $totales;
    public $from;
    public $to;

    public function __construct($purchases, $totales, $from, $to)
    {
        $this->purchases = $purchases;
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
            'D' => NumberFormat::FORMAT_NUMBER_00,
            'E' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        $total = 0;
        $counter = 0;
        foreach ($this->purchases as $key => $value) {            
            $total = $total + collect($value)->count();
            $counter++;
        }
        $total = $total + 7;
        return [
            AfterSheet::class    => function(AfterSheet $event) use ($total){
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $event->sheet->getStyle("A1:J{$total}")->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ]
                ]);
            },
        ];
    }


    public function view(): View
    {
        return view('excels.purchases', [
            'purchases' => $this->purchases, 
            'totales' => $this->totales,
            'to' => $this->to,
            'from' => $this->from,
        ]);
    }
}
