<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Headquarter extends Model
{
    protected $fillable = ['name', 'manager_id'];

    /**
     * Get the manager associated with the headquarter.
     */

    public function users()
    {
        return $this->hasMany(AppUser::class);
    }
    public function manager()
    {
        return $this->belongsTo(AppUser::class, 'manager_id');
    }
    public function advisers()
    {
        return $this->hasMany(AppUser::class, 'headquarter_id')->where('role_id', 3);
    }
    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
