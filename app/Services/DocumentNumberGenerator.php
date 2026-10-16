<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\DB;

class DocumentNumberGenerator
{
    public function generate(string $type): string
    {
        $prefix = $type === 'quote' ? 'QUO' : 'INV';
        $year = now()->year;

        return DB::transaction(function () use ($type, $prefix, $year) {
            // Sort by length first, then alphabetically: this orders numbers
            // correctly even past 999 (e.g. "1000" after "999")
            $lastDocument = Document::where('type', $type)
                ->where('number', 'like', "{$prefix}-{$year}-%")
                ->lockForUpdate()
                ->orderByRaw('LENGTH(number) DESC')
                ->orderByDesc('number')
                ->first();

            if (!$lastDocument) {
                $nextNumber = 1;
            } else {
                // Ex: "INV-2026-1007" -> take everything after the last "-" -> 1007 -> +1
                $suffix = substr($lastDocument->number, strrpos($lastDocument->number, '-') + 1);
                $nextNumber = (int) $suffix + 1;
            }

            return sprintf('%s-%d-%03d', $prefix, $year, $nextNumber);
        });
    }
}
