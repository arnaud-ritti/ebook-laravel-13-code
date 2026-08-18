<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Tenant;

it('qualifie la colonne par la table du modele, ce que le query builder ne peut pas faire', function () {
    $sql = Invoice::query()
        ->whereWithinPeriodOn('issued_at', new DateTimeImmutable('2026-08-01'), new DateTimeImmutable('2026-09-01'))
        ->toSql();

    expect($sql)->toContain('invoices')
        ->and($sql)->toContain('issued_at');
});

it('trie par la cle ulid, dont l ordre lexicographique suit l ordre de generation', function () {
    $tenant = Tenant::factory()->create();
    $customer = Customer::factory()->for($tenant)->create();

    // Deux ULID generes dans le meme processus PHP restent ordonnes meme dans
    // la meme milliseconde : Symfony\Component\Uid\Ulid::generate() incremente
    // sa partie aleatoire statique plutot que d en tirer une nouvelle.
    // App\Models\Invoice ne declare pas de relation tenant(), contrairement a
    // Customer, Product et InvoiceNumber : le tenant_id se pose ici en attribut brut.
    $premiere = Invoice::factory()->for($customer)->create(['tenant_id' => $tenant->id]);
    $seconde = Invoice::factory()->for($customer)->create(['tenant_id' => $tenant->id]);

    $ids = Invoice::query()->orderByCreationKey()->pluck('id')->all();

    expect($ids)->toBe([$premiere->id, $seconde->id]);
});
