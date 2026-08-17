<?php

declare(strict_types=1);

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\LazyLoadingViolationException;
use Illuminate\Support\Facades\DB;

it('compte une requête par facture sans chargement anticipé', function () {
    Model::preventLazyLoading(false);

    Invoice::factory()->count(5)->issued()->create();

    $count = 0;
    DB::listen(function () use (&$count): void {
        $count++;
    });

    Invoice::query()->issued()->get()->each(fn (Invoice $invoice) => $invoice->customer?->name);

    expect($count)->toBe(6);
});

it('compte deux requêtes avec chargement anticipé', function () {
    Model::preventLazyLoading(false);

    Invoice::factory()->count(5)->issued()->create();

    $count = 0;
    DB::listen(function () use (&$count): void {
        $count++;
    });

    Invoice::query()->issued()->with('customer')->get()
        ->each(fn (Invoice $invoice) => $invoice->customer?->name);

    expect($count)->toBe(2);
});

it('lève une violation quand le mode strict est actif sur une collection de plusieurs factures', function () {
    Model::preventLazyLoading();

    Invoice::factory()->count(2)->issued()->create();

    expect(fn () => Invoice::query()->issued()->get()->each(fn (Invoice $invoice) => $invoice->customer?->name))
        ->toThrow(LazyLoadingViolationException::class);
});
