<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class TransmittalExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithColumnFormatting, ShouldAutoSize, WithEvents
{
    protected $transmittal;

    public function __construct($transmittal)
    {
        $this->transmittal = $transmittal;
    }

    public function headings(): array
    {
        $transmittal = $this->transmittal;
        return [
                    ['Reference', $transmittal->ref, '', '', 'Return due', $this->formatDate($transmittal->due)],
                    ['Transmitted to', $transmittal->inchargeUser->name, '', '', 'Prepared by', $transmittal->user->name],
                    ['Date Transmitted', $this->formatDate($transmittal->date), '', '', 'No. of Checks', $transmittal->checks->count()],
                    ['Date Returned', $this->formatDate($transmittal->returned), '', '', 'Total Amount', $transmittal->checks->sum('amount')],
                    [],
                    ['Date', 'Check #', 'Payee Name', 'Details', 'Amount', 'Date Claimed' ]
                ];
    }

    public function collection()
    {
        return $this->transmittal->checks;
    }

    public function title(): string
    {
        return $this->transmittal->ref;
    }

    public function map($check): array
    {
        $claimed = $check->history->first( function($h) {
            return $h->action_id === 4 && $h->active === 1;
        });

        return [
            Date::dateTimeToExcel(new Carbon($check->date)),
            $check->number,
            $check->payee->name,
            $check->details,
            $check->amount,
            $claimed ? Date::dateTimeToExcel(new Carbon($claimed->date)) : '',
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class    => function(BeforeSheet $event) {
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getFont()->setName('Century Gothic')->setSize(10);

                $event->sheet->getDelegate()->getParent()->getActiveSheet()->freezePane('A7');
            },

            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A6:W6')->getFont()->setBold('true');

                $event->sheet->getDelegate()->getStyle('A1:F4')->getFont()->setSize(10.5);

                $event->sheet->getDelegate()->getStyle('B3:B4')->getNumberFormat()->setFormatCode('mmmm dd, yyyy');
                $event->sheet->getDelegate()->getStyle('F1')->getNumberFormat()->setFormatCode('mmmm dd, yyyy');
                $event->sheet->getDelegate()->getStyle('F3')->getNumberFormat()->setFormatCode('0');
                $event->sheet->getDelegate()->getStyle('F4')->getNumberFormat()->setFormatCode('₱#,##0.00');
                $event->sheet->getDelegate()->getStyle('B1:B4')->getAlignment()->setHorizontal('right');
                $event->sheet->getDelegate()->getStyle('F1:F4')->getAlignment()->setHorizontal('right');
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => 'mm/dd/yyyy',
            'E' => '#,##0.00',
            'F' => 'mm/dd/yyyy',
        ];
    }

    protected function formatDate($date)
    {
        if(!$date)
            return '';

        return Date::dateTimeToExcel(new Carbon($date));
    }
}
