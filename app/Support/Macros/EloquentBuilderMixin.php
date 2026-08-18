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
            /**
             * @var Builder<Model> $this
             *
             * @phpstan-ignore varTag.nativeType (la fermeture est reliee a l execution par bindTo, l analyseur raisonne sur la portee lexicale de sa definition)
             */
            $this->getQuery()->whereWithinPeriod($this->qualifyColumn($column), $from, $until);

            return $this;
        };
    }

    public function orderByCreationKey(): Closure
    {
        return function (string $direction = 'asc'): Builder {
            /**
             * Le direction natif reste string, orderBy() exige 'asc'|'desc'|SortDirection :
             * le docblock resserre localement ce que le contrat public ne verrouille pas.
             *
             * @var Builder<Model> $this
             * @var 'asc'|'desc' $direction
             *
             * @phpstan-ignore varTag.nativeType (la fermeture est reliee a l execution par bindTo, l analyseur raisonne sur la portee lexicale de sa definition)
             */
            return $this->orderBy($this->getModel()->getQualifiedKeyName(), $direction);
        };
    }
}
