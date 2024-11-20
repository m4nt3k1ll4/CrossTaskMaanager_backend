<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority',
        'due_date',
        'headquarter_id',
    ];


    public function headquarter()
    {
        return $this->belongsTo(Headquarter::class);
    }

    public function images()
    {
        return $this->hasMany(AdvicerTaskImage::class, 'task_id');
    }

    public function users()
    {
        return $this->belongsToMany(AppUser::class,'adviser_task','task_id', 'user_id');
    }


    /**
     * Scope a query to only include tasks of a given headquarter.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $headquarterId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfHeadquarter($query, $headquarterId)
    {
        return $query->where('headquarter_id', $headquarterId);
    }

    /**
     * Scope a query to only include tasks of a given user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}


