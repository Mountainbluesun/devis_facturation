<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentDeletionTest extends TestCase
{
    use RefreshDatabase;

    private function createDocument(string $type): Document
    {
        $client = Client::create([
            'nom' => 'Test Client',
            'email' => 'client@example.com',
            'adresse' => '1 rue de Test, 69000 Lyon',
            'siret' => '12345678901234',
        ]);

        return Document::create([
            'type' => $type,
            'numero' => $type === 'facture' ? 'FACT-2026-001' : 'DEV-2026-001',
            'client_id' => $client->id,
            'statut' => 'brouillon',
            'date_creation' => now(),
            'date_echeance' => now()->addDays(30),
        ]);
    }

    public function test_an_invoice_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createDocument('facture');

        $response = $this->actingAs($user)
            ->delete(route('documents.destroy', $invoice));

        $response->assertRedirect(route('documents.index'));
        $this->assertDatabaseHas('documents', ['id' => $invoice->id]);
    }

    public function test_a_draft_quote_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $quote = $this->createDocument('devis');

        $this->actingAs($user)
            ->delete(route('documents.destroy', $quote));

        $this->assertDatabaseMissing('documents', ['id' => $quote->id]);
    }
}
