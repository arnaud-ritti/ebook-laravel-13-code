<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceLine>
 */
class InvoiceLineFactory extends Factory
{
    /** @var array<string, int<1, max>> */
    private static array $positions = [];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->randomFloat(3, 1, 20);
        $unitPrice = fake()->randomFloat(4, 5, 500);
        $rate = 0.2000;
        $excl = round($quantity * $unitPrice, 4);

        return [
            'invoice_id' => Invoice::factory(),
            'product_id' => null,
            'position' => 0,
            'label' => fake()->words(3, true),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_rate' => $rate,
            'amount_excl_tax' => $excl,
            'tax_amount' => round($excl * $rate, 4),
            'amount_incl_tax' => round($excl * (1 + $rate), 4),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (InvoiceLine $line): void {
            if ($line->position !== 0) {
                return;
            }

            /** @var string $invoiceId */
            $invoiceId = $line->getAttribute('invoice_id');

            $position = (self::$positions[$invoiceId] ?? 0) + 1;

            self::$positions[$invoiceId] = $position;

            $line->position = $position;
        });
    }
}
