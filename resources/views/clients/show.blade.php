@extends('layouts.app')

@section('title', $client->nom)

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold">{{ $client->nom }}</h1>
            <p class="text-sm text-gray-500">{{ $client->email ?? 'No email' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('clients.edit', $client) }}"
               class="text-sm text-blue-600 hover:underline">Edit</a>
            <form action="{{ route('clients.destroy', $client) }}" method="POST"
                  onsubmit="return confirm('Delete this client?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6 text-sm">
        <p><span class="text-gray-500">Address:</span> {{ $client->adresse ?? '—' }}</p>
        <p><span class="text-gray-500">Business ID (SIRET):</span> {{ $client->siret ?? '—' }}</p>
    </div>

    <h2 class="text-lg font-medium mb-3">Documents</h2>

    <div class="bg-white rounded-lg border border-gray-200 divide-y divide-gray-100">
        @forelse ($client->documents as $document)
            <a href="{{ route('documents.show', $document) }}"
               class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                <div>
                    <p class="font-medium">{{ $document->numero }}</p>
                    <p class="text-sm text-gray-500">{{ ucfirst($document->type) }} — {{ ucfirst($document->statut) }}</p>
                </div>
                <span class="text-sm text-gray-700">{{ number_format($document->total_ttc, 2) }} €</span>
            </a>
        @empty
            <p class="px-4 py-6 text-center text-gray-500">No documents for this client.</p>
        @endforelse
    </div>

@endsection
