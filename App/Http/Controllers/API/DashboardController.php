<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Headquarter;
use Illuminate\Http\Request;
use App\Models\AdviserTask;

class DashboardController extends Controller
{
    public function getCEOData(Request $request)
    {
        if (!$request->user()->tokenCan('view-tasks')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $headquarters = Headquarter::with(['advisers.tasks'])->get();

        $data = $headquarters->map(function ($headquarter) {
            $tasks = $headquarter->advisers->flatMap->tasks;

            $totalTasks = $tasks->count();
            $completedTasks = $tasks->where('pivot.status', 'completed')->count();
            $pendingTasks = $tasks->where('pivot.status', 'uncompleted');

            $compliancePercentage = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

            return [
                'headquarter_name' => $headquarter->name,
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks->count(),
                'compliance_percentage' => $compliancePercentage,
                'pending_users' => $headquarter->advisers->map(function ($adviser) {
                    $adviserPendingTasks = $adviser->tasks->filter(function ($task) {
                        return $task->pivot->status === 'uncompleted';
                    });

                    if ($adviserPendingTasks->isNotEmpty()) {
                        return $adviserPendingTasks->map(function ($task) use ($adviser) {
                            return [
                                'task_title' => $task->title,
                                'user' => $adviser->name,
                            ];
                        });
                    }
                    return null;
                })->filter()->collapse(), 
            ];
        });

        return response()->json(['headquarters' => $data], 200);
    }




    public function getManagerData(Request $request)
{
    try {
        $user = $request->user();

        if (!$user->isManager()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $headquarterId = $user->headquarter_id;

        $assignedTasks = AdviserTask::with(['task:id,title', 'user:id,name,headquarter_id'])
            ->whereHas('user', function ($query) use ($headquarterId) {
                $query->where('headquarter_id', $headquarterId);
            })
            ->get(['id', 'task_id', 'user_id', 'status']);

        $totalTasks = $assignedTasks->count();

        $completedTasks = $assignedTasks->where('status', 'completed')->count();
        $pendingTasks = $assignedTasks->where('status', 'uncompleted');

        $pendingUsers = $pendingTasks->map(function ($task) {
            return [
                'task_title' => $task->task->title ?? 'Tarea sin título',
                'user' => $task->user->name ?? 'Usuario desconocido',
            ];
        })->values();


        return response()->json([
            'headquarter_name' => $user->headquarter->name ?? 'Sede no asignada',
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks->count(),
            'pending_users' => $pendingUsers,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to retrieve manager dashboard data',
            'message' => $e->getMessage(),
        ], 500);
    }
}
}
