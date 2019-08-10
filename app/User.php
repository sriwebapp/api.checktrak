<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'group_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'user_branch', 'user_id');
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'user_action', 'user_id');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'user_module', 'user_id');
    }

    public function getBranches()
    {
        return $this->group->branch ?
            $this->group->getBranches() :
            $this->branches;
    }

    public function getActions()
    {
        return $this->group->action ?
            $this->group->getActions() :
            $this->actions;
    }

    public function getModules()
    {
        return $this->group->module ?
            $this->group->getModules() :
            $this->modules;
    }

    public function access()
    {
        $this->branch = $this->getBranches()->pluck('code');
        $this->action = $this->getActions()->pluck('code');
        $this->module = $this->getModules()->pluck('code');

        return $this;
    }
}
