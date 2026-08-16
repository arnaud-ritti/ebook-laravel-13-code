<?php

namespace App\Support;

use App\Contracts\Clock;
use DateTimeImmutable;

class FrozenClock implements Clock
{
    public function __construct(protected DateTimeImmutable $instant) {}

    public function now(): DateTimeImmutable
    {
        return $this->instant;
    }
}
