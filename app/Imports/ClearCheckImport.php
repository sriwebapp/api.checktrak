<?php

namespace App\Imports;

use App\Check;
use App\Import;
use App\Account;
use App\Company;
use App\History;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClearCheckImport implements ToCollection, WithHeadingRow
{
    protected $account;
    protected $company;
    protected $totalRows;
    protected $importedRows = 0;
    protected $failedRows = 0;
    protected $successChecks = [];
    protected $failedChecks = [];
    protected $import;
    protected $alreadyLogged = false;

    public function __construct(Company $company = null, Account $account = null)
    {
        $this->account = $account;
        $this->company = $company;
    }

    public function collection(Collection $rows)
    {
        $this->totalRows = $rows->count();

        $this->createImport();

        $rows->each( function($row) {
            // check if exiting check
            if(! $check = $this->getCheck($row)) {
                $this->handleError($row, __('message.not_existing.check'));
                return;
            }
            // check if already cleared
            if ($check->inStatus("cleared")) {
                $this->handleError($row, __('message.check.cleared'));
                return;
            }
            // check if claimed
            if(! $check->inStatus("claimed") ) {
                $this->handleError($row, __('message.check.not.claimed'));
                return;
            }

            try {
                // persist to database
                $this->clearCheck($row, $check);
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

    protected function getCheck(Collection $row)
    {
        return $this->account->checks()->where('number', trim($row['check_number']))->first();;
    }

    protected function createImport()
    {
        $this->import = Import::create([
            'company_id' => $this->company->id,
            'user_id' => auth()->user()->id,
            'subject' => 'ClearCheck',
            'total' => $this->totalRows,
        ]);
    }

    protected function clearCheck(Collection $row, Check $check)
    {
        // trigger error if date is in invalid format
        $date = Carbon::createFromFormat('m/d/Y', trim($row['date_cleared']))->format('Y-m-d');
        // update check
        $check->update([
            'status_id' => 6,
            'cleared' => trim($row['amount_cleared']),
        ]);

        History::create([
            'check_id' => $check->id,
            'action_id' => 7,
            'user_id' => auth()->user()->id,
            'date' => $date,
            'remarks' => 'Imported',
            'state' => json_encode($check->only(['group_id', 'branch_id', 'status_id', 'received', 'details', 'deleted_at']))
        ]);

        array_push($this->successChecks, $check->id);

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
        array_push($this->failedChecks, [
            'number' => trim($row['check_number']),
            'date' => trim($row['date_cleared']),
            'cleared' => trim($row['amount_cleared']),
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

        $this->import->checks = Check::whereIn('id', $this->successChecks)
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
