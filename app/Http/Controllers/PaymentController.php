<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Document $document)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
        ]);

        try {
            $document->recordPayment(
                $validated['amount'],
                $validated['date'],
                $validated['payment_method'] ?? null
            );

            return redirect()->route('documents.show', $document)
                ->with('success', 'Payment recorded.');
        } catch (\LogicException $e) {
            return redirect()->route('documents.show', $document)
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Document $document, \App\Models\Payment $payment)
    {
        $payment->delete();

        // If a payment is cancelled, the document can no longer be "paid"
        if ($document->status === 'paid') {
            $document->update(['status' => 'sent']);
        }

        return redirect()->route('documents.show', $document)
            ->with('success', 'Payment deleted.');
    }
}
