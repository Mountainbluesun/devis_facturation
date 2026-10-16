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
        'name' => 'Test Client',
        'email' => 'client@example.com',
        'address' => '1 rue de Test, 69000 Lyon',
        'tax_id' => '12345678901234',
    ]);

    $quote = $user->documents()->create([
        'type' => 'quote',
        'number' => 'QUO-2026-001',
        'client_id' => $client->id,
        'status' => $status,
        'issue_date' => now(),
        'due_date' => now()->addDays(30),
    ]);

    $quote->lines()->create([
        'description' => 'Service A',
        'quantity' => 2,
        'unit_price' => 100,
        'vat_rate' => 20,
    ]);

    return $quote;
}

    public function test_accepted_quote_is_converted_to_a_draft_invoice(): void
    {
        Carbon::setTestNow('2026-05-10');

        $quote = $this->createQuote('accepted');

        $invoice = $quote->convertToInvoice();

        $this->assertSame('invoice', $invoice->type);
        $this->assertSame('INV-2026-001', $invoice->number);
        $this->assertSame('draft', $invoice->status);
        $this->assertSame($quote->client_id, $invoice->client_id);
        $this->assertCount(1, $invoice->lines);
        $this->assertEquals(200, $invoice->subtotal);
    }

    public function test_quote_that_is_not_accepted_cannot_be_converted(): void
    {
        $quote = $this->createQuote('draft');

        $this->expectException(LogicException::class);

        $quote->convertToInvoice();
    }

    public function test_converted_invoice_belongs_to_the_same_user(): void
{
    $user = User::factory()->create();
    $quote = $this->createQuote('accepted', $user);

    $invoice = $quote->convertToInvoice();

    $this->assertSame($user->id, $invoice->user_id);
}
}
