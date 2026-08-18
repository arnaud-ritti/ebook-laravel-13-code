<?php

declare(strict_types=1);

use App\Support\Macros\QueryBuilderMacros;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

afterEach(function () {
    QueryBuilderMacros::register();
});

it('perd la macro apres un flush et retombe silencieusement sur dynamicWhere, la table etant statique et non liee a la requete', function () {
    QueryBuilder::flushMacros();

    expect(QueryBuilder::hasMacro('whereWithinPeriod'))->toBeFalse();

    $requete = DB::table('invoices')
        ->whereWithinPeriod('issued_at', new DateTimeImmutable('2026-08-01'), new DateTimeImmutable('2026-09-01'));

    expect($requete->toSql())->toContain('within_period')
        ->and($requete->getBindings())->toBe(['issued_at']);
});

it('retrouve la macro apres un nouvel enregistrement', function () {
    QueryBuilder::flushMacros();
    QueryBuilderMacros::register();

    expect(QueryBuilder::hasMacro('whereWithinPeriod'))->toBeTrue();

    $sql = DB::table('invoices')
        ->whereWithinPeriod('issued_at', new DateTimeImmutable('2026-08-01'), new DateTimeImmutable('2026-09-01'))
        ->toSql();

    expect($sql)->toContain('>= ?')
        ->and($sql)->toContain('< ?');
});
