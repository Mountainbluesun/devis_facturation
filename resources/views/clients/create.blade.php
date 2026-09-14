@extends('layouts.app')

@section('title', 'Nouveau client')

@section('content')

    <h1 class="text-2xl font-semibold mb-6">Nouveau client</h1>

    <form action="{{ route('clients.store') }}" method="POST" class="bg-white rounded-lg border border-gray-200 p-6 space-y-4 max-w-lg">
        @csrf

        <div>
            <label for="nom" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
            <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            @error('nom')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            @error('email')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="adresse" class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
            <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            @error('adresse')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="siret" class="block text-sm font-medium text-gray-700 mb-1">SIRET</label>
            <input type="text" name="siret" id="siret" value="{{ old('siret') }}"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            @error('siret')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                Créer le client
            </button>
            <a href="{{ route('clients.index') }}"
               class="text-gray-600 px-4 py-2 rounded-md text-sm hover:bg-gray-100">
                Annuler
            </a>
        </div>

    </form>

@endsection
