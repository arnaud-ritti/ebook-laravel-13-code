<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\InvoiceLineFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLine extends Model
{
    /** @use HasFactory<InvoiceLineFactory> */
    use HasFactory;

    use HasUlids;

    public $timestamps = false;

    /** @var list<string> */
    protected $fillable = [
        'invoice_id', 'product_id', 'position', 'label', 'quantity',
        'unit_price', 'tax_rate', 'amount_excl_tax', 'tax_amount', 'amount_incl_tax',
    ];

    /** @return BelongsTo<Invoice, $this> */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'unit_price' => 'decimal:4',
            'tax_rate' => 'decimal:4',
            'amount_excl_tax' => 'decimal:4',
            'tax_amount' => 'decimal:4',
            'amount_incl_tax' => 'decimal:4',
        ];
    }
}
