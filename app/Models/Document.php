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
        'number',
        'client_id',
        'user_id',
        'status',
        'issue_date',
        'due_date',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(DocumentLine::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Total HT = somme des (quantity * unit_price) de chaque line
    protected function subtotal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->lines->sum(
                fn ($line) => $line->quantity * $line->unit_price
            ),
        );
    }

    // Total TVA = somme de la TVA de chaque line
    protected function vatAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->lines->sum(
                fn ($line) => $line->quantity * $line->unit_price * ($line->vat_rate / 100)
            ),
        );
    }

    // Total TTC = HT + TVA
    protected function total(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->subtotal + $this->vat_amount,
        );
    }

public function convertToInvoice(): self
{
    if ($this->type !== 'quote') {
        throw new \LogicException('Only a quote can be converted to an invoice.');
    }


    if ($this->status !== 'accepted') {
        throw new \LogicException('The quote must be accepted before conversion.');
    }

    return DB::transaction(function () {
        $generator = new DocumentNumberGenerator();

        $invoice = self::create([
            'type' => 'invoice',
            'number' => $generator->generate('invoice'),
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
        ]);

        foreach ($this->lines as $line) {
            $invoice->lines()->create([
                'description' => $line->description,
                'quantity' => $line->quantity,
                'unit_price' => $line->unit_price,
                'vat_rate' => $line->vat_rate,
            ]);
        }

        return $invoice;
    });
}
protected function totalPaid(): Attribute
{
    return Attribute::make(
        get: fn () => $this->payments->sum('amount'),
    );
}

protected function balance(): Attribute
{
    return Attribute::make(
        get: fn () => $this->total - $this->total_paid,
    );
}
public function recordPayment(float $amount, string $date, ?string $paymentMethod = null): Payment
{
    if ($this->type !== 'invoice') {
        throw new \LogicException('Only an invoice can receive a payment.');
    }

    return DB::transaction(function () use ($amount, $date, $paymentMethod) {
        $payment = $this->payments()->create([
            'amount' => $amount,
            'date' => $date,
            'payment_method' => $paymentMethod,
        ]);

        $this->refresh();

        if ($this->balance <= 0) {
            $this->update(['status' => 'paid']);
        }

        return $payment;
    });
}
}
