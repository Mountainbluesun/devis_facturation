<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function createDocumentFor(User $user, string $numero): Document
    {
        $client = $user->clients()->create(['nom' => 'Client of user ' . $user->id]);

        return $user->documents()->create([
            'type' => 'devis',
            'numero' => $numero,
            'client_id' => $client->id,
            'statut' => 'brouillon',
            'date_creation' => now(),
            'date_echeance' => now()->addDays(30),
        ]);
    }

    public function test_user_only_sees_their_own_documents_in_the_list(): void
    {
        $marie = User::factory()->create();
        $paul = User::factory()->create();

        $this->createDocumentFor($marie, 'DEV-2026-001');
        $this->createDocumentFor($paul, 'DEV-2026-002');

        $response = $this->actingAs($marie)->get(route('documents.index'));

        $response->assertSee('DEV-2026-001');
        $response->assertDontSee('DEV-2026-002');
    }

    public function test_user_cannot_open_another_users_document(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulDocument = $this->createDocumentFor($paul, 'DEV-2026-002');

    $this->actingAs($marie)
        ->get(route('documents.show', $paulDocument))
        ->assertNotFound();
}

public function test_user_cannot_open_the_edit_form_of_another_users_document(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulDocument = $this->createDocumentFor($paul, 'DEV-2026-002');

    $this->actingAs($marie)
        ->get(route('documents.edit', $paulDocument))
        ->assertNotFound();
}

public function test_user_cannot_update_another_users_document(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulDocument = $this->createDocumentFor($paul, 'DEV-2026-002');

    $this->actingAs($marie)
        ->put(route('documents.update', $paulDocument), ['statut' => 'envoye'])
        ->assertNotFound();

    $this->assertDatabaseHas('documents', ['id' => $paulDocument->id, 'statut' => 'brouillon']);
}

public function test_user_cannot_delete_another_users_document(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulDocument = $this->createDocumentFor($paul, 'DEV-2026-002');

    $this->actingAs($marie)
        ->delete(route('documents.destroy', $paulDocument))
        ->assertNotFound();

    $this->assertDatabaseHas('documents', ['id' => $paulDocument->id]);
}
    public function test_created_document_belongs_to_the_logged_in_user(): void
{
    $marie = User::factory()->create();
    $marieClient = $marie->clients()->create(['nom' => 'Marie Client']);

    $this->actingAs($marie)->post(route('documents.store'), [
        'type' => 'devis',
        'client_id' => $marieClient->id,
        'lignes' => [
            [
                'designation' => 'Service A',
                'quantite' => 1,
                'prix_unitaire' => 100,
                'taux_tva' => 20,
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

    $paulQuote = $this->createDocumentFor($paul, 'DEV-2026-002');
    $paulQuote->update(['statut' => 'accepte']);

    $this->actingAs($marie)
        ->post(route('documents.convert-to-invoice', $paulQuote))
        ->assertNotFound();

    $this->assertDatabaseMissing('documents', ['type' => 'facture']);
}

public function test_user_cannot_download_another_users_pdf(): void
{
    $marie = User::factory()->create();
    $paul = User::factory()->create();

    $paulDocument = $this->createDocumentFor($paul, 'DEV-2026-002');

    $this->actingAs($marie)
        ->get(route('documents.pdf', $paulDocument))
        ->assertNotFound();
}


}
