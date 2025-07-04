<?php

namespace App\Search;

use App\Models\Task;

class TaskIndexer
{
    public function index(Task $task)
    {
        \Log::info("Task #{$task->id} indexed: {$task->title}");
    }
}