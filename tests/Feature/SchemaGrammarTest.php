<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

it('conserve la précision et l échelle d une colonne decimale, sauf sous sqlite qui les perd', function () {
    $columns = collect(Schema::getColumns('invoices'))->keyBy('name');

    $type = $columns['amount_excl_tax']['type'];

    if (config('database.default') === 'sqlite') {
        expect($type)->toBe('numeric');

        return;
    }

    $matched = preg_match('/\((\d+),\s*(\d+)\)/', $type, $matches);

    $precision = isset($matches[1]) ? (int) $matches[1] : null;
    $scale = isset($matches[2]) ? (int) $matches[2] : null;

    expect($matched)->toBe(1)
        ->and($precision)->toBe(19)
        ->and($scale)->toBe(4);
});

it('conserve la longueur de la clé ulid, sauf sous sqlite qui la perd', function () {
    $columns = collect(Schema::getColumns('invoices'))->keyBy('name');

    $type = $columns['id']['type'];

    if (config('database.default') === 'sqlite') {
        expect($type)->toBe('varchar');

        return;
    }

    $matched = preg_match('/\((\d+)\)/', $type, $matches);

    $length = isset($matches[1]) ? (int) $matches[1] : null;

    expect($matched)->toBe(1)
        ->and($length)->toBe(26);
});
