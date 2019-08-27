<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $guarded = [];

    public function incharge()
    {
        return $this->belongsTo(User::class, 'incharge_id');
    }

    public function transmittals()
    {
        return $this->hasMany(Transmittal::class);
    }
}
