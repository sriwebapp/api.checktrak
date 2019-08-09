<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'group_branch', 'group_id');
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'group_action', 'group_id');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'group_module', 'group_id');
    }
}
