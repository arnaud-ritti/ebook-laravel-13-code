<?php

declare(strict_types=1);

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

it('n émet que les colonnes modifiées dans un update', function () {
    $customer = Customer::factory()->create();

    $statements = [];
    DB::listen(function ($query) use (&$statements): void {
        $statements[] = $query->sql;
    });

    $customer->name = 'Nouvelle raison sociale';
    $customer->save();

    $updates = array_values(array_filter(
        $statements,
        fn (string $sql): bool => str_starts_with(strtolower($sql), 'update')
    ));

    expect($updates)->toHaveCount(1)
        ->and($updates[0])->toContain('name')
        ->and($updates[0])->not->toContain('vat_number');
});
