<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payee extends Model
{
    protected $guarded = [];

    public function group()
    {
        return $this->belongsTo(PayeeGroup::class, 'payee_group_id');
    }
}
