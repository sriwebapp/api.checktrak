<?php

namespace App\Imports;

use App\Check;
use App\Import;
use App\Company;
use App\History;
use App\TempCheck;
use Carbon\Carbon;
use App\FailureReason;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Concerns\ToModel;
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
            'subject' => 'Create',
            'total' => $this->totalRows,
        ]);

        $rows->each( function($row) {
            $payee = $this->company->payees->where('code', $row['bp_code'])->first();
            $account = $this->company->accounts->where('bank', $row['bank_no'])
                ->where('number', $row['account'])->first();


            if ($payee && $account) {
                $checkbook = $account->checkbooks()
                    ->where('start_series', '<=', $row['cheque_no'])
                    ->where('end_series', '>=', $row['cheque_no'])
                    ->exists();

                if($checkbook) {
                    $existing = $account->checks()->where('number', $row['cheque_no'])->first();

                    if(!$existing) {
                        try {
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
                            ]);

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
                        } catch (\InvalidArgumentException $e) {
                            $this->handle($row, 1);
                            Log::error('[' . auth()->user()->username . '] Importing Error:' . $e->getMessage());
                        } catch (QueryException $e) {
                            $this->handle($row, 1);
                            Log::error('[' . auth()->user()->username . '] Importing Error:' . $e->getMessage());
                        }
                    } elseif ($existing) {
                        $this->handle($row, 2);
                    }
                } else {
                    $this->handle($row, 9);
                }
            } elseif (!$payee) {
                $this->handle($row, 3);
            } elseif (!$account) {
                $this->handle($row, 4);
            }
        });

        $this->import->update(['success' => $this->importedRows, 'failed' => $this->failedRows]);
    }

    public function handle($row, $reason)
    {
        $reasons = FailureReason::get();

        array_push($this->failedChecks, [
            'bank' => trim($row['bank_no']),
            'account' => trim($row['account']),
            'number' => trim($row['cheque_no']),
            'payee_name' => trim($row['bp_name']),
            'payee_code' => trim($row['bp_code']),
            'amount' => trim($row['payment_amt']),
            'details' => trim($row['journal_remarks']),
            'date' => trim($row['posting_date']),
            'reason' => $reasons->find($reason)->desc,
        ]);
        // TempCheck::create([
        //     'import_id' => $this->import->id,
        //     'reason_id' => $reason,
        // ]);
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
