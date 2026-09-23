@extends('layouts.app')

@section('title', 'Clients')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Clients</h1>
        <a href="{{ route('clients.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
            + New client
        </a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 divide-y divide-gray-100">
        @forelse ($clients as $client)
            <a href="{{ route('clients.show', $client) }}"
               class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                <div>
                    <p class="font-medium">{{ $client->nom }}</p>
                    <p class="text-sm text-gray-500">{{ $client->email ?? 'No email' }}</p>
                </div>
                <span class="text-gray-400 text-sm">{{ $client->documents_count ?? '' }}</span>
            </a>
        @empty
            <p class="px-4 py-6 text-center text-gray-500">No clients yet.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $clients->links() }}
    </div>

@endsection
