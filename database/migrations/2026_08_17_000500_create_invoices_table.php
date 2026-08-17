<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('customer_id')->constrained()->restrictOnDelete();
            $table->string('number', 32)->nullable();
            $table->string('status', 16);
            $table->char('currency', 3);
            $table->decimal('amount_excl_tax', 19, 4);
            $table->decimal('tax_amount', 19, 4);
            $table->decimal('amount_incl_tax', 19, 4);
            $table->timestamp('issued_at')->nullable();
            $table->date('due_on')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'number']);
            $table->index(['tenant_id', 'status', 'issued_at']);
        });
    }
};
