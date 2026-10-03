<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    // Creates a document with one line: 2 x 100 at 20% VAT = 240 incl. tax
    private function createDocument(string $type): Document
    {
        $client = Client::create([
            'nom' => 'Test Client',
            'email' => 'client@example.com',
            'adresse' => '1 rue de Test, 69000 Lyon',
            'siret' => '12345678901234',
        ]);

        $document = Document::create([
            'type' => $type,
            'numero' => $type === 'facture' ? 'FACT-2026-001' : 'DEV-2026-001',
            'client_id' => $client->id,
            'statut' => 'envoye',
            'date_creation' => now(),
            'date_echeance' => now()->addDays(30),
        ]);

        $document->lines()->create([
            'designation' => 'Service A',
            'quantite' => 2,
            'prix_unitaire' => 100,
            'taux_tva' => 20,
        ]);

        return $document;
    }

    public function test_partial_payment_does_not_mark_the_invoice_as_paid(): void
    {
        $invoice = $this->createDocument('facture');

        $invoice->enregistrerPaiement(100, '2026-05-10', 'virement');

        $invoice->refresh();

        $this->assertSame('envoye', $invoice->statut);
        $this->assertEquals(140, $invoice->solde);
    }

    public function test_full_payment_marks_the_invoice_as_paid(): void
    {
        $invoice = $this->createDocument('facture');

        $invoice->enregistrerPaiement(100, '2026-05-10', 'virement');
        $invoice->enregistrerPaiement(140, '2026-05-20', 'cheque');

        $invoice->refresh();

        $this->assertSame('paye', $invoice->statut);
        $this->assertEquals(0, $invoice->solde);
    }

    public function test_a_quote_cannot_receive_a_payment(): void
    {
        $quote = $this->createDocument('devis');

        $this->expectException(LogicException::class);

        $quote->enregistrerPaiement(100, '2026-05-10');
    }
}
