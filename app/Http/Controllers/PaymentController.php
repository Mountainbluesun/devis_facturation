<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Document $document)
    {
        $validated = $request->validate([
            'montant' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'moyen_paiement' => 'nullable|string|max:255',
        ]);

        try {
            $document->enregistrerPaiement(
                $validated['montant'],
                $validated['date'],
                $validated['moyen_paiement'] ?? null
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

        // Si on annule un paiement, le document ne peut plus être "payé"
        if ($document->statut === 'paye') {
            $document->update(['statut' => 'envoye']);
        }

        return redirect()->route('documents.show', $document)
            ->with('success', 'Payment deleted.');
    }
}
