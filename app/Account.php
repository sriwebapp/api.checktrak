<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function checks()
    {
        return $this->hasMany(Check::class);
    }

    public function checkBooks()
    {
        return $this->hasMany(CheckBook::class);
    }

    public function availableCheckBooks()
    {
        return $this->hasMany(CheckBook::class)->where('available', '>', 0);
    }

    public function needReorder(): int
    {
        return $this->availableCheckBooks()->count() <= $this->reorder_point;
    }

    public function latestCheck()
    {
        return $this->checks()->latest()->first();
    }
}
