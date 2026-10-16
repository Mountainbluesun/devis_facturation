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

        $number = (new DocumentNumberGenerator())->generate('invoice');

        $this->assertSame('INV-2026-001', $number);
    }

    public function test_quote_numbers_use_the_dev_prefix(): void
    {
        Carbon::setTestNow('2026-05-10');

        $number = (new DocumentNumberGenerator())->generate('quote');

        $this->assertSame('QUO-2026-001', $number);
    }

    public function test_next_invoice_number_follows_the_last_one(): void
{
    Carbon::setTestNow('2026-05-10');

    $client = Client::create([
        'name' => 'Test Client',
        'email' => 'client@example.com',
        'address' => '1 rue de Test, 69000 Lyon',
        'tax_id' => '12345678901234',
    ]);

    // An invoice number 007 already exists in the database
    Document::create([
        'type' => 'invoice',
        'number' => 'INV-2026-007',
        'client_id' => $client->id,
        'status' => 'draft',
        'issue_date' => now(),
        'due_date' => now()->addDays(30),
    ]);

    $number = (new DocumentNumberGenerator())->generate('invoice');

    $this->assertSame('INV-2026-008', $number);
}
    public function test_numbering_continues_after_999(): void
{
    Carbon::setTestNow('2026-05-10');

    $client = Client::create([
        'name' => 'Test Client',
        'email' => 'client@example.com',
        'address' => '1 rue de Test, 69000 Lyon',
        'tax_id' => '12345678901234',
    ]);

    foreach (['INV-2026-999', 'INV-2026-1000'] as $number) {
        Document::create([
            'type' => 'invoice',
            'number' => $number,
            'client_id' => $client->id,
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
        ]);
    }

    $number = (new DocumentNumberGenerator())->generate('invoice');

    $this->assertSame('INV-2026-1001', $number);
}

}
