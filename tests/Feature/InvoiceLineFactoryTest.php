<?php

declare(strict_types=1);

use App\Models\Invoice;
use App\Models\InvoiceLine;

it('attribue des positions distinctes à plusieurs lignes créées pour la même facture', function () {
    $invoice = Invoice::factory()->create();

    $lines = InvoiceLine::factory()->count(3)->create(['invoice_id' => $invoice->id]);

    expect($lines->pluck('position')->sort()->values()->all())->toBe([1, 2, 3])
        ->and(InvoiceLine::where('invoice_id', $invoice->id)->count())->toBe(3);
});

it('respecte une position explicitement fournie sans la réattribuer', function () {
    $invoice = Invoice::factory()->create();

    $line = InvoiceLine::factory()->create(['invoice_id' => $invoice->id, 'position' => 5]);

    expect($line->position)->toBe(5);
});
