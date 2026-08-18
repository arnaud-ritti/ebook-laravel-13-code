<?php

declare(strict_types=1);

namespace App\Support\Macros;

use DateTimeInterface;
use Illuminate\Database\Query\Builder;

class QueryBuilderMacros
{
    public static function register(): void
    {
        Builder::macro('whereWithinPeriod', function (string $column, DateTimeInterface $from, DateTimeInterface $until): Builder {
            /** @var Builder $this */
            return $this->where($column, '>=', $from)
                ->where($column, '<', $until);
        });
    }
}
