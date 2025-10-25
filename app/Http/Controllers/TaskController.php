<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            // Filtering
            $query = Task::query()
            ->when(!empty($request->status), function ($q) use($request) {
                $q->where('status', $request->status);
            })
            ->when(!empty($request->input('search')), function ($q) use($request) {
                $q->whereAny(['title', 'description'], 'like', '%' . $request->input('search') . '%');
            });

            if ($sort = $request->input('sort')) {
                $order = $request->input('order', 'desc');
                $query->orderBy($sort, $order);
            }

            // Pagination
            $perPage = $request->input('per_page', 10);
            $tasks = $query->paginate($perPage);

            return $this->success('Tasks retrieved successfully', TaskResource::collection($tasks)->response()->getData(true));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error("Failed to get data task", 500);

        }
    }


    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'sometimes|in:todo,in-progress,done',
                'due_date' => 'nullable|date|after_or_equal:today',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), 422);
            }

            $task = Task::create($request->all());

            return $this->success('Task created successfully', new TaskResource($task), 201);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error("Failed to save data task", 500);

        }
    }


    public function show(Task $task)
    {
        try {

            return $this->success('Task retrieved successfully', new TaskResource($task));

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error("Failed to get data task", 500);

        }
    }


    public function update(Request $request, Task $task)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'sometimes|in:todo,in-progress,done',
                'due_date' => 'nullable|date|after_or_equal:today',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors()->first(), 422);
            }

            $task->update($request->all());

            return $this->success('Task updated successfully', new TaskResource($task));

        } catch (Exception $e) {
             Log::error($e->getMessage());
            return $this->error("Failed to update task", 500);

        }
    }


    public function destroy(Task $task)
    {
        try {
            $task->delete();
            return $this->success('Task deleted successfully', null, 204);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->error("Failed to delete task", 500);

        }
    }
}
