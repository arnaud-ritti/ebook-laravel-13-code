<?php

declare(strict_types=1);

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

it('enregistre la macro de periode sur le query builder', function () {
    expect(QueryBuilder::hasMacro('whereWithinPeriod'))->toBeTrue();
});

it('enregistre les deux macros du mixin comme macros globales du builder eloquent', function () {
    expect(EloquentBuilder::hasGlobalMacro('whereWithinPeriodOn'))->toBeTrue()
        ->and(EloquentBuilder::hasGlobalMacro('orderByCreationKey'))->toBeTrue();
});

it('ne pose aucune macro locale sur un builder neuf', function () {
    expect(Invoice::query()->hasMacro('whereWithinPeriodOn'))->toBeFalse();
});
