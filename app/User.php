<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'branch_id', 'access_id', 'active', 'avatar',
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

    public function history()
    {
        return $this->hasMany(History::class);
    }

    public function createdTransmittals()
    {
        return $this->hasMany(Transmittal::class, 'user_id');
    }

    public function inchargeTransmittals()
    {
        return $this->hasMany(Transmittal::class, 'incharge');
    }

    public function returnedTransmittals()
    {
        return $this->hasMany(Transmittal::class, 'returnedBy_id');
    }

    public function inchargeGroups()
    {
        return $this->belongsToMany(Group::class, 'group_incharge', 'user_id');
    }

    public function imports()
    {
        return $this->hasMany(Import::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'user_group', 'user_id');
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'user_action', 'user_id');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'user_module', 'user_id');
    }

    public function reports()
    {
        return $this->belongsToMany(Report::class, 'user_report', 'user_id');
    }

    public function getGroups()
    {
        return $this->access->group ?
            $this->access->getGroups() :
            $this->groups;
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

    public function getReports()
    {
        return $this->access->report ?
            $this->access->getReports() :
            $this->reports;
    }

    public function accessibility()
    {
        $this->branch;
        $this->groupAccess = $this->getGroups()->pluck('id');
        $this->actionAccess = $this->getActions()->pluck('code');
        $this->moduleAccess = $this->getModules()->pluck('code');
        $this->reportAccess = $this->getReports()->pluck('code');

        return $this;
    }
}
