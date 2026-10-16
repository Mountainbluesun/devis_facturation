<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Client;
use App\Services\DocumentNumberGenerator;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = auth()->user()->documents()->with('client')->latest()->paginate(15);
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        $clients = auth()->user()->clients()->orderBy('name')->get();
        return view('documents.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:quote,invoice',
            'client_id' => ['required', Rule::exists('clients', 'id')->where('user_id', auth()->id())],
            'due_date' => 'nullable|date',
            'lines' => 'required|array|min:1',
            'lines.*.description' => 'required|string|max:255',
            'lines.*.quantity' => 'required|numeric|min:0.01',
            'lines.*.unit_price' => 'required|numeric|min:0',
            'lines.*.vat_rate' => 'required|numeric|min:0|max:100',
        ]);

        $generator = new DocumentNumberGenerator();

        $document = auth()->user()->documents()->create([
            'type' => $validated['type'],
            'number' => $generator->generate($validated['type']),
            'client_id' => $validated['client_id'],
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => $validated['due_date'] ?? null,
        ]);

        foreach ($validated['lines'] as $line) {
            $document->lines()->create($line);
        }

        return redirect()->route('documents.show', $document)->with('success', 'Document created successfully.');
    }

    public function show(Document $document)
    {
        $this->ensureOwnership($document);
        $document->load('client', 'lines', 'payments');
        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $this->ensureOwnership($document);

        if ($document->status === 'paid') {
            return redirect()->route('documents.show', $document)
                ->with('error', 'A paid document can no longer be edited.');
        }

        $clients = Client::orderBy('name')->get();
        $document->load('lines');
        return view('documents.edit', compact('document', 'clients'));
    }

    public function update(Request $request, Document $document)
    {
        $this->ensureOwnership($document);

        if ($document->status === 'paid') {
            return redirect()->route('documents.show', $document)
                ->with('error', 'A paid document can no longer be edited.Only a draft can be deleted.');
        }

        $validated = $request->validate([
            'status' => 'required|in:draft,sent,accepted,rejected,paid,overdue',
            'due_date' => 'nullable|date',
        ]);

        $document->update($validated);

        return redirect()->route('documents.show', $document)->with('success', 'Document updated.');
    }

    public function destroy(Document $document)
    {
        $this->ensureOwnership($document);

        if ($document->type === 'invoice') {
           return redirect()->route('documents.index')
               ->with('error', 'An invoice cannot be deleted.');
}
        if ($document->status !== 'draft') {
            return redirect()->route('documents.index')
               ->with('error', 'Only a draft can be deleted.');
        }
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted.');
    }

    // Custom action, not part of standard CRUD
    public function convertToInvoice(Document $document)
    {
        $this->ensureOwnership($document);

        try {
            $invoice = $document->convertToInvoice();
            return redirect()->route('documents.show', $invoice)
                ->with('success', 'Invoice generated successfully.');
        } catch (\LogicException $e) {
            return redirect()->route('documents.show', $document)
                ->with('error', $e->getMessage());
        }
    }



public function downloadPdf(Document $document)
{
    $this->ensureOwnership($document);

    $document->load('client', 'lines');

    $pdf = Pdf::loadView('documents.pdf', compact('document'));

    return $pdf->download($document->number . '.pdf');
}

private function ensureOwnership(Document $document): void
{
    abort_unless($document->user_id == auth()->id(), 404);
}
}
