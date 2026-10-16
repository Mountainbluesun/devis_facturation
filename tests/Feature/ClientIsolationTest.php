<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_only_sees_their_own_clients_in_the_list(): void
    {
        $marie = User::factory()->create();
        $paul = User::factory()->create();

        $marie->clients()->create(['name' => 'Marie Client']);
        $paul->clients()->create(['name' => 'Paul Client']);

        $response = $this->actingAs($marie)->get(route('clients.index'));

        $response->assertSee('Marie Client');
        $response->assertDontSee('Paul Client');
    }

    public function test_created_client_belongs_to_the_logged_in_user(): void
    {
        $marie = User::factory()->create();

        $this->actingAs($marie)->post(route('clients.store'), [
            'name' => 'New Client',
        ]);

        $this->assertDatabaseHas('clients', [
            'name' => 'New Client',
            'user_id' => $marie->id,
        ]);
    }

    public function test_user_cannot_open_another_users_client(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulClient = $paul->clients()->create(['name' => 'Paul Client']);

    $response = $this->actingAs($marie)->get(route('clients.show', $paulClient));

    $response->assertNotFound();
}

    public function test_user_cannot_open_the_edit_form_of_another_users_client(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulClient = $paul->clients()->create(['name' => 'Paul Client']);

    $this->actingAs($marie)
        ->get(route('clients.edit', $paulClient))
        ->assertNotFound();
}

public function test_user_cannot_update_another_users_client(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulClient = $paul->clients()->create(['name' => 'Paul Client']);

    $this->actingAs($marie)
        ->put(route('clients.update', $paulClient), ['name' => 'Hacked'])
        ->assertNotFound();

    $this->assertDatabaseHas('clients', ['id' => $paulClient->id, 'name' => 'Paul Client']);
}

public function test_user_cannot_delete_another_users_client(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulClient = $paul->clients()->create(['name' => 'Paul Client']);

    $this->actingAs($marie)
        ->delete(route('clients.destroy', $paulClient))
        ->assertNotFound();

    $this->assertDatabaseHas('clients', ['id' => $paulClient->id]);
}
}
