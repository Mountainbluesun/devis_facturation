@extends('layouts.app')

@section('title', 'New client')

@section('content')

    <h1 class="text-2xl font-semibold mb-6">New client</h1>

    <form action="{{ route('clients.store') }}" method="POST" class="bg-white rounded-lg border border-gray-200 p-6 space-y-4 max-w-lg">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            @error('name')
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
            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <input type="text" name="address" id="address" value="{{ old('address') }}"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            @error('address')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">Business ID (SIRET)</label>
            <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}"
                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            @error('tax_id')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                Create client
            </button>
            <a href="{{ route('clients.index') }}"
               class="text-gray-600 px-4 py-2 rounded-md text-sm hover:bg-gray-100">
                Cancel
            </a>
        </div>

    </form>

@endsection
