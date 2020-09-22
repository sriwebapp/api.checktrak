<?php

namespace App\Imports;

use App\Check;
use App\Payee;
use App\Import;
use App\Account;
use App\Company;
use App\History;
use App\CheckBook;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CheckImport implements ToCollection, WithHeadingRow
{
    protected $company;
    protected $totalRows;
    protected $importedRows = 0;
    protected $failedRows = 0;
    protected $failedChecks = [];
    protected $import;
    protected $accounts;
    protected $alreadyLogged = false;

    public function __construct(Company $company = null)
    {
        $this->company = $company;

        if ($company)
            $this->accounts = $company->accounts;
    }

    public function collection(Collection $rows)
    {
        $this->totalRows = $rows->count();

        $this->createImport();

        $rows->each( function($row) {
            // check if existing account
            if (! $account = $this->getAccount($row) ) {
                $this->handleError($row, __('message.not_existing.account'));
                return;
            }
            // check if existing payee
            if (! $payee = $this->getPayee($row) ) {
                $this->handleError($row, __('message.not_existing.payee'));
                return;
            }
            // check if existing checkbook
            if (! $checkbook = $this->getCheckBooks($row, $account) ) {
                $this->handleError($row, __('message.not_existing.check_book'));
                return;
            }
            // check if existing check
            if ($this->getCheck($row, $account)) {
                $this->handleError($row, __('message.data.existing'));
                return;
            }

            try {
                // persist to database
                $this->createCheck($row, $account, $payee, $checkbook);
            } catch (\InvalidArgumentException $e) {
                $this->handleError($row, __('message.data.invalid'));

                $this->logError($e->getMessage());
            } catch (QueryException $e) {
                $this->handleError($row, __('message.data.invalid'));

                $this->logError($e->getMessage());
            }
        });

        $this->import->update(['success' => $this->importedRows, 'failed' => $this->failedRows]);
    }

    protected function getAccount(Collection $row)
    {
        return $this->accounts->where('bank', trim($row['bank_no']))
            ->where('number', trim($row['account']))->first();
    }

    protected function getPayee(Collection $row)
    {
        return $this->company->payees()->where('code', trim($row['bp_code']))->first();
    }

    protected function getCheckBooks(Collection $row, Account $account)
    {
        return $account->availableCheckbooks()
            ->where('start_series', '<=', trim($row['cheque_no']))
            ->where('end_series', '>=', trim($row['cheque_no']))
            ->whereRaw('length(start_series) = ' . strlen(trim($row['cheque_no'])) )
            ->first();
    }

    protected function getCheck(Collection $row, Account $account)
    {
        return $account->checks()->where('number', trim($row['cheque_no']))->first();
    }

    protected function createImport()
    {
        $this->import = Import::create([
            'company_id' => $this->company->id,
            'user_id' => auth()->user()->id,
            'subject' => 'CreateCheck',
            'total' => $this->totalRows,
        ]);
    }

    protected function createCheck(Collection $row, Account $account, Payee $payee, CheckBook $checkbook)
    {
        $check = Check::create([
            'number' => trim($row['cheque_no']),
            'company_id' => $this->company->id,
            'account_id' => $account->id,
            'payee_id' => $payee->id,
            'import_id' => $this->import->id,
            'amount' => trim($row['payment_amt']),
            'date' => Carbon::createFromFormat('m/d/Y', trim($row['posting_date']))->format('Y-m-d'),
            'details' => trim($row['journal_remarks']),
            'status_id' => 1, // created
            'received' => 1, // received
            'branch_id' => 1, // head office
            'group_id' => 1, // disbursement
            'check_book_id' => $checkbook->id
        ]);
        // update checkbook
        $checkbook->update([
            'posted' => $checkbook->posted + 1,
            'available' => $checkbook->available - 1,
        ]);
        // set date today if post dated check
        $date = new Carbon($check->date) > new Carbon(date('Y-m-d')) ? date('Y-m-d') : $check->date;

        History::create([
            'check_id' => $check->id,
            'action_id' => 1,
            'user_id' => auth()->user()->id,
            'date' => $date,
            'remarks' => 'Imported',
            'state' => json_encode($check->only(['group_id', 'branch_id', 'status_id', 'received', 'details', 'deleted_at']))
        ]);

        $this->importedRows++;
    }

    protected function logError($message)
    {
        if (! $this->alreadyLogged)
        {
            // $this->alreadyLogged = true;

            Log::error('[' . auth()->user()->username . '] Importing Error:' . $message);
        }
    }

    protected function handleError(Collection $row, $reason)
    {
        array_push($this->failedChecks, [
            'bank' => trim($row['bank_no']),
            'account' => trim($row['account']),
            'number' => trim($row['cheque_no']),
            'payee_name' => trim($row['bp_name']),
            'payee_code' => trim($row['bp_code']),
            'amount' => trim($row['payment_amt']),
            'details' => trim($row['journal_remarks']),
            'date' => trim($row['posting_date']),
            'reason' => $reason,
        ]);

        $this->failedRows++;
    }

    public function response()
    {
        $response = [];

        if ($this->importedRows) {
            $response['successMessage'] = $this->importedRows . ' out of ' . $this->totalRows . ' checks successfully imported.';
        }

        if ($this->failedRows) {
            $response['failedMessage'] = $this->failedRows . ' out of ' . $this->totalRows . ' checks failed importing.';
        }

        $this->import->checks = $this->import->checks()
            ->with('status')
            ->with('payee')
            ->with('account')
            ->with('group')
            ->with('branch')
            ->with('history')
            ->get();

        $this->import->failedChecks = $this->failedChecks;

        $response['import'] = $this->import;

        return $response;
    }
}
