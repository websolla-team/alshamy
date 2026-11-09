<?php

namespace Tests\Feature;

use Illuminate\Auth\RequestGuard;
use Illuminate\Auth\SessionGuard;
use Illuminate\Auth\TokenGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mockery;
use Tests\TestCase;

class SecurityAuthenticationExposureTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_legacy_token_guard_is_exposed_through_auth_configuration(): void
    {
        $this->assertSame('web', config('auth.defaults.guard'));
        $this->assertSame('session', config('auth.guards.web.driver'));
        $this->assertSame(['web'], config('sanctum.guard'));

        foreach (config('auth.guards') as $guard) {
            $this->assertNotSame('token', $guard['driver']);
        }

        $this->assertInstanceOf(SessionGuard::class, Auth::guard('web'));
        $this->assertInstanceOf(RequestGuard::class, Auth::guard('sanctum'));
        $this->assertNotInstanceOf(TokenGuard::class, Auth::guard('sanctum'));
    }

    public function test_api_user_endpoint_requires_sanctum_and_rejects_unknown_credentials(): void
    {
        $route = Route::getRoutes()->match(Request::create('/api/user', 'GET'));
        $this->assertContains('auth:sanctum', $route->gatherMiddleware());

        $this->withHeader('Authorization', 'Bearer not-a-valid-token')
            ->getJson('/api/user')
            ->assertUnauthorized();
    }

    public function test_installed_token_guard_rejects_non_string_credentials_before_user_lookup(): void
    {
        // This characterizes the installed vendor code; it does not override
        // the official Composer advisory classification for Laravel 10.
        $provider = Mockery::mock(UserProvider::class);
        $provider->shouldNotReceive('retrieveByCredentials');

        $request = Request::create('/api/user', 'GET', [
            'api_token' => ['not-a-string'],
        ]);
        $guard = new TokenGuard($provider, $request);

        $this->assertNull($guard->user());
        $this->assertFalse($guard->validate(['api_token' => ['not-a-string']]));
    }
}
