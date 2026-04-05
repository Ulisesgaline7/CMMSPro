<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permit_to_work', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('requested_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');

            $table->string('code')->unique();
            $table->string('title');
            $table->string('type');
            $table->string('status');
            $table->string('risk_level');

            $table->text('description')->nullable();
            $table->text('lockout_points')->nullable();
            $table->text('required_ppe')->nullable();
            $table->text('precautions')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('closure_notes')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'work_order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permit_to_work');
    }
};
