<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    use HasUlids;

    /** @var list<string> */
    protected $fillable = [
        'tenant_id', 'customer_id', 'number', 'status', 'currency',
        'amount_excl_tax', 'tax_amount', 'amount_incl_tax', 'issued_at', 'due_on',
    ];

    /** @return HasMany<InvoiceLine, $this> */
    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class)->orderBy('position');
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @param Builder<$this> $query */
    public function scopeIssued(Builder $query): void
    {
        $query->whereNotNull('issued_at');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'due_on' => 'date',
            'amount_excl_tax' => 'decimal:4',
            'tax_amount' => 'decimal:4',
            'amount_incl_tax' => 'decimal:4',
        ];
    }
}
