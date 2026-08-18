<?php

declare(strict_types=1);

use App\Contracts\Clock;
use App\Support\SystemClock;

function horlogeDecoreeVers1900(Clock $horlogeInterne): Clock
{
    return new class($horlogeInterne) implements Clock
    {
        public function __construct(private Clock $horlogeInterne) {}

        public function now(): DateTimeImmutable
        {
            return $this->horlogeInterne->now()->setDate(1900, 1, 1);
        }
    };
}

it('décore ce que le conteneur rend quand extend est posé avant toute résolution', function () {
    app()->extend(Clock::class, fn (Clock $horloge): Clock => horlogeDecoreeVers1900($horloge));

    $premier = app(Clock::class);
    $second = app(Clock::class);

    expect($premier->now()->format('Y'))->toBe('1900');
    expect($second)->toBe($premier);
});

it('remplace l\'instance en cache mais laisse nue toute référence déjà tenue, sur un singleton déjà résolu', function () {
    $referenceCapturee = app(Clock::class);

    expect($referenceCapturee->now()->format('Y'))->not->toBe('1900');

    app()->extend(Clock::class, fn (Clock $horloge): Clock => horlogeDecoreeVers1900($horloge));

    $nouvelleResolution = app(Clock::class);

    expect($nouvelleResolution->now()->format('Y'))->toBe('1900');
    expect($nouvelleResolution)->not->toBe($referenceCapturee);
    expect($referenceCapturee->now()->format('Y'))->not->toBe('1900');
});

it('perd sa décoration après forgetInstance sur un singleton, l\'extender n\'étant pas empilé', function () {
    app(Clock::class);

    app()->extend(Clock::class, fn (Clock $horloge): Clock => horlogeDecoreeVers1900($horloge));

    expect(app(Clock::class)->now()->format('Y'))->toBe('1900');

    app()->forgetInstance(Clock::class);

    expect(app(Clock::class)->now()->format('Y'))->not->toBe('1900');
});

it('rejoue l\'extender à chaque résolution sur une liaison non partagée', function () {
    app()->bind(Clock::class, SystemClock::class);

    app()->extend(Clock::class, fn (Clock $horloge): Clock => horlogeDecoreeVers1900($horloge));

    $premier = app(Clock::class);
    $second = app(Clock::class);

    expect($premier->now()->format('Y'))->toBe('1900');
    expect($second->now()->format('Y'))->toBe('1900');
    expect($second)->not->toBe($premier);
});
