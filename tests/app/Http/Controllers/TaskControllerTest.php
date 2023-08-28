<?php

namespace Tests\app\Http\Controllers;

use App\Http\Controllers\api\TaskController;
use App\Models\File;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create task
        $task = Task::factory()->state([
            'created_by' => $user->id
        ])->create();

        File::factory()->state([
            'task_id' => $task->id
        ])->create();

        $response = $this->get('api/tasks');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'tasks' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'completed',
                    'completed_at',
                    'created_by',
                    'updated_by',
                    'created_at',
                    'updated_at',
                    'creator' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                    'updater',
                    'files' => [
                        '*' => [
                            'id',
                            'pdf',
                            'task_id',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testStore()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'title' => 'Test 1',
            'description' => 'description 1',
            'pdf' => ['test 1', 'test 2']
        ];

        $response = $this->post('/api/tasks', $data);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function testShow()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create task
        $task = Task::factory()->state([
            'created_by' => $user->id
        ])->create();

        File::factory()->state([
            'task_id' => $task->id
        ])->create();

        $response = $this->get("/api/tasks/{$task->id}");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'task' => [
                'id',
                'title',
                'description',
                'completed',
                'completed_at',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at',
                'creator' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
                'updater',
                'files' => [
                    '*' => [
                        'id',
                        'pdf',
                        'task_id',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ],
        ]);
    }

    public function testUpdate()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->state([
            'created_by' => $user->id
        ])->create();

        File::factory()->state([
            'task_id' => $task->id
        ])->create();

        $taskUpdate = [
            'title' => 'Update 1',
            'description' => 'Update 1',
            'pdf' => ['Update 1']
        ];

        $response = $this->put("/api/tasks/{$task->id}", $taskUpdate);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message'
        ]);
    }

    public function testDelete()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->state([
            'created_by' => $user->id
        ])->create();

        File::factory()->state([
            'task_id' => $task->id
        ])->create();

        $response = $this->delete("/api/tasks/{$task->id}");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message'
        ]);
    }
}
