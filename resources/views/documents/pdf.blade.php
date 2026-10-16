<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; }
        h1 { font-size: 20px; margin: 0; }
        .label { color: #6b7280; font-size: 10px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; border-bottom: 2px solid #e5e7eb; padding: 8px; font-size: 10px; text-transform: uppercase; color: #6b7280; }
        td { padding: 8px; border-bottom: 1px solid #f3f4f6; }
        .totals { width: 250px; margin-left: auto; margin-top: 20px; }
        .totals td { border: none; padding: 4px 8px; }
        .totals .final { font-weight: bold; border-top: 2px solid #1f2937; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <div>
            <h1>{{ $document->type === 'quote' ? 'Quote' : 'Invoice' }}</h1>
            <p class="label">{{ $document->number }}</p>
        </div>
        <div class="text-right">
            <p class="label">Date</p>
            <p>{{ $document->issue_date->format('Y-m-d') }}</p>
            @if ($document->due_date)
                <p class="label">Due date</p>
                <p>{{ $document->due_date->format('Y-m-d') }}</p>
            @endif
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <p class="label">Client</p>
        <p><strong>{{ $document->client->name }}</strong></p>
        @if ($document->client->address)
            <p>{{ $document->client->address }}</p>
        @endif
        @if ($document->client->tax_id)
            <p>Business ID (SIRET): {{ $document->client->tax_id }}</p>
        @endif
    </div>

    <table>
