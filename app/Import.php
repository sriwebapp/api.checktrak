<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tempChecks()
    {
        return $this->hasMany(TempCheck::class);
    }

    public function checks()
    {
        return $this->hasMany(Check::class);
    }
}
