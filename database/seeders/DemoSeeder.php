<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $generator = new DocumentNumberGenerator();

        // All demo data belongs to this user
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
            ]
        );

        // Client 1 - quote awaiting an answer
        $client1 = $user->clients()->create([
            'name' => 'Dubois Carpentry',
            'email' => 'contact@dubois-carpentry.example',
            'address' => '12 Craftsmen Street, 69000 Lyon',
            'tax_id' => '12345678900012',
        ]);

        $quote1 = $user->documents()->create([
            'type' => 'quote',
            'number' => $generator->generate('quote'),
            'client_id' => $client1->id,
            'status' => 'sent',
            'issue_date' => now()->subDays(5),
            'due_date' => now()->addDays(25),
        ]);
        $quote1->lines()->create(['description' => 'Custom door manufacturing', 'quantity' => 1, 'unit_price' => 850, 'vat_rate' => 20]);
        $quote1->lines()->create(['description' => 'Installation and finishing', 'quantity' => 4, 'unit_price' => 45, 'vat_rate' => 20]);

        // Client 2 - fully paid invoice
        $client2 = $user->clients()->create([
            'name' => 'Creative Workshop Ltd',
            'email' => 'accounts@creative-workshop.example',
            'address' => '8 Lumiere Avenue, 69008 Lyon',
            'tax_id' => '98765432100019',
        ]);

        $invoice2 = $user->documents()->create([
            'type' => 'invoice',
            'number' => $generator->generate('invoice'),
            'client_id' => $client2->id,
            'status' => 'draft',
            'issue_date' => now()->subDays(20),
            'due_date' => now()->subDays(10),
        ]);
        $invoice2->lines()->create(['description' => 'Consulting service - 2 days', 'quantity' => 2, 'unit_price' => 600, 'vat_rate' => 20]);
        $invoice2->recordPayment($invoice2->total, now()->subDays(8)->format('Y-m-d'), 'bank transfer');

        // Client 3 - partially paid invoice
        $client3 = $user->clients()->create([
            'name' => 'Martin Bakery',
            'email' => 'martin.bakery@example.com',
            'address' => '3 Market Square, 69003 Lyon',
        ]);

        $invoice3 = $user->documents()->create([
            'type' => 'invoice',
            'number' => $generator->generate('invoice'),
            'client_id' => $client3->id,
            'status' => 'draft',
            'issue_date' => now()->subDays(10),
            'due_date' => now()->addDays(20),
        ]);
        $invoice3->lines()->create(['description' => 'Professional oven repair', 'quantity' => 1, 'unit_price' => 1200, 'vat_rate' => 20]);
        $invoice3->recordPayment(600, now()->subDays(3)->format('Y-m-d'), 'check');
    }
}
