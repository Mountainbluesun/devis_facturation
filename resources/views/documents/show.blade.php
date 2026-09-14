@extends('layouts.app')

@section('title', $document->numero)

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold">{{ $document->numero }}</h1>
            <p class="text-sm text-gray-500">
                {{ ucfirst($document->type) }} — {{ ucfirst($document->statut) }} —
                <a href="{{ route('clients.show', $document->client) }}" class="text-blue-600 hover:underline">{{ $document->client->nom }}</a>
            </p>
        </div>
        <div class="flex gap-2 items-center">
            @if ($document->type === 'devis' && $document->statut === 'accepte')
                <form action="{{ route('documents.convert-to-invoice', $document) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm bg-green-600 text-white px-3 py-1.5 rounded-md hover:bg-green-700">
                        Convertir en facture
                    </button>
                </form>
            @endif
            <a href="{{ route('documents.pdf', $document) }}"
               class="text-sm text-gray-700 hover:underline">Télécharger PDF</a>
            @if ($document->statut !== 'paye')
                <a href="{{ route('documents.edit', $document) }}" class="text-sm text-blue-600 hover:underline">Modifier</a>
            @endif
            @if ($document->statut === 'brouillon')
                <form action="{{ route('documents.destroy', $document) }}" method="POST"
                      onsubmit="return confirm('Supprimer ce document ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:underline">Supprimer</button>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 divide-y divide-gray-100 mb-6">
        @foreach ($document->lines as $line)
            <div class="flex items-center justify-between px-4 py-3 text-sm">
                <span>{{ $line->designation }} <span class="text-gray-400">× {{ $line->quantite }}</span></span>
                <span>{{ number_format($line->quantite * $line->prix_unitaire, 2) }} € <span class="text-gray-400">(TVA {{ $line->taux_tva }}%)</span></span>
            </div>
        @endforeach
    </div>

    <h2 class="text-lg font-medium mb-3">Paiements</h2>

    <div class="bg-white rounded-lg border border-gray-200 divide-y divide-gray-100 mb-6">
        @forelse ($document->payments as $payment)
            <div class="flex items-center justify-between px-4 py-3 text-sm">
                <span>{{ $payment->date->format('d/m/Y') }} — {{ $payment->moyen_paiement ?? 'Non précisé' }}</span>
                <div class="flex items-center gap-3">
                    <span class="font-medium">{{ number_format($payment->montant, 2) }} €</span>
                    <form action="{{ route('documents.payments.destroy', [$document, $payment]) }}" method="POST"
                          onsubmit="return confirm('Supprimer ce paiement ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline text-xs">Supprimer</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="px-4 py-6 text-center text-gray-500 text-sm">Aucun paiement enregistré.</p>
        @endforelse
    </div>

    @if ($document->type === 'facture' && $document->statut !== 'paye')
        <form action="{{ route('documents.payments.store', $document) }}" method="POST"
              class="bg-white rounded-lg border border-gray-200 p-4 mb-6 flex gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Montant</label>
                <input type="number" name="montant" step="0.01" required
                       class="rounded-md border-gray-300 shadow-sm text-sm w-28">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                       class="rounded-md border-gray-300 shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Moyen</label>
                <select name="moyen_paiement" class="rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="virement">Virement</option>
                    <option value="cheque">Chèque</option>
                    <option value="especes">Espèces</option>
                    <option value="cb">Carte bancaire</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                Enregistrer
            </button>
        </form>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm space-y-1 max-w-xs ml-auto">
        <div class="flex justify-between"><span class="text-gray-500">Total HT</span><span>{{ number_format($document->total_ht, 2) }} €</span></div>
        <div class="flex justify-between"><span class="text-gray-500">TVA</span><span>{{ number_format($document->total_tva, 2) }} €</span></div>
        <div class="flex justify-between font-semibold border-t border-gray-200 pt-1 mt-1"><span>Total TTC</span><span>{{ number_format($document->total_ttc, 2) }} €</span></div>
        @if ($document->type === 'facture')
            <div class="flex justify-between text-green-700"><span>Payé</span><span>{{ number_format($document->total_paye, 2) }} €</span></div>
            <div class="flex justify-between font-semibold {{ $document->solde > 0 ? 'text-red-600' : 'text-green-600' }} border-t border-gray-200 pt-1 mt-1">
                <span>Solde</span><span>{{ number_format($document->solde, 2) }} €</span>
            </div>
        @endif
    </div>

@endsection
