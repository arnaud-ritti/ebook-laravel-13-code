<?php

use App\Contracts\Clock;
use App\Support\FrozenClock;
use App\Support\SystemClock;

it('résout SystemClock pour le contrat Clock', function () {
    expect(app(Clock::class))->toBeInstanceOf(SystemClock::class);
});

it('rend la même instance à chaque résolution, le binding étant un singleton', function () {
    expect(app(Clock::class))->toBe(app(Clock::class));
});

it('injecte FrozenClock dans la commande quand le conteneur est réécrit', function () {
    $instant = new DateTimeImmutable('2026-08-16T09:30:00+00:00');

    app()->instance(Clock::class, new FrozenClock($instant));

    $this->artisan('show-clock')
        ->expectsOutputToContain('2026-08-16T09:30:00+00:00')
        ->expectsOutputToContain(FrozenClock::class)
        ->assertSuccessful();
});
