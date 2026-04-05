<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_checklist_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->boolean('is_completed')->default(false);
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('work_order_checklist_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_checklist_items');
    }
};
