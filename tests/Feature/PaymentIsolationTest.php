<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function createInvoiceFor(User $user, string $number): Document
    {
        $client = $user->clients()->create(['name' => 'Client of user ' . $user->id]);

        $invoice = $user->documents()->create([
            'type' => 'invoice',
            'number' => $number,
            'client_id' => $client->id,
            'status' => 'sent',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
        ]);

        $invoice->lines()->create([
            'description' => 'Service A',
            'quantity' => 1,
            'unit_price' => 100,
            'vat_rate' => 20,
        ]);

        return $invoice;
    }

    public function test_user_cannot_record_a_payment_on_another_users_invoice(): void
    {
        $marie = User::factory()->create();
        $paul = User::factory()->create();

        $paulInvoice = $this->createInvoiceFor($paul, 'INV-2026-002');

        $this->actingAs($marie)
            ->post(route('documents.payments.store', $paulInvoice), [
                'amount' => 50,
                'date' => '2026-05-10',
            ])
            ->assertNotFound();

        $this->assertSame(0, Payment::count());
    }

    public function test_user_cannot_delete_a_payment_on_another_users_invoice(): void
    {
        $marie = User::factory()->create();
        $paul = User::factory()->create();

        $paulInvoice = $this->createInvoiceFor($paul, 'INV-2026-002');
        $paulPayment = $paulInvoice->payments()->create([
            'amount' => 50,
            'date' => '2026-05-10',
            'payment_method' => 'bank transfer',
        ]);

        $this->actingAs($marie)
            ->delete(route('documents.payments.destroy', [$paulInvoice, $paulPayment]))
            ->assertNotFound();

        $this->assertNotNull(Payment::find($paulPayment->id));
    }

    public function test_payment_must_belong_to_the_invoice_in_the_url(): void
    {
        $marie = User::factory()->create();
        $paul = User::factory()->create();

        $marieInvoice = $this->createInvoiceFor($marie, 'INV-2026-001');
        $paulInvoice = $this->createInvoiceFor($paul, 'INV-2026-002');
        $paulPayment = $paulInvoice->payments()->create([
            'amount' => 50,
            'date' => '2026-05-10',
            'payment_method' => 'bank transfer',
        ]);

        // Marie's own invoice in the URL, but Paul's payment id
        $this->actingAs($marie)
            ->delete(route('documents.payments.destroy', [$marieInvoice, $paulPayment]))
            ->assertNotFound();

        $this->assertNotNull(Payment::find($paulPayment->id));
    }
}
