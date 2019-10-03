<?php

namespace App\Imports;

use App\Payee;
use App\Import;
use App\Company;
use App\PayeeGroup;
use App\FailureReason;
use Illuminate\Support\Collection;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PayeeImport implements ToCollection, WithHeadingRow
{
    protected $company;
    protected $totalRows;
    protected $importedRows = 0;
    protected $failedRows = 0;
    protected $failedPayees = [];
    protected $successPayees = [];
    protected $import;

    public function __construct(Company $company = null)
    {
        $this->company = $company;
    }

    public function collection(Collection $rows)
    {
        $this->totalRows = $rows->count();

        $this->import = Import::create([
            'company_id' => $this->company->id,
            'user_id' => auth()->user()->id,
            'subject' => 'CreatePayee',
            'total' => $this->totalRows,
        ]);

        $groups = PayeeGroup::get();

        $rows->each( function($row) use ($groups) {
            $group = $groups->where('name', $row['group_code'])->first();

            if($group) {
                $existing = $this->company->payees()->where('code', $row['bp_code'])->first();

                if(!$existing) {
                    try {
                        $payee = Payee::create([
                            'name' => trim($row['bp_name']),
                            'code' => trim($row['bp_code']),
                            'company_id' => $this->company->id,
                            'payee_group_id' => $group->id,
                        ]);

                        $payee->group;
                        array_push($this->successPayees, $payee);

                        $this->importedRows++;
                    } catch (QueryException $e) {
                        $this->handle($row, 1);
                        Log::error('[' . auth()->user()->username . '] Importing Error:' . $e);
                    }
                } elseif ($existing) {
                    $this->handle($row, 2);
                }
            } elseif (!$group) {
                $this->handle($row, 3);
            }
        });

        $this->import->update(['success' => $this->importedRows, 'failed' => $this->failedRows]);
    }

    public function handle($row, $reason)
    {
        $reasons = FailureReason::get();

        array_push($this->failedPayees, [
            'name' => trim($row['bp_name']),
            'code' => trim($row['bp_code']),
            'group' => trim($row['group_code']),
            'reason' => $reasons->find($reason)->desc,
        ]);

        $this->failedRows++;
    }

    public function response()
    {
        $response = [];

        if ($this->importedRows) {
            $response['successMessage'] = $this->importedRows . ' out of ' . $this->totalRows . ' payees successfully imported.';
        }

        if ($this->failedRows) {
            $response['failedMessage'] = $this->failedRows . ' out of ' . $this->totalRows . ' payees failed importing.';
        }

        $this->import->failedPayees = $this->failedPayees;
        $this->import->successPayees = $this->successPayees;

        $response['import'] = $this->import;

        return $response;
    }
}
