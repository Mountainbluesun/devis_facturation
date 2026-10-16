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
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'address' => '1 rue de Test, 69000 Lyon',
            'tax_id' => '12345678901234',
        ]);

        $attributes = [
            'type' => 'invoice',
            'number' => 'INV-2026-001',
            'client_id' => $client->id,
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
        ];

        Document::create($attributes);

        $this->expectException(QueryException::class);

        Document::create($attributes);
    }
}
