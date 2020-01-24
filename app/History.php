<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'check_history';

    protected $guarded = [];

    public function check()
    {
        return $this->belongsTo(Check::class);
    }

    public function action()
    {
        return $this->belongsTo(Action::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
