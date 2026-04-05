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
        Schema::create('sensor_readings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sensor_id')->constrained('sensors')->cascadeOnDelete();
            $table->unsignedBigInteger('tenant_id');
            $table->decimal('value', 12, 4);
            $table->string('quality', 20)->default('good');
            $table->timestamp('read_at');
            $table->timestamps();

            $table->index(['sensor_id', 'read_at']);
            $table->index(['tenant_id', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
