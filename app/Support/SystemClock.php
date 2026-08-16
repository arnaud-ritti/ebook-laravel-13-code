<?php

declare(strict_types=1);

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
