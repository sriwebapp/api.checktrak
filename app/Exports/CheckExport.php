<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class CheckExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithColumnFormatting, ShouldAutoSize
{
    protected $checks;
    protected $title;

    public function __construct($checks, $title)
    {
        $this->checks = $checks;
        $this->title = $title;
    }

    public function headings(): array
    {
        return [
            'Bank',
            'Bank Account',
            'Check Date',
            'Check Number',
            'Payee Name',
            'Details',
            'Amount',
            'Status',
            'Transmittal Number',
            'Date Transmitted',
            'Date Due For Return',
            'Date Claimed',
            'Date Returned',
            'Date Cleared',
            'Date Staled',
        ];
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

        $claimed = $history->first(function($h) {
            return $h->action_id === 4 && $h->active === 1;
        });

        $returned = $history->first(function($h) {
            return $h->action_id === 5 && $h->active === 1;
        });

        $cleared = $history->first(function($h) {
            return $h->action_id === 7 && $h->active === 1;
        });

        $staled = $history->first(function($h) {
            return $h->action_id === 12 && $h->active === 1;
        });

        return [
            $check->account->bank,
            $check->account->number,
            Date::dateTimeToExcel(new Carbon($check->date)),
            $check->number,
            $check->payee->name,
            $check->details,
            $check->amount,
            $check->status->name,
            $transmittal ? $transmittal->ref: '',
            $transmittal ? Date::dateTimeToExcel(new Carbon($transmittal->date)): '',
            $transmittal ? Date::dateTimeToExcel(new Carbon($transmittal->due)): '',
            $claimed ? Date::dateTimeToExcel(new Carbon($claimed->date)): '',
            $returned ? Date::dateTimeToExcel(new Carbon($returned->date)): '',
            $cleared ? Date::dateTimeToExcel(new Carbon($cleared->date)): '',
            $staled ? Date::dateTimeToExcel(new Carbon($staled->date)): '',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => 'mm/dd/yyyy',
            'G' => '#,##0.00',
            'J' => 'mm/dd/yyyy',
            'K' => 'mm/dd/yyyy',
            'L' => 'mm/dd/yyyy',
            'M' => 'mm/dd/yyyy',
            'N' => 'mm/dd/yyyy',
            'O' => 'mm/dd/yyyy',
        ];
    }
}
