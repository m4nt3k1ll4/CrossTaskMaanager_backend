<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvicerTaskImage extends Model
{
    protected $fillable = ['task_id', 'user_id', 'status', 'image_path']; 

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }


    public function user()
    {
        return $this->belongsTo(AppUser::class, 'user_id');
    }
}
//(•ˋ _ ˊ•)