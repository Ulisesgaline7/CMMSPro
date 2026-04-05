<?php

use App\Enums\MaintenancePlanFrequency;
use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default(WorkOrderType::Preventive->value);
            $table->string('frequency')->default(MaintenancePlanFrequency::Monthly->value);
            $table->unsignedInteger('frequency_value')->nullable();
            $table->string('priority')->default(WorkOrderPriority::Medium->value);
            $table->unsignedSmallInteger('estimated_duration')->nullable()->comment('minutos');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_execution_date')->nullable();
            $table->date('last_execution_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('checklist_template')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'next_execution_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_plans');
    }
};
