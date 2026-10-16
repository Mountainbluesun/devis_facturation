<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function createDocumentFor(User $user, string $number): Document
    {
        $client = $user->clients()->create(['name' => 'Client of user ' . $user->id]);

        return $user->documents()->create([
            'type' => 'quote',
            'number' => $number,
            'client_id' => $client->id,
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
        ]);
    }

    public function test_user_only_sees_their_own_documents_in_the_list(): void
    {
        $marie = User::factory()->create();
        $paul = User::factory()->create();

        $this->createDocumentFor($marie, 'QUO-2026-001');
        $this->createDocumentFor($paul, 'QUO-2026-002');

        $response = $this->actingAs($marie)->get(route('documents.index'));

        $response->assertSee('QUO-2026-001');
        $response->assertDontSee('QUO-2026-002');
    }

    public function test_user_cannot_open_another_users_document(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulDocument = $this->createDocumentFor($paul, 'QUO-2026-002');

    $this->actingAs($marie)
        ->get(route('documents.show', $paulDocument))
        ->assertNotFound();
}

public function test_user_cannot_open_the_edit_form_of_another_users_document(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulDocument = $this->createDocumentFor($paul, 'QUO-2026-002');

    $this->actingAs($marie)
        ->get(route('documents.edit', $paulDocument))
        ->assertNotFound();
}

public function test_user_cannot_update_another_users_document(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulDocument = $this->createDocumentFor($paul, 'QUO-2026-002');

    $this->actingAs($marie)
        ->put(route('documents.update', $paulDocument), ['status' => 'sent'])
        ->assertNotFound();

    $this->assertDatabaseHas('documents', ['id' => $paulDocument->id, 'status' => 'draft']);
}

public function test_user_cannot_delete_another_users_document(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulDocument = $this->createDocumentFor($paul, 'QUO-2026-002');

    $this->actingAs($marie)
        ->delete(route('documents.destroy', $paulDocument))
        ->assertNotFound();

    $this->assertDatabaseHas('documents', ['id' => $paulDocument->id]);
}
    public function test_created_document_belongs_to_the_logged_in_user(): void
{
    $marie = User::factory()->create();
    $marieClient = $marie->clients()->create(['name' => 'Marie Client']);

    $this->actingAs($marie)->post(route('documents.store'), [
        'type' => 'quote',
        'client_id' => $marieClient->id,
        'lines' => [
            [
                'description' => 'Service A',
                'quantity' => 1,
                'unit_price' => 100,
                'vat_rate' => 20,
            ],
        ],
    ]);

    $this->assertDatabaseHas('documents', [
        'client_id' => $marieClient->id,
        'user_id' => $marie->id,
    ]);
}

    public function test_user_cannot_convert_another_users_quote(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulQuote = $this->createDocumentFor($paul, 'QUO-2026-002');
    $paulQuote->update(['status' => 'accepted']);

    $this->actingAs($marie)
        ->post(route('documents.convert-to-invoice', $paulQuote))
        ->assertNotFound();

    $this->assertDatabaseMissing('documents', ['type' => 'invoice']);
}

public function test_user_cannot_download_another_users_pdf(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulDocument = $this->createDocumentFor($paul, 'QUO-2026-002');

    $this->actingAs($marie)
        ->get(route('documents.pdf', $paulDocument))
        ->assertNotFound();
}


}
