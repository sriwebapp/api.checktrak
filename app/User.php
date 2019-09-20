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
        'name', 'username', 'email', 'password', 'branch_id', 'access_id', 'active'
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

    public function access()
    {
        return $this->belongsTo(Access::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
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
        return $this->access->branch ?
            $this->access->getBranches() :
            $this->branches;
    }

    public function getActions()
    {
        return $this->access->action ?
            $this->access->getActions() :
            $this->actions;
    }

    public function getModules()
    {
        return $this->access->module ?
            $this->access->getModules() :
            $this->modules;
    }

    public function accessibility()
    {
        $this->branch;
        $this->branchAccess = $this->getBranches()->pluck('code');
        $this->actionAccess = $this->getActions()->pluck('code');
        $this->moduleAccess = $this->getModules()->pluck('code');

        return $this;
    }
}
