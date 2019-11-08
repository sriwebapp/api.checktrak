<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class TransmittalExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithColumnFormatting, ShouldAutoSize
{
    protected $transmittal;

    public function __construct($transmittal)
    {
        $this->transmittal = $transmittal;
    }

    public function headings(): array
    {
        return ['Date', 'Check #', 'Payee Name', 'Details', 'Amount', 'Date Claimed' ];
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

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
