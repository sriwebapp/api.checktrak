<?php

namespace App\Imports;

use App\Payee;
use App\Import;
use App\Company;
use App\PayeeGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
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
    protected $groups;
    protected $alreadyLogged = false;

    public function __construct(Company $company = null)
    {
        $this->company = $company;

        if($company)
            $this->groups = PayeeGroup::get();
    }

    public function collection(Collection $rows)
    {
        $this->totalRows = $rows->count();

        $this->createImport();

        $rows->each( function($row) {
            // check if existing group
            if(! $group = $this->getGroup($row) ) {
                $this->handleError($row, __('message.not_existing.group'));
                return;
            }
            // check if existing payee
            if($this->getPayee($row) ) {
                $this->handleError($row, __('message.data.existing'));
                return;
            }

            try {
                // persist to database
                $this->createPayee($row, $group);
            } catch (QueryException $e) {
                $this->handleError($row, __('message.data.invalid'));

                $this->logError($e->getMessage());
            }
        });

        $this->import->update(['success' => $this->importedRows, 'failed' => $this->failedRows]);
    }

    protected function getGroup(Collection $row)
    {
        return $this->groups->where('name', trim($row['group_code']))->first();
    }

    protected function getPayee(Collection $row)
    {
        return $this->company->payees()->where('code', trim($row['bp_code']))->first();
    }

    protected function createImport()
    {
        $this->import = Import::create([
            'company_id' => $this->company->id,
            'user_id' => auth()->user()->id,
            'subject' => 'CreatePayee',
            'total' => $this->totalRows,
        ]);
    }

    protected function createPayee(Collection $row, PayeeGroup $group)
    {
        $payee = Payee::create([
            'name' => trim($row['bp_name']),
            'code' => trim($row['bp_code']),
            'company_id' => $this->company->id,
            'payee_group_id' => $group->id,
        ]);

        array_push($this->successPayees, $payee->id);

        $this->importedRows++;
    }

    protected function logError($message)
    {
        if (! $this->alreadyLogged)
        {
            $this->alreadyLogged = true;

            Log::error('[' . auth()->user()->username . '] Importing Error:' . $message);
        }
    }

    protected function handleError(Collection $row, $reason)
    {
        array_push($this->failedPayees, [
            'name' => trim($row['bp_name']),
            'code' => trim($row['bp_code']),
            'group' => trim($row['group_code']),
            'reason' => $reason,
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
        $this->import->successPayees = Payee::whereIn('id', $this->successPayees)
                ->with('group')
                ->get();

        $response['import'] = $this->import;

        return $response;
    }
}
