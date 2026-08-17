<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

it('porte l unicité du numéro de facture par tenant', function () {
    $unique = collect(Schema::getIndexes('invoices'))
        ->first(fn (array $index): bool => $index['unique'] && in_array('number', $index['columns'], true));

    expect($unique)->not->toBeNull()
        ->and($unique['columns'])->toBe(['tenant_id', 'number']);
});

it('place tenant_id en tête de tout index composite qui le contient', function () {
    foreach (['customers', 'products', 'invoices', 'tenant_user', 'invoice_numbers'] as $table) {
        foreach (Schema::getIndexes($table) as $index) {
            if (count($index['columns']) > 1 && in_array('tenant_id', $index['columns'], true)) {
                expect($index['columns'][0])->toBe('tenant_id', "index {$index['name']} sur {$table}");
            }
        }
    }
});
