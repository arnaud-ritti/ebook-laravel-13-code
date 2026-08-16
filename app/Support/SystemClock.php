<?php

namespace App\Support;

use App\Contracts\Clock;
use Carbon\CarbonImmutable;
use DateTimeImmutable;

class SystemClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return CarbonImmutable::now();
    }
}
