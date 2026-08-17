<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TenantUser extends Pivot
{
    use HasUlids;

    protected $table = 'tenant_user';

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['joined_at' => 'datetime'];
    }
}
