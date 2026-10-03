<?php

namespace Tests\Unit;

use App\Models\Document;
use App\Models\DocumentLine;
use Tests\TestCase;

class DocumentTotalsTest extends TestCase
{
    public function test_totals_are_computed_from_lines(): void
    {
        $document = new Document();

        $document->setRelation('lines', collect([
            (new DocumentLine())->forceFill([
                'quantite' => 2,
                'prix_unitaire' => 100,
                'taux_tva' => 20,
            ]),
            (new DocumentLine())->forceFill([
                'quantite' => 1,
                'prix_unitaire' => 50,
                'taux_tva' => 10,
            ]),
        ]));

        $this->assertEqualsWithDelta(250, $document->total_ht, 0.001);
        $this->assertEqualsWithDelta(45, $document->total_tva, 0.001);
        $this->assertEqualsWithDelta(295, $document->total_ttc, 0.001);
    }
}
