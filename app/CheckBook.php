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
        return $this->hasMany(Check::class);
    }

    public function postedChecksWithPayee()
    {
        return $this->hasMany(Check::class)->with('payee');
    }

    public function checks()
    {
        $postedChecks = $this->postedChecksWithPayee;

        $checks = [];

        for ($i=$this->start_series; $i <= $this->end_series; $i++) {
            $check = $postedChecks
                ->where('number', $i)
                ->first();


            array_push($checks, ($check ? $check : [ 'number' => substr('00000000' . $i, strlen($this->start_series) * -1) ]));
        }

        return collect($checks);
    }
}
