<?php

namespace App\Imports;

use App\Import;
use App\Account;
use App\History;
use Carbon\Carbon;
use App\FailureReason;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClearCheckImport implements ToCollection, WithHeadingRow
{
    protected $account;
    protected $totalRows;
    protected $importedRows = 0;
    protected $failedRows = 0;
    protected $failedChecks = [];
    protected $import;

    public function __construct(Account $account = null)
    {
        $this->account = $account;
    }

    public function collection(Collection $rows)
    {
        $this->totalRows = $rows->count();

        $this->import = Import::create([
            'company_id' => $this->account->company->id,
            'user_id' => auth()->user()->id,
            'subject' => 'Clear',
            'total' => $this->totalRows,
        ]);

        $rows->each( function($row) {
            $check = $this->account->checks()->where('number', trim($row['check_number']))->first();

            if($check) {
                if($check->status_id === 3) {
                    try {
                        $check->update([
                            'status_id' => 6,
                            'cleared' => $row['amount_cleared'],
                            'import_id' => $this->import->id
                        ]);

                        History::create([
                            'check_id' => $check->id,
                            'action_id' => 7,
                            'user_id' => auth()->user()->id,
                            'date' => Carbon::createFromFormat('m/d/Y', trim($row['date_cleared']))->format('Y-m-d'),
                            'remarks' => 'Imported',
                            'state' => json_encode($check->only(['group_id', 'branch_id', 'status_id', 'received', 'details', 'deleted_at']))
                        ]);

                        $this->importedRows++;
                    } catch (\InvalidArgumentException $e) {
                        $this->handle($row, 1);
                        Log::error('[' . auth()->user()->username . '] Importing Error:' . $e->getMessage());
                    } catch (QueryException $e) {
                        $this->handle($row, 1);
                        Log::error('[' . auth()->user()->username . '] Importing Error:' . $e->getMessage());
                    }
                } elseif ($check->status_id === 6) {
                    $this->handle($row, 6);
                } else {
                    $this->handle($row, 7);
                }
            } elseif (! $check) {
                $this->handle($row, 5);
            }
        });

        $this->import->update(['success' => $this->importedRows, 'failed' => $this->failedRows]);
    }

    public function handle($row, $reason)
    {
        $reasons = FailureReason::get();

        array_push($this->failedChecks, [
            'number' => trim($row['check_number']),
            'date' => trim($row['date_cleared']),
            'cleared' => trim($row['amount_cleared']),
            'reason' => $reasons->find($reason)->desc,
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
