<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdviserTask extends Model
{

    public $table = "adviser_task";

    protected $fillable = [
        'task_id',
        'user_id',
        'status',
    ];
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function user()
    {
        return $this->belongsTo(AppUser::class);
    }

}
