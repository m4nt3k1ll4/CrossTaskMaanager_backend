<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdvicerTaskImage;
use App\Models\AdviserTask;
use App\Models\AppUser;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
//task CRUD
    public function index(Request $request)
    {
        try {
            $tasks = Task::with(['comments', 'images'])->get();
            return response()->json($tasks, 200);
        } catch (\Exception $e) {
            return $this->handleError('Error fetching tasks', $e);
        }
    }

    public function store(Request $request)
    {
        if (!$request->user()->tokenCan('manage-tasks')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $this->authorize('create', Task::class);
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|string|in:low,medium,high',
                'due_date' => 'required|string',
            ]);

            $task = Task::create($validatedData);
            return response()->json(['status' => 'success', 'message' => 'Task created successfully', 'task' => $task], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Invalid data entry', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return $this->handleError('Error creating task', $e);
        }
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->tokenCan('view-tasks')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->authorize('view', Task::class);

        try {
            $task = Task::with(['user', 'headquarter'])->findOrFail($id);
            return response()->json(['status' => 'success', 'data' => $task], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found'], 404);
        } catch (\Exception $e) {
            return $this->handleError('Error fetching task', $e);
        }
    }

    public function update(Request $request, $id)
    {
        if (!$request->user()->tokenCan('manage-tasks')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task = Task::findOrFail($id);

        $this->authorize('update', $task);

        try {
            $validatedData = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'priority' => 'sometimes|in:low,medium,high',
                'due_date' => 'sometimes|date',
            ]);

            $task->update($validatedData);

            return response()->json(['status' => 'success', 'message' => 'Task updated successfully', 'task' => $task], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Invalid data entry', 'details' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found'], 404);
        } catch (\Exception $e) {
            return $this->handleError('Error updating task', $e);
        }
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->tokenCan('manage-tasks')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $task = Task::findOrFail($id);

        $this->authorize('delete', $task);

        try {
            $task->delete();
            return response()->json(['status' => 'success', 'message' => 'Task deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Task not found'], 404);
        } catch (\Exception $e) {
            return $this->handleError('Error deleting task', $e);
        }
    }

