<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $guarded = [];

    public function checks()
    {
        return $this->hasMany(Check::class);
    }
}
