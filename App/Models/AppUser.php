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

    // Relación con Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relación con Headquarter
    public function headquarter()
    {
        return $this->belongsTo(Headquarter::class, 'headquarter_id');
    }

    // Relación con Tasks
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'adviser_task', 'user_id', 'task_id')
                    ->withPivot('status');
    }

    // Métodos de Roles
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

    public function hasRole($roleName)
    {
        return $this->role->name === $roleName;
    }

    // (Opcional) Métodos para scopes si usas Passport
    public function token()
    {
        return $this->hasOne('Laravel\Passport\Token');
    }

    public function hasScope($scope)
    {
        return in_array($scope, $this->scopes());
    }

    public function scopes()
    {
        return $this->token()->scopes;
    }
}
