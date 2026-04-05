<?php

use App\Enums\WorkOrderPriority;
use App\Enums\WorkOrderStatus;
use App\Enums\WorkOrderType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code', 50)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default(WorkOrderType::Corrective->value);
            $table->string('status')->default(WorkOrderStatus::Draft->value);
            $table->string('priority')->default(WorkOrderPriority::Medium->value);
            $table->timestamp('due_date')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('estimated_duration')->nullable()->comment('minutos');
            $table->unsignedInteger('actual_duration')->nullable()->comment('minutos');
            $table->text('failure_cause')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'priority']);
            $table->index(['tenant_id', 'assigned_to']);
            $table->index(['tenant_id', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
