<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Services\DocumentNumberGenerator;
use Illuminate\Support\Facades\DB;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'numero',
        'client_id',
        'statut',
        'date_creation',
        'date_echeance',
    ];

    protected $casts = [
        'date_creation' => 'date',
        'date_echeance' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(DocumentLine::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Total HT = somme des (quantite * prix_unitaire) de chaque ligne
    protected function totalHt(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->lines->sum(
                fn ($line) => $line->quantite * $line->prix_unitaire
            ),
        );
    }

    // Total TVA = somme de la TVA de chaque ligne
    protected function totalTva(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->lines->sum(
                fn ($line) => $line->quantite * $line->prix_unitaire * ($line->taux_tva / 100)
            ),
        );
    }

    // Total TTC = HT + TVA
    protected function totalTtc(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_ht + $this->total_tva,
        );
    }

public function convertToInvoice(): self
{
    if ($this->type !== 'devis') {
        throw new \LogicException('Only a quote can be converted to an invoice.');
    }


    if ($this->statut !== 'accepte') {
        throw new \LogicException('The quote must be accepted before conversion.');
    }

    return DB::transaction(function () {
        $generator = new DocumentNumberGenerator();

        $invoice = self::create([
            'type' => 'facture',
            'numero' => $generator->generate('facture'),
            'client_id' => $this->client_id,
            'statut' => 'brouillon',
            'date_creation' => now(),
            'date_echeance' => now()->addDays(30),
        ]);

        foreach ($this->lines as $line) {
            $invoice->lines()->create([
                'designation' => $line->designation,
                'quantite' => $line->quantite,
                'prix_unitaire' => $line->prix_unitaire,
                'taux_tva' => $line->taux_tva,
            ]);
        }

        return $invoice;
    });
}
protected function totalPaye(): Attribute
{
    return Attribute::make(
        get: fn () => $this->payments->sum('montant'),
    );
}

protected function solde(): Attribute
{
    return Attribute::make(
        get: fn () => $this->total_ttc - $this->total_paye,
    );
}
public function enregistrerPaiement(float $montant, string $date, ?string $moyenPaiement = null): Payment
{
    if ($this->type !== 'facture') {
        throw new \LogicException('Only an invoice can receive a payment.');
    }

    return DB::transaction(function () use ($montant, $date, $moyenPaiement) {
        $payment = $this->payments()->create([
            'montant' => $montant,
            'date' => $date,
            'moyen_paiement' => $moyenPaiement,
        ]);

        $this->refresh();

        if ($this->solde <= 0) {
            $this->update(['statut' => 'paye']);
        }

        return $payment;
    });
}
}
