<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempCheck extends Model
{
    protected $guarded = [];

    public function reason()
    {
        return $this->belongsTo(FailureReason::class, 'reason_id');
    }
}
