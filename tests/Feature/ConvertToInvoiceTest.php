<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use LogicException;
use Tests\TestCase;
use App\Models\User;

class ConvertToInvoiceTest extends TestCase
{
    use RefreshDatabase;

    private function createQuote(string $status, ?User $user = null): Document
{
    $user ??= User::factory()->create();

    $client = $user->clients()->create([
        'nom' => 'Test Client',
        'email' => 'client@example.com',
        'adresse' => '1 rue de Test, 69000 Lyon',
        'siret' => '12345678901234',
    ]);

    $quote = $user->documents()->create([
        'type' => 'devis',
        'numero' => 'DEV-2026-001',
        'client_id' => $client->id,
        'statut' => $status,
        'date_creation' => now(),
        'date_echeance' => now()->addDays(30),
    ]);

    $quote->lines()->create([
        'designation' => 'Service A',
        'quantite' => 2,
        'prix_unitaire' => 100,
        'taux_tva' => 20,
    ]);

    return $quote;
}

    public function test_accepted_quote_is_converted_to_a_draft_invoice(): void
    {
        Carbon::setTestNow('2026-05-10');

        $quote = $this->createQuote('accepte');

        $invoice = $quote->convertToInvoice();

        $this->assertSame('facture', $invoice->type);
        $this->assertSame('FACT-2026-001', $invoice->numero);
        $this->assertSame('brouillon', $invoice->statut);
        $this->assertSame($quote->client_id, $invoice->client_id);
        $this->assertCount(1, $invoice->lines);
        $this->assertEquals(200, $invoice->total_ht);
    }

    public function test_quote_that_is_not_accepted_cannot_be_converted(): void
    {
        $quote = $this->createQuote('brouillon');

        $this->expectException(LogicException::class);

        $quote->convertToInvoice();
    }

    public function test_converted_invoice_belongs_to_the_same_user(): void
{
    $user = User::factory()->create();
    $quote = $this->createQuote('accepte', $user);

    $invoice = $quote->convertToInvoice();

    $this->assertSame($user->id, $invoice->user_id);
}
}
