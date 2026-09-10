<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Halaman utama (dashboard) dapat dirender setelah login.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $user = User::factory()->create(['ruangan_id' => null]);

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
    }
}
