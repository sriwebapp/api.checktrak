<?php

namespace App\Imports;

use App\Check;
use App\Company;
use App\History;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CheckImport implements ToCollection, WithHeadingRow
{
    protected $company;
    protected $totalRows;
    protected $importedRows = 0;

    public function __construct(Company $company = null)
    {
        $this->company = $company;
    }

    public function collection(Collection $rows)
    {
        $this->totalRows = $rows->count();
        $accounts = $this->company->accounts;
        $payees = $this->company->payees;

        $rows->each( function($row) use ($accounts, $payees) {
            $payee = $payees->where('code', $row['bp_code'])->first();
            $account = $accounts->where('bank', $row['bank_no'])
                ->where('number', $row['account'])->first();


            if ($payee && $account) {
                $existing = $account->checks()->where('number', $row['cheque_no'])->first();

                if(!$existing) {
                    $check = Check::create([
                        'status_id' => 1,
                        'number' => $row['cheque_no'],
                        'company_id' => $this->company->id,
                        'account_id' => $account->id,
                        'payee_id' => $payee->id,
                        'group_id' => 1,
                        'branch_id' => 1,
                        'amount' => $row['payment_amt'],
                        'date' => Carbon::createFromFormat('m/d/Y', $row['posting_date'])->format('Y-m-d'),
                        'details' => $row['journal_remarks'],
                    ]);

                    History::create([
                        'check_id' => $check->id,
                        'action_id' => 1,
                        'user_id' => auth()->user()->id,
                        'date' => date('Y-m-d'),
                        'remarks' => 'Imported'
                    ]);

                    $this->importedRows++;
                }
            }
        });
    }

    public function response()
    {
        return $this->importedRows . ' out of ' . $this->totalRows . ' checks successfully imported.';
    }
}
