<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'access_branch', 'access_id');
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'access_action', 'access_id');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'access_module', 'access_id');
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
