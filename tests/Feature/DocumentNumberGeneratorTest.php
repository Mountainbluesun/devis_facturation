<?php

namespace Tests\Feature;

use App\Services\DocumentNumberGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Models\Client;
use App\Models\Document;

class DocumentNumberGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_invoice_number_of_the_year_is_001(): void
    {
        Carbon::setTestNow('2026-05-10');

        $number = (new DocumentNumberGenerator())->generate('facture');

        $this->assertSame('FACT-2026-001', $number);
    }

    public function test_quote_numbers_use_the_dev_prefix(): void
    {
        Carbon::setTestNow('2026-05-10');

        $number = (new DocumentNumberGenerator())->generate('devis');

        $this->assertSame('DEV-2026-001', $number);
    }

    public function test_next_invoice_number_follows_the_last_one(): void
{
    Carbon::setTestNow('2026-05-10');

    $client = Client::create([
        'nom' => 'Test Client',
        'email' => 'client@example.com',
        'adresse' => '1 rue de Test, 69000 Lyon',
        'siret' => '12345678901234',
    ]);

    // An invoice number 007 already exists in the database
    Document::create([
        'type' => 'facture',
        'numero' => 'FACT-2026-007',
        'client_id' => $client->id,
        'statut' => 'brouillon',
        'date_creation' => now(),
        'date_echeance' => now()->addDays(30),
    ]);

    $number = (new DocumentNumberGenerator())->generate('facture');

    $this->assertSame('FACT-2026-008', $number);
}
    public function test_numbering_continues_after_999(): void
{
    Carbon::setTestNow('2026-05-10');

    $client = Client::create([
        'nom' => 'Test Client',
        'email' => 'client@example.com',
        'adresse' => '1 rue de Test, 69000 Lyon',
        'siret' => '12345678901234',
    ]);

    foreach (['FACT-2026-999', 'FACT-2026-1000'] as $numero) {
        Document::create([
            'type' => 'facture',
            'numero' => $numero,
            'client_id' => $client->id,
            'statut' => 'brouillon',
            'date_creation' => now(),
            'date_echeance' => now()->addDays(30),
        ]);
    }

    $number = (new DocumentNumberGenerator())->generate('facture');

    $this->assertSame('FACT-2026-1001', $number);
}

}
