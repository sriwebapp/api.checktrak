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

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'access_group', 'access_id');
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'access_action', 'access_id');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'access_module', 'access_id');
    }

    public function getGroups()
    {
        if ( $this->group === 2 ) {
            return Group::get();
        } elseif ( $this->group === 1 ) {
            return $this->groups;
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
