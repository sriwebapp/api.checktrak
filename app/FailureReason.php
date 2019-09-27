<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FailureReason extends Model
{
    public function tempChecks()
    {
        return $this->hasMany(TempCheck::class, 'reason_id');
    }
}
