<?php
namespace App\Policies;

use App\Models\Task;
use App\Models\AppUser; // Cambiar esto si el modelo de usuario tiene otro nombre
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determina si el usuario puede ver cualquier tarea.
     *
     * @param  \App\Models\AppUser  $user
     * @return bool
     */
    public function viewAny(AppUser $user)
    {
        return $user->hasScope('view-tasks');
    }

    /**
     * Determina si el usuario puede ver una tarea específica.
     *
     * @param  \App\Models\AppUser  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function view(AppUser $user, Task $task)
    {
        // Si el usuario tiene el scope o está asignado a la tarea
        return $user->hasScope('view-tasks') || $user->id === $task->assigned_user_id;
    }

    /**
     * Determina si el usuario puede crear una tarea.
     *
     * @param  \App\Models\AppUser  $user
     * @return bool
     */
    public function create(AppUser $user)
    {
        return $user->hasScope('manage-tasks');
    }

    /**
     * Determina si el usuario puede actualizar una tarea.
     *
     * @param  \App\Models\AppUser  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function update(AppUser $user, Task $task)
    {
        return $user->hasScope('manage-tasks') || $user->id === $task->assigned_user_id;
    }

    /**
     * Determina si el usuario puede eliminar una tarea.
     *
     * @param  \App\Models\AppUser  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function delete(AppUser $user, Task $task)
    {
        return $user->hasScope('manage-tasks');
    }

    /**
     * Determina si el usuario puede asignar una tarea.
     *
     * @param  \App\Models\AppUser  $user
     * @return bool
     */
    public function assign(AppUser $user)
    {
        return $user->hasScope('manage-tasks');
    }
}
