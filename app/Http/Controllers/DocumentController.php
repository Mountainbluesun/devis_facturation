<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Client;
use App\Services\DocumentNumberGenerator;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with('client')->latest()->paginate(15);
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        return view('documents.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:devis,facture',
            'client_id' => 'required|exists:clients,id',
            'date_echeance' => 'nullable|date',
            'lignes' => 'required|array|min:1',
            'lignes.*.designation' => 'required|string|max:255',
            'lignes.*.quantite' => 'required|numeric|min:0.01',
            'lignes.*.prix_unitaire' => 'required|numeric|min:0',
            'lignes.*.taux_tva' => 'required|numeric|min:0|max:100',
        ]);

        $generator = new DocumentNumberGenerator();

        $document = Document::create([
            'type' => $validated['type'],
            'numero' => $generator->generate($validated['type']),
            'client_id' => $validated['client_id'],
            'statut' => 'brouillon',
            'date_creation' => now(),
            'date_echeance' => $validated['date_echeance'] ?? null,
        ]);

        foreach ($validated['lignes'] as $ligne) {
            $document->lines()->create($ligne);
        }

        return redirect()->route('documents.show', $document)->with('success', 'Document créé avec succès.');
    }

    public function show(Document $document)
    {
        $document->load('client', 'lines', 'payments');
        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        if ($document->statut === 'paye') {
            return redirect()->route('documents.show', $document)
                ->with('error', 'Un document payé ne peut plus être modifié.');
        }

        $clients = Client::orderBy('nom')->get();
        $document->load('lines');
        return view('documents.edit', compact('document', 'clients'));
    }

    public function update(Request $request, Document $document)
    {
        if ($document->statut === 'paye') {
            return redirect()->route('documents.show', $document)
                ->with('error', 'Un document payé ne peut plus être modifié.');
        }

        $validated = $request->validate([
            'statut' => 'required|in:brouillon,envoye,accepte,refuse,paye,en_retard',
            'date_echeance' => 'nullable|date',
        ]);

        $document->update($validated);

        return redirect()->route('documents.show', $document)->with('success', 'Document mis à jour.');
    }

    public function destroy(Document $document)
    {
        if ($document->statut !== 'brouillon') {
            return redirect()->route('documents.index')
                ->with('error', 'Seul un brouillon peut être supprimé.');
        }

        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document supprimé.');
    }

    // Action personnalisée, pas dans le CRUD standard
    public function convertToInvoice(Document $document)
    {
        try {
            $facture = $document->convertToInvoice();
            return redirect()->route('documents.show', $facture)
                ->with('success', 'Facture générée avec succès.');
        } catch (\LogicException $e) {
            return redirect()->route('documents.show', $document)
                ->with('error', $e->getMessage());
        }
    }



public function downloadPdf(Document $document)
{
    $document->load('client', 'lines');

    $pdf = Pdf::loadView('documents.pdf', compact('document'));

    return $pdf->download($document->numero . '.pdf');
}
}