//task assignation methods

    public function getTasksAndUsers(request $request)
    {

        try {
            $tasks = Task::all();
            $user = $request->user();
            if ($user->isManager()) {
                $users = AppUser::where('role_id', 3)->where('headquarter_id', $user->headquarter_id)->get();
                return response()->json([
                    'tasks' => $tasks,
                    'users' => $users,
                ], 200);
            }
            $users = AppUser::where('role_id', 3)->get();

            return response()->json([
                'tasks' => $tasks,
                'users' => $users,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load tasks and users', 'message' => $e->getMessage()], 500);
        }
    }

    public function getAssignedTaskId(Request $request, $id)
    {
        if (!$request->user()->tokenCan('view-tasks')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }


        try {
            $assignedTask = AdviserTask::with(['user', 'task'])->findOrFail($id);
            return response()->json($assignedTask, 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Assigned Task not found'], 404);
        } catch (\Exception $e) {
            return $this->handleError('Error fetching Assigned task', $e);
        }
    }

    public function getAssignedTasks(request $request)
    {
        try {
            $user = $request->user();
            if ($user->isAdviser()) {
                $userId = $user->id;
                $assignedTasks = AdviserTask::with(['task:id,title', 'user:id,name'])->where('user_id', $userId)
                    ->get(['id', 'task_id', 'user_id', 'status']);
                $response = $assignedTasks->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'task_id' => $assignment->task_id,
                        'task_title' => $assignment->task->title ?? null,
                        'user_id' => $assignment->user_id,
                        'user_name' => $assignment->user->name ?? null,
                        'status' => $assignment->status,
                    ];
                });
                return response()->json($response, 200);
            }


            if ($user->isManager()) {
                $headquarterId = $user->headquarter_id;
                $assignedTasks = AdviserTask::with(['task:id,title', 'user:id,name'])
                    ->whereHas('user', function ($query) use ($headquarterId) {
                        $query->where('headquarter_id', $headquarterId);
                    })
                    ->get(['id', 'task_id', 'user_id', 'status']);
                $response = $assignedTasks->map(function ($assignment) {
                    return [
                        'id' => $assignment->id,
                        'task_id' => $assignment->task_id,
                        'task_title' => $assignment->task->title ?? null,
                        'user_id' => $assignment->user_id,
                        'user_name' => $assignment->user->name ?? null,
                        'status' => $assignment->status,
                    ];
                });
                return response()->json($response, 200);
            }
            $assignedTasks = AdviserTask::with(['task:id,title', 'user:id,name'])
                ->get(['id', 'task_id', 'user_id', 'status']);

            $response = $assignedTasks->map(function ($assignment) {

                return [
                    'id' => $assignment->id,
                    'task_id' => $assignment->task_id,
                    'task_title' => $assignment->task->title ?? null,
                    'user_id' => $assignment->user_id,
                    'user_name' => $assignment->user->name ?? null,
                    'status' => $assignment->status,
                ];
            });
            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to retrieve assigned tasks',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function assignTaskToAdviser(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'user_id' => 'required|exists:app_users,id',
        ]);

        try {
            $task = Task::findOrFail($request->task_id);
            $user = AppUser::findOrFail($request->user_id);

            if ($task->users()->where('user_id', $user->id)->exists()) {
                return response()->json(['error' => 'Task already assigned to this user'], 400);
            }

            $task->users()->attach($user->id, ['status' => 'uncompleted']);

            return response()->json([
                'status' => 'success',
                'message' => 'Task assigned to adviser successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to assign task', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateAssignedTaskStatus(Request $request, $Id)
    {
        $request->validate([
            'status' => 'required|string|in:uncompleted,completed,progress',
        ]);

        try {
            $adviserTask = AdviserTask::find($Id);

            if (!$adviserTask) {
                return response()->json(['error' => 'Assigned task not found'], 404);
            }

            $adviserTask->status = $request->status;
            $adviserTask->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Task status updated successfully.',
                'data' => [
                    'id' => $adviserTask->id,
                    'task_id' => $adviserTask->task_id,
                    'user_id' => $adviserTask->user_id,
                    'status' => $adviserTask->status,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update task status', 'message' => $e->getMessage()], 500);
        }
    }

    public function unassignTaskById($adviserTaskId)
    {
        $adviserTask = AdviserTask::find($adviserTaskId);

        if (!$adviserTask) {
            return response()->json(['error' => 'Relation not found'], 404);
        }

        $adviserTask->delete();
        return response()->json(['success' => 'Task unassigned successfully']);

    }

    public function getImages($taskId)
    {
        try {

            $task = Task::with('images')->findOrFail($taskId);

            if (!$task->images->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $task->images,
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'data' => [],
                'message' => 'No images found for this task',
            ], 200);

        } catch (\Exception $e) {
            return $this->handleError('Failed to get images', $e);
        }
    }

    public function postImages(Request $request, string $assignedTaskId)
    {
        try {
            if (!$request->user()->tokenCan('view-tasks')) {
                return $this->handleError('Unauthorized', new \Exception('Token scope error'));
            }

            $request->validate([
                'image' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            ]);
            $assignedTask = AdviserTask::findOrFail($assignedTaskId);

            if ($assignedTask->user_id !== $request->user()->id && !$request->user()->isCeo()) {
                return response()->json(['error' => 'User not assigned to this task'], 403);
            }

            if ($request->hasFile('image')) {
                $fileName = time() . '_' . $request->file('image')->getClientOriginalName();
                $filePath = $request->file('image')->storeAs('uploads/task_images', $fileName, 'public');

                $status = file_exists(public_path('storage/' . $filePath)) ? 'uploaded' : 'failed';

                $advicerTaskImage = AdvicerTaskImage::create([
                    'task_id' => $assignedTask->task_id,
                    'user_id' => $request->user()->id,
                    'status' => $status,
                    'image_path' => '/storage/' . $filePath,
                ]);
                return response()->json($advicerTaskImage,201);

            }

            return response()->json(['error' => 'No image uploaded'], 400);
        } catch (\Exception $e) {
            return $this->handleError('Failed to upload image', $e);
        }
    }

    public function deleteImage(Request $request, $taskId, $imageId)
    {
        try {
            if (!$request->user()->tokenCan('view-tasks')) {
                return $this->handleError('Unauthorized', new \Exception('Token scope error'));
            }

            $task = Task::findOrFail($taskId);
            $image = AdvicerTaskImage::findOrFail($imageId);

            if ($task->user_id !== $request->user()->id && !$request->user()->isCeo()) {
                return response()->json(['error' => 'User not assigned to this task'], 403);
            }

            $imagePath = public_path('storage/' . $image->image_path);

            if (file_exists($imagePath)) {
                unlink($imagePath);
            } else {
                return response()->json(['error' => 'Image file not found'], 404);
            }

            $image->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Image deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return $this->handleError('Failed to delete image', $e);
        }
    }

    private function handleError($message, \Exception $e)
    {
        return response()->json(['error' => $message, 'details' => $e->getMessage()], 500);
    }

}
