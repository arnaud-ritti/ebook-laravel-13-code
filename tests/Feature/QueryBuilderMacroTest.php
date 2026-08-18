<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

it('compile un intervalle semi ouvert, borne haute exclue', function () {
    $sql = DB::table('invoices')
        ->whereWithinPeriod('issued_at', new DateTimeImmutable('2026-08-01 00:00:00'), new DateTimeImmutable('2026-09-01 00:00:00'))
        ->toSql();

    expect($sql)->toContain('>= ?')
        ->and($sql)->toContain('< ?')
        ->and($sql)->not->toContain('between');
});

it('exclut la borne haute et inclut la borne basse sur le moteur courant', function () {
    $tenant = Tenant::factory()->create();
    $customer = Customer::factory()->for($tenant)->create();

    $borneBasse = new DateTimeImmutable('2026-08-01 00:00:00');
    $borneHaute = new DateTimeImmutable('2026-09-01 00:00:00');

    // App\Models\Invoice ne declare pas de relation tenant(), contrairement a
    // Customer, Product et InvoiceNumber : le tenant_id se pose ici en attribut brut.
    Invoice::factory()->for($customer)->create(['tenant_id' => $tenant->id, 'issued_at' => $borneBasse]);
    Invoice::factory()->for($customer)->create(['tenant_id' => $tenant->id, 'issued_at' => $borneHaute]);

    $compte = DB::table('invoices')->whereWithinPeriod('issued_at', $borneBasse, $borneHaute)->count();

    expect($compte)->toBe(1);
});
