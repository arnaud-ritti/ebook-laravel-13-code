<?php

declare(strict_types=1);

namespace App\Support\Macros;

use Closure;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EloquentBuilderMixin
{
    public function whereWithinPeriodOn(): Closure
    {
        return function (string $column, DateTimeInterface $from, DateTimeInterface $until): Builder {
            /** @var Builder<Model> $this */
            $this->getQuery()->whereWithinPeriod($this->qualifyColumn($column), $from, $until);

            return $this;
        };
    }

    public function orderByCreationKey(): Closure
    {
        return function (string $direction = 'asc'): Builder {
            /** @var Builder<Model> $this */
            return $this->orderBy($this->getModel()->getQualifiedKeyName(), $direction);
        };
    }
}
