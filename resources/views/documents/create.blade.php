@extends('layouts.app')

@section('title', 'Nouveau document')

@section('content')

    <h1 class="text-2xl font-semibold mb-6">Nouveau document</h1>

    <form action="{{ route('documents.store') }}" method="POST" class="bg-white rounded-lg border border-gray-200 p-6 space-y-6 max-w-2xl">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" id="type" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="devis" {{ old('type') == 'devis' ? 'selected' : '' }}>Devis</option>
                    <option value="facture" {{ old('type') == 'facture' ? 'selected' : '' }}>Facture</option>
                </select>
                @error('type')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                <select name="client_id" id="client_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">— Sélectionner —</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->nom }}
                        </option>
                    @endforeach
                </select>
                @error('client_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="date_echeance" class="block text-sm font-medium text-gray-700 mb-1">Date d'échéance</label>
            <input type="date" name="date_echeance" id="date_echeance" value="{{ old('date_echeance') }}"
                   class="rounded-md border-gray-300 shadow-sm text-sm">
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Lignes</label>
                <button type="button" onclick="ajouterLigne()"
                        class="text-sm text-blue-600 hover:underline">+ Ajouter une ligne</button>
            </div>

            <div id="lignes-container" class="space-y-2">
                {{-- Les lignes sont injectées ici en JS --}}
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                Créer le document
            </button>
            <a href="{{ route('documents.index') }}"
               class="text-gray-600 px-4 py-2 rounded-md text-sm hover:bg-gray-100">
                Annuler
            </a>
        </div>

    </form>

@endsection

@push('scripts')
<script>
    let ligneIndex = 0;

    function ajouterLigne() {
        const container = document.getElementById('lignes-container');
        const div = document.createElement('div');
        div.className = 'grid grid-cols-12 gap-2 items-center';
        div.innerHTML = `
            <input type="text" name="lignes[${ligneIndex}][designation]" placeholder="Désignation"
                   class="col-span-5 rounded-md border-gray-300 shadow-sm text-sm">
            <input type="number" name="lignes[${ligneIndex}][quantite]" placeholder="Qté" step="0.01" value="1"
                   class="col-span-2 rounded-md border-gray-300 shadow-sm text-sm">
            <input type="number" name="lignes[${ligneIndex}][prix_unitaire]" placeholder="Prix unit." step="0.01"
                   class="col-span-2 rounded-md border-gray-300 shadow-sm text-sm">
            <input type="number" name="lignes[${ligneIndex}][taux_tva]" placeholder="TVA %" step="0.01" value="20"
                   class="col-span-2 rounded-md border-gray-300 shadow-sm text-sm">
            <button type="button" onclick="this.parentElement.remove()"
                    class="col-span-1 text-red-600 text-sm hover:underline">✕</button>
        `;
        container.appendChild(div);
        ligneIndex++;
    }

    // Ajoute une première ligne au chargement de la page
    document.addEventListener('DOMContentLoaded', ajouterLigne);
</script>
@endpush
