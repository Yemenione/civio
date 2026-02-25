<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Resume;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_resume()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/resumes');

        $response->assertRedirect();
        $this->assertDatabaseHas('resumes', [
            'user_id' => $user->id,
            'title' => 'My Resume',
        ]);
    }

    public function test_user_can_view_dashboard()
    {
        $user = User::factory()->create();
        Resume::create([
            'user_id' => $user->id,
            'title' => 'Test Resume',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }
}
