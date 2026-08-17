<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $tenantOne = Tenant::factory()->create();
        $tenantTwo = Tenant::factory()->create();

        $ownerOne = User::factory()->create();
        $ownerTwo = User::factory()->create();
        $shared = User::factory()->create();

        $tenantOne->users()->attach($ownerOne, ['role' => 'owner', 'joined_at' => now()]);
        $tenantTwo->users()->attach($ownerTwo, ['role' => 'owner', 'joined_at' => now()]);
        $tenantOne->users()->attach($shared, ['role' => 'member', 'joined_at' => now()]);
        $tenantTwo->users()->attach($shared, ['role' => 'member', 'joined_at' => now()]);

        foreach ([$tenantOne, $tenantTwo] as $tenant) {
            $customers = Customer::factory(5)->create(['tenant_id' => $tenant->id]);
            $products = Product::factory(8)->create(['tenant_id' => $tenant->id]);

            for ($index = 1; $index <= 10; $index++) {
                $this->createInvoice($tenant, $customers, $products, $index <= 5);
            }
        }
    }

    /**
     * @param  Collection<int, Customer>  $customers
     * @param  Collection<int, Product>  $products
     */
    private function createInvoice(Tenant $tenant, Collection $customers, Collection $products, bool $issued): Invoice
    {
        $invoice = Invoice::factory()->create([
            'tenant_id' => $tenant->id,
            'customer_id' => $customers->random()->id,
            'amount_excl_tax' => 0,
            'tax_amount' => 0,
            'amount_incl_tax' => 0,
        ]);

        $exclTotal = 0.0;
        $taxTotal = 0.0;

        for ($position = 1; $position <= fake()->numberBetween(2, 5); $position++) {
            ['exclTax' => $exclTax, 'taxAmount' => $taxAmount] = $this->createLine($invoice, $products, $position);

            $exclTotal += $exclTax;
            $taxTotal += $taxAmount;
        }

        $exclTotal = round($exclTotal, 4);
        $taxTotal = round($taxTotal, 4);

        $invoice->update([
            'amount_excl_tax' => $exclTotal,
            'tax_amount' => $taxTotal,
            'amount_incl_tax' => round($exclTotal + $taxTotal, 4),
            'status' => $issued ? 'issued' : 'draft',
            'number' => $issued ? 'F-'.fake()->unique()->numberBetween(1000, 99999) : null,
            'issued_at' => $issued ? now() : null,
        ]);

        return $invoice;
    }

    /**
     * @param  Collection<int, Product>  $products
     * @return array{exclTax: float, taxAmount: float}
     */
    private function createLine(Invoice $invoice, Collection $products, int $position): array
    {
        $product = fake()->boolean(70) ? $products->random() : null;
        $quantity = fake()->randomFloat(3, 1, 20);

        if ($product === null) {
            $label = fake()->words(3, true);
            $unitPrice = fake()->randomFloat(4, 5, 500);
            $taxRate = 0.2000;
        } else {
            $label = $product->label;
            $unitPrice = (float) $product->unit_price;
            $taxRate = (float) $product->tax_rate;
        }

        $exclTax = round($quantity * $unitPrice, 4);
        $taxAmount = round($exclTax * $taxRate, 4);

        InvoiceLine::factory()->create([
            'invoice_id' => $invoice->id,
            'product_id' => $product?->id,
            'position' => $position,
            'label' => $label,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'amount_excl_tax' => $exclTax,
            'tax_amount' => $taxAmount,
            'amount_incl_tax' => round($exclTax + $taxAmount, 4),
        ]);

        return ['exclTax' => $exclTax, 'taxAmount' => $taxAmount];
    }
}
