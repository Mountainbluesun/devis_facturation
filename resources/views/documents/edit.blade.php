@extends('layouts.app')

@section('title', 'Edit ' . $document->number)

@section('content')

    <h1 class="text-2xl font-semibold mb-6">Edit {{ $document->number }}</h1>

    <form action="{{ route('documents.update', $document) }}" method="POST" class="bg-white rounded-lg border border-gray-200 p-6 space-y-4 max-w-lg">
        @csrf
        @method('PUT')

        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                @foreach (['draft' => 'Draft', 'sent' => 'Sent', 'accepted' => 'Accepted', 'rejected' => 'Refused', 'paid' => 'Paid', 'overdue' => 'Overdue'] as $value => $label)
                    <option value="{{ $value }}" {{ old('status', $document->status) == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due date</label>
            <input type="date" name="due_date" id="due_date"
                   value="{{ old('due_date', $document->due_date?->format('Y-m-d')) }}"
                   class="rounded-md border-gray-300 shadow-sm text-sm">
            @error('due_date')
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
