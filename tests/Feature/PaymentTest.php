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
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'address' => '1 rue de Test, 69000 Lyon',
            'tax_id' => '12345678901234',
        ]);

        $document = Document::create([
            'type' => $type,
            'number' => $type === 'invoice' ? 'INV-2026-001' : 'QUO-2026-001',
            'client_id' => $client->id,
            'status' => 'sent',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
        ]);

        $document->lines()->create([
            'description' => 'Service A',
            'quantity' => 2,
            'unit_price' => 100,
            'vat_rate' => 20,
        ]);

        return $document;
    }

    public function test_partial_payment_does_not_mark_the_invoice_as_paid(): void
    {
        $invoice = $this->createDocument('invoice');

        $invoice->recordPayment(100, '2026-05-10', 'bank transfer');

        $invoice->refresh();

        $this->assertSame('sent', $invoice->status);
        $this->assertEquals(140, $invoice->balance);
    }

    public function test_full_payment_marks_the_invoice_as_paid(): void
    {
        $invoice = $this->createDocument('invoice');

        $invoice->recordPayment(100, '2026-05-10', 'bank transfer');
        $invoice->recordPayment(140, '2026-05-20', 'check');

        $invoice->refresh();

        $this->assertSame('paid', $invoice->status);
        $this->assertEquals(0, $invoice->balance);
    }

    public function test_a_quote_cannot_receive_a_payment(): void
    {
        $quote = $this->createDocument('quote');

        $this->expectException(LogicException::class);

        $quote->recordPayment(100, '2026-05-10');
    }
}
