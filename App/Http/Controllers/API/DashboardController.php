<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Headquarter;
use App\Models\Task;

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
                'pending_users' => $pendingTasks->map(function ($task) {
                    $adviser = $task->advisers->first();
                    return [
                        'task_title' => $task->title,
                        'user' => $adviser ? $adviser->name : null,
                    ];
                }),
            ];
        });

        return response()->json(['headquarters' => $data], 200);
    }

    public function getManagerData(Request $request)
    {
        if (!$request->user()->tokenCan('view-tasks')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $manager = $request->user();
        $headquarter = $manager->headquarter()->with('advisers.tasks')->first();

        if (!$headquarter) {
            return response()->json(['error' => 'No headquarter found for the manager'], 404);
        }

        $tasks = $headquarter->advisers->flatMap->tasks;

        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('pivot.status', 'completed')->count();
        $pendingTasks = $tasks->where('pivot.status', 'uncompleted');

        return response()->json([
            'headquarter_name' => $headquarter->name,
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks->count(),
            'pending_users' => $pendingTasks->map(function ($task) {
                $adviser = $task->advisers->first();
                return [
                    'task_title' => $task->title,
                    'user' => $adviser ? $adviser->name : null,
                ];
            }),
        ], 200);
    }
}
