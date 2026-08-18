<?php

declare(strict_types=1);

namespace App\Providers;

use App\Support\Macros\EloquentBuilderMixin;
use App\Support\Macros\QueryBuilderMacros;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        QueryBuilderMacros::register();

        EloquentBuilder::mixin(new EloquentBuilderMixin);
    }
}
