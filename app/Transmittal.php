<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transmittal extends Model
{
    protected $guarded = [];

    public function checks()
    {
        return $this->belongsToMany(Check::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
