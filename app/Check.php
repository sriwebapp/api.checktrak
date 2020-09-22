<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Check extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function payee()
    {
        return $this->belongsTo(Payee::class);
    }

    public function history()
    {
        return $this->hasMany(History::class);
    }

    public function transmittals()
    {
        return $this->belongsToMany(Transmittal::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function checkBook()
    {
        return $this->belongsTo(CheckBook::class);
    }

    public function inStatus($stat)
    {
        $stats = [
            'Created',
            'Transmitted',
            'Claimed',
            'Returned',
            'Cancelled',
            'Cleared',
            'Staled'
        ];

        return strtolower($stats[$this->status_id - 1]) === strtolower($stat);
    }
}
