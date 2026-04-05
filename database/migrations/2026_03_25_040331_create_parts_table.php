<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('part_number', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->text('description')->nullable();
            $table->string('unit', 30)->default('pieza');
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('min_stock')->default(0);
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->string('storage_location')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'part_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
