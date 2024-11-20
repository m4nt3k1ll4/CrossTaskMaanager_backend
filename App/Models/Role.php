<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];
    
    public function appUsers()
    {
        return $this->hasMany(AppUser::class, 'role_id');
    }
}