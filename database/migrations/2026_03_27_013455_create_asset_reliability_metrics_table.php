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
        Schema::create('asset_reliability_metrics', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->timestamp('calculated_at');
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('total_work_orders')->default(0);
            $table->integer('corrective_count')->default(0);
            $table->integer('total_downtime_minutes')->default(0);
            $table->integer('total_repair_time_minutes')->default(0);
            $table->decimal('mtbf_hours', 10, 2)->nullable();
            $table->decimal('mttr_hours', 10, 2)->nullable();
            $table->decimal('availability_percent', 5, 2)->nullable();
            $table->decimal('failure_rate', 12, 8)->nullable();
            $table->integer('recommended_pm_interval_days')->nullable();
            $table->decimal('failure_probability_30d', 5, 2)->nullable();
            $table->decimal('anomaly_score', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['asset_id', 'period_start', 'period_end'], 'arm_asset_period_unique');
            $table->index(['tenant_id', 'asset_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_reliability_metrics');
    }
};
