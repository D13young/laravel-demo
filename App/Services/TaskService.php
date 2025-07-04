<?php

namespace App\Services;

use App\Models\Task;
use App\Search\TaskIndexer;

class TaskService
{
    protected $searchIndexer;

    public function __construct(TaskIndexer $searchIndexer)
    {
        $this->searchIndexer = $searchIndexer;
    }

    public function createTask(array $data)
    {
        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'user_id' => $data['user_id']
        ]);

        $this->searchIndexer->index($task);

        return $task;
    }
}