<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('reference', 64);
            $table->string('label');
            $table->decimal('unit_price', 19, 4);
            $table->decimal('tax_rate', 5, 4);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'reference']);
        });
    }
};
