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

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function incharge()
    {
        return $this->belongsTo(User::class, 'incharge');
    }
}
