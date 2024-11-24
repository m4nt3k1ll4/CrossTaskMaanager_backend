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
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }


    public function images()
    {
        return $this->hasMany(AdvicerTaskImage::class, 'task_id');
    }


    public function users()
    {
        return $this->belongsToMany(AppUser::class, 'adviser_task', 'task_id', 'user_id');
    }


    public function scopeOfUser($query, $userId)
    {
        return $query->whereHas('users', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        });
    }
}


