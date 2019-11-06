<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'code';
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function payees()
    {
        return $this->hasMany(Payee::class);
    }

    public function transmittals()
    {
        return $this->hasMany(Transmittal::class);
    }

    public function checks()
    {
        return $this->hasMany(Check::class);
    }

    public function checkBooks()
    {
        return $this->hasMany(CheckBook::class);
    }

    public function imports()
    {
        return $this->hasMany(Import::class);
    }
}
