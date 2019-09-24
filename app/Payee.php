<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payee extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function group()
    {
        return $this->belongsTo(PayeeGroup::class, 'payee_group_id');
    }

    public function checks()
    {
        return $this->hasMany(Check::class);
    }
}
