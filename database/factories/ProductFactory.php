<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'reference' => strtoupper(fake()->unique()->bothify('PRD-####??')),
            'label' => fake()->words(3, true),
            'unit_price' => fake()->randomFloat(4, 5, 2000),
            'tax_rate' => fake()->randomElement(['0.0000', '0.0550', '0.1000', '0.2000']),
        ];
    }
}
