<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskCreateRequest;
use App\Integrations\BitrixService;
use App\Jobs\ProcessTaskNotification;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    protected $taskService;
    protected $bitrix;

    public function __construct(TaskService $taskService, BitrixService $bitrix)
    {
        $this->taskService = $taskService;
        $this->bitrix = $bitrix;
    }

    public function store(TaskCreateRequest $request)
    {
        $task = DB::transaction(function () use ($request) {
            $task = $this->taskService->createTask($request->validated());

            $bitrixId = $this->bitrix->createDeal([
                'title' => $task->title,
                'client_id' => $task->user_id
            ]);

            $task->update(['bitrix_id' => $bitrixId]);
            return $task;
        });

        ProcessTaskNotification::dispatch($task);

        return response()->json([
            'data' => $task,
            'links' => [
                'self' => url('/tasks/'.$task->id)
            ]
        ], 201);
    }
}