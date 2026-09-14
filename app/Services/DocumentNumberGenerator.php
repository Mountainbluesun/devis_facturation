<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\DB;

class DocumentNumberGenerator
{
    public function generate(string $type): string
    {
        $prefix = $type === 'devis' ? 'DEV' : 'FACT';
        $year = now()->year;

        return DB::transaction(function () use ($type, $prefix, $year) {
            // On verrouille la ligne pendant la transaction pour éviter
            // que deux créations simultanées obtiennent le même numéro
            $lastDocument = Document::where('type', $type)
                ->where('numero', 'like', "{$prefix}-{$year}-%")
                ->lockForUpdate()
                ->orderByDesc('numero')
                ->first();

            if (!$lastDocument) {
                $nextNumber = 1;
            } else {
                // Ex: "FACT-2026-007" → on extrait "007" → 7 → +1 = 8
                $lastNumber = (int) substr($lastDocument->numero, -3);
                $nextNumber = $lastNumber + 1;
            }

            return sprintf('%s-%d-%03d', $prefix, $year, $nextNumber);
        });
    }
}
