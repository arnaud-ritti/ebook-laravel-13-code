<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

it('applique le même schéma sur le moteur courant', function () {
    Schema::dropIfExists('amount_probes');

    Schema::create('amount_probes', function (Blueprint $table) {
        $table->id();
        $table->decimal('total', 19, 4);
    });

    DB::table('amount_probes')->insert(['total' => '1234.5678']);

    $pilotePdoAttendu = match (config('database.default')) {
        'mariadb' => 'mysql',
        default => config('database.default'),
    };

    expect(Schema::hasTable('amount_probes'))->toBeTrue()
        ->and(DB::connection()->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME))->toBe($pilotePdoAttendu);
});

it('documente le silence de lockForUpdate sous SQLite', function () {
    $sql = DB::table('users')->lockForUpdate()->toSql();

    config('database.default') === 'sqlite'
        ? expect($sql)->not->toContain('for update')
        : expect(strtolower($sql))->toContain('for update');
});
