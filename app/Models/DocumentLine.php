<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'designation',
        'quantite',
        'prix_unitaire',
        'taux_tva',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
