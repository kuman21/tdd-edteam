<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use App\Models\User;


class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_login()
    {
        $user = User::factory()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/dashboard');

        $this->assertTrue(Auth::check());
        $this->assertEquals('John', Auth::user()->name);
        $this->assertTrue(Auth::user()->is($user));
    }

    /** @test */
    public function a_user_cannot_log_in_if_credentials_are_incorrect()
    {
        $user = User::factory()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'john@example.com',
            'password' => 'incorrect',
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/login');

        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function the_email_field_is_required()
    {
        $user = User::factory()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => null,
            'password' => 'password',
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function the_email_field_should_be_a_valid_email()
    {
        $user = User::factory()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'incorrect-email-format',
            'password' => 'password',
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function the_password_field_is_required()
    {
        $user = User::factory()->create([
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'john@example.com',
            'password' => null,
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/login')
            ->assertSessionHasErrors('password');

        $this->assertFalse(Auth::check());
    }
}
