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
            <h1>{{ ucfirst($document->type) }}</h1>
            <p class="label">{{ $document->numero }}</p>
        </div>
        <div class="text-right">
            <p class="label">Date</p>
            <p>{{ $document->date_creation->format('d/m/Y') }}</p>
            @if ($document->date_echeance)
                <p class="label">Échéance</p>
                <p>{{ $document->date_echeance->format('d/m/Y') }}</p>
            @endif
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <p class="label">Client</p>
        <p><strong>{{ $document->client->nom }}</strong></p>
        @if ($document->client->adresse)
            <p>{{ $document->client->adresse }}</p>
        @endif
        @if ($document->client->siret)
            <p>SIRET : {{ $document->client->siret }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="text-right">Qté</th>
                <th class="text-right">Prix unit.</th>
                <th class="text-right">TVA</th>
                <th class="text-right">Total HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($document->lines as $line)
                <tr>
                    <td>{{ $line->designation }}</td>
                    <td class="text-right">{{ $line->quantite }}</td>
                    <td class="text-right">{{ number_format($line->prix_unitaire, 2) }} €</td>
                    <td class="text-right">{{ $line->taux_tva }}%</td>
                    <td class="text-right">{{ number_format($line->quantite * $line->prix_unitaire, 2) }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Total HT</td><td class="text-right">{{ number_format($document->total_ht, 2) }} €</td></tr>
        <tr><td>TVA</td><td class="text-right">{{ number_format($document->total_tva, 2) }} €</td></tr>
        <tr class="final"><td>Total TTC</td><td class="text-right">{{ number_format($document->total_ttc, 2) }} €</td></tr>
    </table>

</body>
</html>
