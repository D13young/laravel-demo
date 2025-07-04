<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_creation()
    {
        Queue::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'title' => 'API Integration',
            'description' => 'Connect to Bitrix API',
            'user_id' => $user->id
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'bitrix_id'],
                'links'
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'API Integration',
            'bitrix_id' => 'BX_' // Проверка префикса
        ]);

        Queue::assertPushed(ProcessTaskNotification::class);
    }
}