<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function Branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function incharge()
    {
        return $this->belongsToMany(User::class, 'group_incharge', 'group_id');
    }

    public function checks()
    {
        return $this->hasMany(Check::class);
    }

    public function transmittals()
    {
        return $this->hasMany(Transmittal::class);
    }
}
