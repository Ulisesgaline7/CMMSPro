<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('stripe_invoice_id', 255)->nullable()->unique();
            $table->integer('amount_due')->default(0);
            $table->integer('amount_paid')->default(0);
            $table->string('currency', 3)->default('usd');
            $table->string('status', 50);
            $table->string('invoice_pdf_url', 500)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
