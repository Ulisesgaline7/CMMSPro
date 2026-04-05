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
        Schema::create('sensors', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->string('code', 100);
            $table->string('name', 255);
            $table->string('type', 50);
            $table->string('unit', 30);
            $table->string('status', 50)->default('active');
            $table->decimal('min_threshold', 12, 4)->nullable();
            $table->decimal('max_threshold', 12, 4)->nullable();
            $table->decimal('warning_threshold_low', 12, 4)->nullable();
            $table->decimal('warning_threshold_high', 12, 4)->nullable();
            $table->smallInteger('sampling_interval_seconds')->default(60);
            $table->decimal('last_reading_value', 12, 4)->nullable();
            $table->timestamp('last_reading_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'asset_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
