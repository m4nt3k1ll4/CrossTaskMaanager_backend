<?php

namespace App\Policies;

use App\Models\AppUser;
use App\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(AppUser $user)
    {
        return in_array($user->role->name, ['manager', 'ceo']);
    }

    public function view(AppUser $user, Task $task)
    {
        return $user->role->name === 'ceo' ||
               ($user->role->name === 'manager' && $task->headquarter_id == $user->headquarter_id) ||
               ($user->role->name === 'adviser' && $task->assigned_to_id === $user->id);
    }

    public function create(AppUser $user)
    {
        return in_array($user->role->name, ['manager', 'ceo']);
    }

    public function update(AppUser $user)
    {
        return $user->role->name === 'ceo' ||
               ($user->role->name === 'manager' && $task->headquarter_id == $user->headquarter_id) ||
               ($user->role->name === 'adviser' && $task->assigned_to_id === $user->id);
    }

    public function delete(AppUser $user)
    {
        return $user->role->name === 'ceo';
    }
}
