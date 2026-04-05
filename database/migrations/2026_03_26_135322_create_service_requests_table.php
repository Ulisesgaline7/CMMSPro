<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('priority');
            $table->string('status')->default('open');
            $table->string('location_description')->nullable();
            $table->datetime('sla_deadline')->nullable();
            $table->datetime('resolved_at')->nullable();
            $table->datetime('closed_at')->nullable();
            $table->integer('resolution_time')->nullable();
            $table->boolean('sla_met')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'sla_deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
