<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $excl = fake()->randomFloat(4, 100, 10000);
        $rate = 0.2000;

        return [
            'tenant_id' => Tenant::factory(),
            'customer_id' => Customer::factory(),
            'number' => null,
            'status' => 'draft',
            'currency' => 'EUR',
            'amount_excl_tax' => $excl,
            'tax_amount' => round($excl * $rate, 4),
            'amount_incl_tax' => round($excl * (1 + $rate), 4),
            'issued_at' => null,
            'due_on' => null,
        ];
    }

    public function issued(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'issued',
            'number' => 'F-'.fake()->unique()->numberBetween(1000, 9999),
            'issued_at' => now(),
        ]);
    }
}
