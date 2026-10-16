@extends('layouts.app')

@section('title', $document->number)

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold">{{ $document->number }}</h1>
            <p class="text-sm text-gray-500">
                {{ ucfirst($document->type) }} — {{ ucfirst($document->status) }} —
                <a href="{{ route('clients.show', $document->client) }}" class="text-blue-600 hover:underline">{{ $document->client->name }}</a>
            </p>
        </div>
        <div class="flex gap-2 items-center">
            @if ($document->type === 'quote' && $document->status === 'accepted')
                <form action="{{ route('documents.convert-to-invoice', $document) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm bg-green-600 text-white px-3 py-1.5 rounded-md hover:bg-green-700">
                        Convert to invoice
                    </button>
                </form>
            @endif
            <a href="{{ route('documents.pdf', $document) }}"
               class="text-sm text-gray-700 hover:underline">Download PDF</a>
            @if ($document->status !== 'paid')
                <a href="{{ route('documents.edit', $document) }}" class="text-sm text-blue-600 hover:underline">Edit</a>
            @endif
            @if ($document->status === 'draft')
                <form action="{{ route('documents.destroy', $document) }}" method="POST"
                      onsubmit="return confirm('Delete this document?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:underline">Delete</button>
                </form>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 divide-y divide-gray-100 mb-6">
        @foreach ($document->lines as $line)
            <div class="flex items-center justify-between px-4 py-3 text-sm">
                <span>{{ $line->description }} <span class="text-gray-400">× {{ $line->quantity }}</span></span>
                <span>{{ number_format($line->quantity * $line->unit_price, 2) }} € <span class="text-gray-400">(VAT {{ $line->vat_rate }}%)</span></span>
            </div>
        @endforeach
    </div>

    <h2 class="text-lg font-medium mb-3">Payments</h2>

    <div class="bg-white rounded-lg border border-gray-200 divide-y divide-gray-100 mb-6">
        @forelse ($document->payments as $payment)
            <div class="flex items-center justify-between px-4 py-3 text-sm">
                <span>{{ $payment->date->format('Y-m-d') }} — {{ $payment->payment_method ?? 'Not specified' }}</span>
                <div class="flex items-center gap-3">
                    <span class="font-medium">{{ number_format($payment->amount, 2) }} €</span>
                    <form action="{{ route('documents.payments.destroy', [$document, $payment]) }}" method="POST"
                          onsubmit="return confirm('Delete this payment?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="px-4 py-6 text-center text-gray-500 text-sm">No payments recorded.</p>
        @endforelse
    </div>

    @if ($document->type === 'invoice' && $document->status !== 'paid')
        <form action="{{ route('documents.payments.store', $document) }}" method="POST"
              class="bg-white rounded-lg border border-gray-200 p-4 mb-6 flex gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Amount</label>
                <input type="number" name="amount" step="0.01" required
                       class="rounded-md border-gray-300 shadow-sm text-sm w-28">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                       class="rounded-md border-gray-300 shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Method</label>
                <select name="payment_method" class="rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="bank transfer">Bank transfer</option>
                    <option value="check">Check</option>
                    <option value="especes">Cash</option>
                    <option value="cb">Card</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                Record
            </button>
        </form>
    @endif

    <div class="bg-white rounded-lg border border-gray-200 p-4 text-sm space-y-1 max-w-xs ml-auto">
        <div class="flex justify-between"><span class="text-gray-500">Subtotal (excl. VAT)</span><span>{{ number_format($document->subtotal, 2) }} €</span></div>
        <div class="flex justify-between"><span class="text-gray-500">VAT</span><span>{{ number_format($document->vat_amount, 2) }} €</span></div>
        <div class="flex justify-between font-semibold border-t border-gray-200 pt-1 mt-1"><span>Total (incl. VAT)</span><span>{{ number_format($document->total, 2) }} €</span></div>
        @if ($document->type === 'invoice')
            <div class="flex justify-between text-green-700"><span>Paid</span><span>{{ number_format($document->total_paid, 2) }} €</span></div>
            <div class="flex justify-between font-semibold {{ $document->balance > 0 ? 'text-red-600' : 'text-green-600' }} border-t border-gray-200 pt-1 mt-1">
                <span>Balance due</span><span>{{ number_format($document->balance, 2) }} €</span>
            </div>
        @endif
    </div>

@endsection
