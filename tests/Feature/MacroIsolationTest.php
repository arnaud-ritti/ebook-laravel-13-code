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

it('sert a une seconde requete du meme processus la valeur qu une fermeture de macro a capturee par use a la premiere, substitut au niveau processus du cas worker differe au module 7', function () {
    // Ce depot tourne en mode classique jusqu au Module 7, un processus par requete : ce test
    // ne fait tourner aucun worker Octane et n en simule pas l ordonnancement. Il rejoue au
    // niveau du processus PHP le mecanisme etabli par lecture de source au point 13 du journal,
    // $macros est une propriete statique de classe qu aucun mecanisme du framework ne vide, en
    // appelant deux fois de suite une macro dont la fermeture capture par use une valeur qui,
    // dans une application reelle, serait resolue depuis la requete, un tenant courant ou un
    // utilisateur authentifie.

    $tenantDeLaRequete = 'tenant-premiere-requete';

    QueryBuilder::macro('tenantCapturePourLeTest', function () use ($tenantDeLaRequete): string {
        return $tenantDeLaRequete;
    });

    try {
        /**
         * @var string $reponseAvantMutation
         *
         * @phpstan-ignore method.notFound (macro enregistree dans le corps de ce test, jamais vue par le boot que larastan rejoue avant l analyse)
         */
        $reponseAvantMutation = DB::query()->tenantCapturePourLeTest();

        expect($reponseAvantMutation)->toBe('tenant-premiere-requete');

        // La source mute, comme elle le ferait entre deux requetes servies par le meme worker
        // resident : le boot() qui a enregistre la macro ne se rejoue pas forcement a chacune.
        $tenantDeLaRequete = 'tenant-seconde-requete';

        /**
         * @var string $reponseApresMutation
         *
         * @phpstan-ignore method.notFound (macro enregistree dans le corps de ce test, jamais vue par le boot que larastan rejoue avant l analyse)
         */
        $reponseApresMutation = DB::query()->tenantCapturePourLeTest();

        expect($reponseApresMutation)->toBe('tenant-premiere-requete')
            ->and($reponseApresMutation)->not->toBe($tenantDeLaRequete);
    } finally {
        // La table est statique : sans ce finally, une assertion qui tombe laisserait la
        // macro enregistree pour tous les tests suivants du meme processus PHP.
        QueryBuilder::flushMacros();
    }
});
