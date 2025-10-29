<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_users(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $users = User::factory()->count(3)->create();

        $response = $this->getJson('/api/users');

        $response->assertOk()
            ->assertJsonCount(4, 'data')
            ->assertJsonFragment([
                'id' => $users->first()->id,
                'name' => $users->first()->name,
                'email' => $users->first()->email,
            ]);
    }

    /** @test */
    public function it_creates_a_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Jane Doe')
            ->assertJsonPath('data.email', 'jane@example.com');

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
        ]);

        $createdUser = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue(Hash::check('password123', $createdUser->password));
    }

    /** @test */
    public function it_shows_a_user(): void
    {
        $actingUser = User::factory()->create();
        Sanctum::actingAs($actingUser);

        $user = User::factory()->create();

        $response = $this->getJson("/api/users/{$user->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email);
    }

    /** @test */
    public function it_updates_a_user(): void
    {
        $actingUser = User::factory()->create();
        Sanctum::actingAs($actingUser);

        $user = User::factory()->create([
            'email' => 'original@example.com',
        ]);

        $payload = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'password' => 'newpassword123',
        ];

        $response = $this->putJson("/api/users/{$user->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Name')
            ->assertJsonPath('data.email', 'updated@example.com');

        $user->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('updated@example.com', $user->email);
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    /** @test */
    public function it_deletes_a_user(): void
    {
        $actingUser = User::factory()->create();
        Sanctum::actingAs($actingUser);

        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_a_user(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function it_validates_unique_email_on_create_and_update(): void
    {
        $actingUser = User::factory()->create();
        Sanctum::actingAs($actingUser);

        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $createResponse = $this->postJson('/api/users', [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'password123',
        ]);

        $createResponse->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $targetUser = User::factory()->create([
            'email' => 'target@example.com',
        ]);

        $updateResponse = $this->putJson("/api/users/{$targetUser->id}", [
            'email' => 'existing@example.com',
        ]);

        $updateResponse->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
