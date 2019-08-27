<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
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

    public function branch()
    {
        if ( $this->transmittals->count() ) {
            return $this->transmittals->reverse()->first()->branch;
        } else {
            return Branch::first();
        }
    }
}
