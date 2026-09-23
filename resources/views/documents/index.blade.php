@extends('layouts.app')

@section('title', 'Documents')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Documents</h1>
        <a href="{{ route('documents.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
            + New document
        </a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 divide-y divide-gray-100">
        @forelse ($documents as $document)
            <a href="{{ route('documents.show', $document) }}"
               class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                <div>
                    <p class="font-medium">{{ $document->numero }}</p>
                    <p class="text-sm text-gray-500">{{ $document->client->nom }} — {{ ucfirst($document->statut) }}</p>
                </div>
                <span class="text-sm text-gray-700">{{ number_format($document->total_ttc, 2) }} €</span>
            </a>
        @empty
            <p class="px-4 py-6 text-center text-gray-500">No documents yet.</p>
        @endforelse
