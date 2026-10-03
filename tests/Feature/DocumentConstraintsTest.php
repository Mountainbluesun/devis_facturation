<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Document;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentConstraintsTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_documents_cannot_share_the_same_number(): void
    {
        $client = Client::create([
            'nom' => 'Test Client',
            'email' => 'client@example.com',
            'adresse' => '1 rue de Test, 69000 Lyon',
            'siret' => '12345678901234',
        ]);

        $attributes = [
            'type' => 'facture',
            'numero' => 'FACT-2026-001',
            'client_id' => $client->id,
            'statut' => 'brouillon',
            'date_creation' => now(),
            'date_echeance' => now()->addDays(30),
        ];

        Document::create($attributes);

        $this->expectException(QueryException::class);

        Document::create($attributes);
    }
}
