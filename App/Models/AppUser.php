<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class AppUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function headquarter()
    {
        return $this->belongsTo(Headquarter::class);
    }

    public function isManager()
    {
        return $this->role_id == 2;
    }

    public function isCeo()
    {
        return $this->role_id == 1;
    }

    public function isAdviser()
    {
        return $this->role_id == 3;
    }

    public function hasScope($scope)
    {
        return in_array($scope, $this->scopes());
    }

    public function scopes()
    {
        return [];
    }

    public function tasks(){
        return $this->belongsToMany(Task::class, 'adviser_task', 'user_id', 'task_id');
    }
    public function images(){
        return $this->hasMany(AdvicerTaskImage::class, 'user_id');
    }
}



