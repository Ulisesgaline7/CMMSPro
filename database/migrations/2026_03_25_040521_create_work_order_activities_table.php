<?php

use App\Enums\WorkOrderActivityAction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action')->default(WorkOrderActivityAction::Created->value);
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['work_order_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_activities');
    }
};
