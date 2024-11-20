<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Headquarter extends Model
{
    protected $fillable = ['name', 'manager_id'];

    /**
     * Get the manager associated with the headquarter.
     */
    public function manager()
    {
        return $this->belongsTo(AppUser::class, 'manager_id');
    }

    /**
     * Get the advisers associated with the headquarter.
     */
    public function advisers()
    {
        return $this->belongsToMany(AppUser::class, 'adviser_headquarters', 'headquarter_id', 'user_id');
    }
}


