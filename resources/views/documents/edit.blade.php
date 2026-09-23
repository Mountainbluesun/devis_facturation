@extends('layouts.app')

@section('title', 'Edit ' . $document->numero)

@section('content')

    <h1 class="text-2xl font-semibold mb-6">Edit {{ $document->numero }}</h1>

    <form action="{{ route('documents.update', $document) }}" method="POST" class="bg-white rounded-lg border border-gray-200 p-6 space-y-4 max-w-lg">
        @csrf
        @method('PUT')

        <div>
            <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="statut" id="statut" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                @foreach (['brouillon' => 'Draft', 'envoye' => 'Sent', 'accepte' => 'Accepted', 'refuse' => 'Refused', 'paye' => 'Paid', 'en_retard' => 'Overdue'] as $value => $label)
                    <option value="{{ $value }}" {{ old('statut', $document->statut) == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('statut')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="date_echeance" class="block text-sm font-medium text-gray-700 mb-1">Due date</label>
            <input type="date" name="date_echeance" id="date_echeance"
                   value="{{ old('date_echeance', $document->date_echeance?->format('Y-m-d')) }}"
                   class="rounded-md border-gray-300 shadow-sm text-sm">
            @error('date_echeance')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                Save
            </button>
            <a href="{{ route('documents.show', $document) }}"
               class="text-gray-600 px-4 py-2 rounded-md text-sm hover:bg-gray-100">
                Cancel
            </a>
        </div>

    </form>

@endsection
