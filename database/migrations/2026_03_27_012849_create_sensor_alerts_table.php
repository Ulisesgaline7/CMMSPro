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
        Schema::create('sensor_alerts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sensor_id')->constrained('sensors')->cascadeOnDelete();
            $table->unsignedBigInteger('tenant_id');
            $table->string('type', 50);
            $table->string('severity', 20)->default('warning');
            $table->text('message');
            $table->decimal('value', 12, 4)->nullable();
            $table->decimal('threshold', 12, 4)->nullable();
            $table->timestamp('triggered_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->unsignedBigInteger('acknowledged_by')->nullable();
            $table->foreign('acknowledged_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['sensor_id', 'is_active']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_alerts');
    }
};
