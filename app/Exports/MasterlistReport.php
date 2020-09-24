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

class MasterlistReport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithColumnFormatting, ShouldAutoSize, WithEvents
{
    protected $checks;
    protected $title;
    protected $headers;

    public function __construct($checks, $title, $headers)
    {
        $this->checks = $checks;
        $this->title = $title;
        $this->headers = $headers;
    }

    public function headings(): array
    {
        return $this->headers->map(function($item, $key) {
                    return [$key, $item];
                })
                ->push([])
                ->push([
                        'Bank',
                        'Bank Account',
                        'Check Date',
                        'Check Number',
                        'Payee Code',
                        'Payee Name',
                        'Details',
                        'Amount',
                        'Status',
                        'Transmittal To',
                        'Transmittal No.',
                        'Date Transmitted',
                        'Transmitted Received',
                        'Date Due For Return',
                        'Date Claimed',
                        'Date Returned',
                        'Returned Received',
                        'No. of Days Delayed',
                        'Date Cleared',
                        'Amount Cleared',
                        'Date Staled',
                        'Date Cancelled',
                        'Reason for Cancellation',
                ])->values()->toArray();
    }

    public function collection()
    {
        return $this->checks;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function map($check): array
    {
        $transmittal = $check->transmittals()->latest()->first();

        $history = $check->history()->latest()->get();

        $transmitted = $history->first(function($h) {
            return $h->action_id === 2 && $h->active === 1;
        });

        $transmittedReceived = $transmitted ? $history->last(function($h) use ($transmitted) {
            return $h->action_id === 3 && $h->active === 1 && $h->id > $transmitted->id;
        }) : null;

        $claimed = $history->first(function($h) {
            return $h->action_id === 4 && $h->active === 1;
        });

        $returned = $transmitted ? $history->first(function($h) use ($transmitted) {
            return $h->action_id === 5 && $h->active === 1 && $h->id > $transmitted->id;
        }) : null;

        $returnedReceived = $returned ? $history->last(function($h) use ($returned) {
            return $h->action_id === 3 && $h->active === 1 && $h->id > $returned->id;
        }) : null;

        $cleared = $history->first(function($h) {
            return $h->action_id === 7 && $h->active === 1;
        });

        $staled = $history->first(function($h) {
            return $h->action_id === 12 && $h->active === 1;
        });

        $cancelled = $history->first(function($h) {
            return $h->action_id === 6 && $h->active === 1;
        });

        return [
            $check->account->bank,
            $check->account->number,
            Date::dateTimeToExcel(new Carbon($check->date)),
            $check->number,
            $check->payee->code,
            $check->payee->name,
            $check->details,
            $check->amount,
            $check->status->name,
            $transmittal ? $transmittal->inchargeUser->name: '',
            $transmittal ? $transmittal->ref: '',
            $transmitted ? Date::dateTimeToExcel(new Carbon($transmitted->date)): '',
            $transmittedReceived ? Date::dateTimeToExcel(new Carbon($transmittedReceived->date)): '',
            $transmittal ? Date::dateTimeToExcel(new Carbon($transmittal->due)): '',
            $claimed ? Date::dateTimeToExcel(new Carbon($claimed->date)): '',
            $returned ? Date::dateTimeToExcel(new Carbon($returned->date)): '',
            $returnedReceived ? Date::dateTimeToExcel(new Carbon($returnedReceived->date)): '',
            '',
            $cleared ? Date::dateTimeToExcel(new Carbon($cleared->date)): '',
            $check->cleared,
            $staled ? Date::dateTimeToExcel(new Carbon($staled->date)): '',
            $cancelled ? Date::dateTimeToExcel(new Carbon($cancelled->date)): '',
            $cancelled ? $cancelled->remarks: '',
        ];
    }


    public function registerEvents(): array
    {
        $start = $this->headers->count() + 2;

        return [
            BeforeSheet::class    => function(BeforeSheet $event) use ($start) {
                $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getFont()->setName('Century Gothic')->setSize(10);

                $event->sheet->getDelegate()->getParent()->getActiveSheet()->freezePane('A' . ( $start + 1 ));
            },

            AfterSheet::class    => function(AfterSheet $event) use ($start) {
                $event->sheet->getDelegate()->getStyle('A' . $start . ':W' . $start)->getFont()->setBold('true');

                $event->sheet->getDelegate()->getStyle('A1:B' . $this->headers->count())->getFont()->setSize(10.5);

                $event->sheet->getDelegate()->getStyle('B1:B' . $this->headers->count())->getAlignment()->setHorizontal('right');
                $event->sheet->getDelegate()->getStyle('A' . $start . ':E'. ($this->checks->count() + $start))->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('I' . $start . ':I'. ($this->checks->count() + $start))->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('K' . $start . ':S'. ($this->checks->count() + $start))->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('U' . $start . ':V'. ($this->checks->count() + $start))->getAlignment()->setHorizontal('center');
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => 'mm/dd/yyyy',
            'E' => '@',
            'H' => '#,##0.00',
            'L' => 'mm/dd/yyyy',
            'M' => 'mm/dd/yyyy',
            'N' => 'mm/dd/yyyy',
            'O' => 'mm/dd/yyyy',
            'P' => 'mm/dd/yyyy',
            'Q' => 'mm/dd/yyyy',
            'R' => '0',
            'S' => 'mm/dd/yyyy',
            'T' => '#,##0.00',
            'U' => 'mm/dd/yyyy',
            'V' => 'mm/dd/yyyy',
        ];
    }
}
