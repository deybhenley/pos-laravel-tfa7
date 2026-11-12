<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PosAccountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_lists_and_create_and_edit_forms_are_accessible(): void
    {
        foreach (['/', '/about', '/customers', '/customers/create', '/users', '/users/create'] as $path) {
            $this->get($path)->assertOk();
        }
        $customer = Customer::first();
        $user = User::first();
        $this->get(route('customers.edit', $customer))->assertOk()->assertSee('value="'.$customer->full_name.'"', false);
        $this->get(route('users.edit', $user))->assertOk()->assertSee('value="'.$user->username.'"', false);
    }

    public function test_customer_validation_preserves_old_input_without_inserting(): void
    {
        $this->from('/customers/create')->post('/customers', [
            'full_name' => 'Keep This Name', 'email' => 'not-an-email', 'phone' => '0900 000 2222',
        ])->assertRedirect('/customers/create')->assertSessionHasErrors('email')->assertSessionHasInput('full_name', 'Keep This Name');
        $this->assertDatabaseCount('customers', 5);
        $this->post('/customers', ['full_name' => '', 'email' => ''])->assertSessionHasErrors(['full_name', 'email']);
    }

    public function test_valid_customer_can_be_created_without_an_optional_phone(): void
    {
        $this->post('/customers', ['full_name' => 'New Customer', 'email' => 'new.customer@example.com'])
            ->assertRedirect(route('customers.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('customers', ['full_name' => 'New Customer', 'email' => 'new.customer@example.com', 'phone' => null]);
    }

    public function test_customer_can_be_updated_and_invalid_update_preserves_saved_record(): void
    {
        $customer = Customer::first();
        $this->put(route('customers.update', $customer), [
            'full_name' => 'Updated Customer', 'email' => 'updated@example.com', 'phone' => '0900 000 3333',
        ])->assertRedirect(route('customers.index'));
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'full_name' => 'Updated Customer']);
        $this->put(route('customers.update', $customer), ['full_name' => 'Rejected Change', 'email' => 'bad'])
            ->assertSessionHasErrors('email');
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'full_name' => 'Updated Customer']);
    }

    public function test_user_creation_rejects_duplicate_username_and_requires_a_full_name(): void
    {
        $this->post('/users', ['username' => 'alex.admin', 'full_name' => ''])
            ->assertSessionHasErrors(['username', 'full_name']);
        $this->assertDatabaseCount('users', 5);
    }

    public function test_valid_user_can_be_created(): void
    {
        $this->post('/users', ['username' => 'new.cashier', 'full_name' => 'New Cashier'])
            ->assertRedirect(route('users.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['username' => 'new.cashier', 'full_name' => 'New Cashier', 'avatar' => null]);
    }

    public function test_update_accepts_the_users_own_username_but_rejects_someone_elses(): void
    {
        $user = User::first();
        $this->put(route('users.update', $user), ['username' => $user->username, 'full_name' => 'Updated User'])
            ->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'full_name' => 'Updated User']);
        $other = User::where('id', '!=', $user->id)->first();
        $this->put(route('users.update', $user), ['username' => $other->username, 'full_name' => 'Rejected Name'])
            ->assertSessionHasErrors('username');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'full_name' => 'Updated User']);
    }

    public function test_avatar_is_prepared_as_a_256_pixel_png_and_only_its_path_is_saved(): void
    {
        $user = User::first();
        $this->put(route('users.update', $user), [
            'username' => $user->username,
            'full_name' => $user->full_name,
            'avatar' => UploadedFile::fake()->image('original-photo.jpg', 640, 480)->size(200),
        ])->assertRedirect(route('users.index'));

        $user->refresh();
        $this->assertMatchesRegularExpression('/\Aavatars\/[a-f0-9-]{36}\.png\z/', $user->avatar);
        Storage::disk('public')->assertExists($user->avatar);
        [$width, $height, $type] = getimagesize(Storage::disk('public')->path($user->avatar));
        $this->assertSame(256, $width);
        $this->assertSame(256, $height);
        $this->assertSame(IMAGETYPE_PNG, $type);
        $this->get(route('avatars.show', ['filename' => basename($user->avatar)]))->assertOk()->assertHeader('Content-Type', 'image/png');
    }

    public function test_non_image_upload_is_rejected_and_text_is_retained(): void
    {
        $user = User::first();
        $this->from(route('users.edit', $user))->put(route('users.update', $user), [
            'username' => $user->username,
            'full_name' => 'Retain This Input',
            'avatar' => UploadedFile::fake()->create('document.txt', 5, 'text/plain'),
        ])->assertSessionHasErrors('avatar')->assertSessionHasInput('full_name', 'Retain This Input');
        $this->assertNull($user->fresh()->avatar);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_image_larger_than_two_megabytes_is_rejected(): void
    {
        $user = User::first();
        $this->put(route('users.update', $user), [
            'username' => $user->username,
            'full_name' => $user->full_name,
            'avatar' => UploadedFile::fake()->image('large.png')->size(2049),
        ])->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar);
    }

    public function test_edit_without_a_file_keeps_the_avatar_and_a_replacement_removes_the_old_file(): void
    {
        $user = User::first();
        $fields = ['username' => $user->username, 'full_name' => $user->full_name];
        $this->put(route('users.update', $user), $fields + ['avatar' => UploadedFile::fake()->image('first.png')])->assertSessionHasNoErrors();
        $firstPath = $user->fresh()->avatar;
        $this->put(route('users.update', $user), $fields)->assertSessionHasNoErrors();
        $this->assertSame($firstPath, $user->fresh()->avatar);
        Storage::disk('public')->assertExists($firstPath);
        $this->put(route('users.update', $user), $fields + ['avatar' => UploadedFile::fake()->image('second.jpg')])->assertSessionHasNoErrors();
        $secondPath = $user->fresh()->avatar;
        $this->assertNotSame($firstPath, $secondPath);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($secondPath);
    }

    public function test_missing_avatar_route_returns_404_and_placeholder_is_available(): void
    {
        $this->get('/users')->assertOk()->assertSee('avatar-placeholder.svg');
        $this->get('/avatars/00000000-0000-0000-0000-000000000000.png')->assertNotFound();
        $this->get('/avatars/not-a-generated-name.txt')->assertNotFound();
    }
}
