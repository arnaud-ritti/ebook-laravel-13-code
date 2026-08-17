<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('product_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('label');
            $table->decimal('quantity', 12, 3);
            $table->decimal('unit_price', 19, 4);
            $table->decimal('tax_rate', 5, 4);
            $table->decimal('amount_excl_tax', 19, 4);
            $table->decimal('tax_amount', 19, 4);
            $table->decimal('amount_incl_tax', 19, 4);

            $table->unique(['invoice_id', 'position']);
        });
    }
};
