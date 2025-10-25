<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::updateOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'abilities' => [
                    'task_read',
                    'task_create',
                    'task_update',
                    'task_delete',
                ],
            ]
        );
    }

    protected function authenticate(array $abilities): void
    {
        Sanctum::actingAs($this->user->fresh(), $abilities);
    }

    protected function createTask(array $overrides = []): Task
    {
        $defaults = [
            'title' => 'Sample Task ' . uniqid(),
            'description' => 'Sample description',
            'status' => 'todo',
            'due_date' => Carbon::now()->addWeek(),
        ];

        return Task::create(array_merge($defaults, $overrides));
    }

    public function testLoginReturnsToken(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@mail.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logged in successfully',
            ])
            ->assertJsonStructure([
                'data' => [
                    'name',
                    'email',
                    'token',
                ],
            ]);
    }

    public function testCanListTasks(): void
    {
        $this->authenticate(['task_read']);

        $tasks = [
            $this->createTask(['title' => 'List Task One']),
            $this->createTask(['title' => 'List Task Two']),
        ];

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Tasks retrieved successfully',
            ]);

        $payloadTitles = collect($response->json('data.data'))->pluck('title');

        $this->assertTrue($payloadTitles->contains($tasks[0]->title));
        $this->assertTrue($payloadTitles->contains($tasks[1]->title));
    }

    public function testCanCreateTask(): void
    {
        $this->authenticate(['task_create']);

        $payload = [
            'title' => 'Created Task',
            'description' => 'Task created through API test',
            'status' => 'todo',
            'due_date' => Carbon::now()->addDay()->toDateString(),
        ];

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task created successfully',
            ])
            ->assertJsonPath('data.title', $payload['title']);
    }

    public function testCanShowTask(): void
    {
        $this->authenticate(['task_read']);

        $task = $this->createTask(['title' => 'Showable Task']);

        $response = $this->getJson('/api/tasks/' . $task->_id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task retrieved successfully',
            ])
            ->assertJsonPath('data.title', $task->title);
    }

    public function testCanUpdateTask(): void
    {
        $this->authenticate(['task_update']);

        $task = $this->createTask(['title' => 'Original Title']);

        $payload = [
            'title' => 'Updated Title',
            'status' => 'done',
        ];

        $response = $this->putJson('/api/tasks/' . $task->_id, $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task updated successfully',
            ])
            ->assertJsonPath('data.title', $payload['title'])
            ->assertJsonPath('data.status', $payload['status']);

        $this->assertEquals($payload['title'], Task::find($task->_id)->title);
    }

    public function testCanDeleteTask(): void
    {
        $this->authenticate(['task_delete']);

        $task = $this->createTask(['title' => 'Deletable Task']);

        $response = $this->deleteJson('/api/tasks/' . $task->_id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task deleted successfully',
            ]);

        $deletedTask = Task::withTrashed()->find($task->_id);

        $this->assertNotNull($deletedTask);
        $this->assertNotNull($deletedTask->deleted_at);
    }
}
