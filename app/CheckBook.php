<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckBook extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function Account()
    {
        return $this->belongsTo(Account::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function totalChecks()
    {
        return $this->end_series - ($this->start_series - 1);
    }

    public function postedChecks()
    {
        return $this->account->checks()
            ->whereBetween('number', [$this->start_series, $this->end_series])
            ->count();
    }

    public function checks()
    {
        $checks = [];

        for ($i=$this->start_series; $i <= $this->end_series; $i++) {
            $check = $this->account->checks()
                ->where('number', $i)
                ->with('payee')
                ->first();

            array_push($checks, ($check ? $check : [ 'number' => $i ]));
        }

        return $checks;

        return $this->account->checks()
            ->whereBetween('number', [$this->start_series, $this->end_series])
            ->get();
    }
}
