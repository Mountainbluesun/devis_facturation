@extends('layouts.app')

@section('title', 'New document')

@section('content')

    <h1 class="text-2xl font-semibold mb-6">New document</h1>

    <form action="{{ route('documents.store') }}" method="POST" class="bg-white rounded-lg border border-gray-200 p-6 space-y-6 max-w-2xl">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" id="type" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="quote" {{ old('type') == 'quote' ? 'selected' : '' }}>Quote</option>
                    <option value="invoice" {{ old('type') == 'invoice' ? 'selected' : '' }}>Invoice</option>
                </select>
                @error('type')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                <select name="client_id" id="client_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">— Select —</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
                @error('client_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due date</label>
            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}"
                   class="rounded-md border-gray-300 shadow-sm text-sm">
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700">Lines</label>
                <button type="button" onclick="ajouterLigne()"
                        class="text-sm text-blue-600 hover:underline">+ Add a line</button>
            </div>

            <div id="lines-container" class="space-y-2">
                {{-- Lines are injected here via JS --}}
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                Create document
            </button>
            <a href="{{ route('documents.index') }}"
               class="text-gray-600 px-4 py-2 rounded-md text-sm hover:bg-gray-100">
                Cancel
            </a>
        </div>

    </form>

@endsection

@push('scripts')
<script>
    let ligneIndex = 0;

    function ajouterLigne() {
        const container = document.getElementById('lines-container');
        const div = document.createElement('div');
        div.className = 'grid grid-cols-12 gap-2 items-center';
        div.innerHTML = `
            <input type="text" name="lines[${ligneIndex}][description]" placeholder="Description"
                   class="col-span-5 rounded-md border-gray-300 shadow-sm text-sm">
            <input type="number" name="lines[${ligneIndex}][quantity]" placeholder="Qty" step="0.01" value="1"
                   class="col-span-2 rounded-md border-gray-300 shadow-sm text-sm">
            <input type="number" name="lines[${ligneIndex}][unit_price]" placeholder="Unit price" step="0.01"
                   class="col-span-2 rounded-md border-gray-300 shadow-sm text-sm">
            <input type="number" name="lines[${ligneIndex}][vat_rate]" placeholder="VAT %" step="0.01" value="20"
                   class="col-span-2 rounded-md border-gray-300 shadow-sm text-sm">
            <button type="button" onclick="this.parentElement.remove()"
                    class="col-span-1 text-red-600 text-sm hover:underline">✕</button>
        `;
        container.appendChild(div);
        ligneIndex++;
    }

    document.addEventListener('DOMContentLoaded', ajouterLigne);
</script>
@endpush
