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

    public function getBranches()
    {
        if ( $this->branch === 2 ) {
            return Branch::get();
        } elseif ( $this->branch === 1 ) {
            return $this->branches;
        }
    }

    public function getActions()
    {
        if ( $this->action === 2 ) {
            return Action::get();
        } elseif ( $this->action === 1 ) {
            return $this->actions;
        }
    }

    public function getModules()
    {
        if ( $this->module === 2 ) {
            return Module::get();
        } elseif ( $this->module === 1 ) {
            return $this->modules;
        }
    }
}
