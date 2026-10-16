<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentDeletionTest extends TestCase
{
    use RefreshDatabase;

    private function createDocument(User $user, string $type): Document
    {
        $client = $user->clients()->create([
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'address' => '1 rue de Test, 69000 Lyon',
            'tax_id' => '12345678901234',
        ]);

        return $user->documents()->create([
            'type' => $type,
            'number' => $type === 'invoice' ? 'INV-2026-001' : 'QUO-2026-001',
            'client_id' => $client->id,
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
        ]);
    }

    public function test_an_invoice_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $invoice = $this->createDocument($user, 'invoice');

        $response = $this->actingAs($user)
            ->delete(route('documents.destroy', $invoice));

        $response->assertRedirect(route('documents.index'));
        $this->assertDatabaseHas('documents', ['id' => $invoice->id]);
    }

    public function test_a_draft_quote_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $quote = $this->createDocument($user, 'quote');

        $this->actingAs($user)
            ->delete(route('documents.destroy', $quote));

        $this->assertDatabaseMissing('documents', ['id' => $quote->id]);
    }
}
