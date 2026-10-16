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
                'quantity' => 2,
                'unit_price' => 100,
                'vat_rate' => 20,
            ]),
            (new DocumentLine())->forceFill([
                'quantity' => 1,
                'unit_price' => 50,
                'vat_rate' => 10,
            ]),
        ]));

        $this->assertEqualsWithDelta(250, $document->subtotal, 0.001);
        $this->assertEqualsWithDelta(45, $document->vat_amount, 0.001);
        $this->assertEqualsWithDelta(295, $document->total, 0.001);
    }
}
