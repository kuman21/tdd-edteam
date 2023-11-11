<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Transfer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake();
    }

    protected function validParams($overrides = []): array
    {
        return array_merge([
            'from_email' => 'john@example.com',
            'to_email' => 'susan@example.com',
            'title' => 'Vaction photos',
            'message' => 'Here i send you the photos',
            'file' => UploadedFile::fake()->image('prety-photo.jpg'),
        ], $overrides);
    }

    public function test_a_guest_user_can_create_transfer(): void
    {
        $this->assertEquals(0, Transfer::count());
        $uploadedFile = UploadedFile::fake()->image('prety-photo.jpg');

        $response = $this->post('/transfers', [
            'from_email' => 'john@example.com',
            'to_email' => 'susan@example.com',
            'title' => 'Vaction photos',
            'message' => 'Here i send you the photos',
            'file' => $uploadedFile,
        ]);

        $response->assertStatus(302)
            ->assertRedirect('/');

        $transfer = Transfer::first();
        $this->assertEquals('john@example.com', $transfer->from_email);
        $this->assertEquals('susan@example.com', $transfer->to_email);
        $this->assertEquals('Vaction photos', $transfer->title);
        $this->assertEquals('Here i send you the photos', $transfer->message);
        $this->assertEquals('prety-photo.jpg', $transfer->file);
        Storage::assertExists('transfers/prety-photo.jpg');

    }

    public function test_the_from_email_field_is_required(): void
    {
        $this->assertEquals(0, Transfer::count());

        $response = $this->post('/transfers', $this->validParams([
            'from_email' => null,
        ]));

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('from_email');
        $this->assertEquals(0, Transfer::count());
    }

    public function test_the_from_email_field_should_be_email(): void
    {
        $this->assertEquals(0, Transfer::count());

        $response = $this->post('/transfers', $this->validParams([
            'from_email' => 'invalid-email-format',
        ]));

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('from_email');
        $this->assertEquals(0, Transfer::count());
    }

    public function test_the_to_email_field_is_required(): void
    {
        $this->assertEquals(0, Transfer::count());

        $response = $this->post('/transfers', $this->validParams([
            'to_email' => null,
        ]));

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('to_email');
        $this->assertEquals(0, Transfer::count());
    }

    public function test_the_to_email_field_should_be_email(): void
    {
        $this->assertEquals(0, Transfer::count());

        $response = $this->post('/transfers', $this->validParams([
            'to_email' => 'invalid-email-format',
        ]));

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('to_email');
        $this->assertEquals(0, Transfer::count());
    }

    public function test_the_title_field_is_required(): void
    {
        $this->assertEquals(0, Transfer::count());

        $response = $this->post('/transfers', $this->validParams([
            'title' => null,
        ]));

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('title');
        $this->assertEquals(0, Transfer::count());
    }

    public function test_the_title_field_should_has_at_least_three_characters(): void
    {
        $this->assertEquals(0, Transfer::count());

        $response = $this->post('/transfers', $this->validParams([
            'title' => 'ab',
        ]));

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('title');
        $this->assertEquals(0, Transfer::count());
    }

    public function test_the_message_field_is_optional(): void
    {
        $this->assertEquals(0, Transfer::count());

        $response = $this->post('/transfers', $this->validParams([
            'message' => null,
        ]));

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasNoErrors();
        $this->assertEquals(1, Transfer::count());
    }

    public function test_the_message_field_should_has_at_least_five_characters(): void
    {
        $this->assertEquals(0, Transfer::count());

        $response = $this->post('/transfers', $this->validParams([
            'message' => 'abcd',
        ]));

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('message');
        $this->assertEquals(0, Transfer::count());
    }

    public function test_the_file_field_is_required(): void
    {
        $this->assertEquals(0, Transfer::count());

        $response = $this->post('/transfers', $this->validParams([
            'file' => null,
        ]));

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('file');
        $this->assertEquals(0, Transfer::count());
    }

    public function test_the_file_field_should_be_a_file(): void
    {
        $this->assertEquals(0, Transfer::count());

        $response = $this->post('/transfers', $this->validParams([
            'file' => 'jdkskkxsk',
        ]));

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('file');
        $this->assertEquals(0, Transfer::count());
    }

    public function test_the_file_field_should_be_max_two_gigabytes(): void
    {
        $this->assertEquals(0, Transfer::count());

        $response = $this->post('/transfers', $this->validParams([
            'file' => UploadedFile::fake()->create('photos.zip', 2097153),
        ]));

        $response->assertStatus(302)
            ->assertRedirect('/')
            ->assertSessionHasErrors('file');
        $this->assertEquals(0, Transfer::count());
    }
}
