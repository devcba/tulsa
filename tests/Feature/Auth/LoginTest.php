<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_token(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'token_type',
                'expires_at',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => config('auth.personal_access_tokens.login.name'),
        ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => trans('auth.failed'),
            ]);
    }

    public function test_token_expiration_differs_based_on_remember_me(): void
    {
        Carbon::setTestNow($now = Carbon::parse('2023-01-01 00:00:00'));

        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $tokenName = config('auth.personal_access_tokens.login.name');
        $defaultHours = (int) config('auth.personal_access_tokens.login.expires_in.default');
        $rememberDays = (int) config('auth.personal_access_tokens.login.expires_in.remember');

        $shortResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $shortResponse->assertOk();

        $shortExpiresFromResponse = Carbon::parse($shortResponse->json('expires_at'));
        $expectedShortExpiration = $now->copy()->addHours($defaultHours);

        $this->assertTrue($shortExpiresFromResponse->equalTo($expectedShortExpiration));
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $shortTokenRecord = DB::table('personal_access_tokens')
            ->where('tokenable_id', $user->id)
            ->where('name', $tokenName)
            ->first();

        $this->assertNotNull($shortTokenRecord);
        $this->assertEquals(
            $expectedShortExpiration->toDateTimeString(),
            Carbon::parse($shortTokenRecord->expires_at)->toDateTimeString()
        );

        Carbon::setTestNow($later = Carbon::parse('2023-01-02 00:00:00'));

        $longResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
            'remember_me' => true,
        ]);

        $longResponse->assertOk();

        $longExpiresFromResponse = Carbon::parse($longResponse->json('expires_at'));
        $expectedLongExpiration = $later->copy()->addDays($rememberDays);

        $this->assertTrue($longExpiresFromResponse->equalTo($expectedLongExpiration));
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->assertTrue(
            DB::table('personal_access_tokens')->where('id', $shortTokenRecord->id)->doesntExist()
        );

        $latestTokenRecord = DB::table('personal_access_tokens')
            ->where('tokenable_id', $user->id)
            ->where('name', $tokenName)
            ->first();

        $this->assertNotNull($latestTokenRecord);
        $this->assertEquals(
            $expectedLongExpiration->toDateTimeString(),
            Carbon::parse($latestTokenRecord->expires_at)->toDateTimeString()
        );

        $this->assertTrue($longExpiresFromResponse->greaterThan($shortExpiresFromResponse));

        Carbon::setTestNow();
    }

    public function test_user_can_logout_and_revoke_current_token(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $loginResponse->assertOk();

        $token = $loginResponse->json('token');
        $tokenName = config('auth.personal_access_tokens.login.name');

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => $tokenName,
        ]);

        $logoutResponse = $this
            ->withToken($token)
            ->postJson('/api/auth/logout');

        $logoutResponse->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => $tokenName,
        ]);
    }
}
