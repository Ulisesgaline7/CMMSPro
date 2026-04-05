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
        Schema::create('subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('stripe_subscription_id', 255)->nullable()->unique();
            $table->string('stripe_customer_id', 255)->nullable();
            $table->string('status', 50)->default('incomplete');
            $table->string('deployment_type', 50)->default('cloud_saas');
            $table->decimal('base_price_monthly', 8, 2)->default(49);
            $table->decimal('modules_cost', 8, 2)->default(0);
            $table->decimal('users_cost', 8, 2)->default(0);
            $table->decimal('total_monthly', 10, 2)->default(49);
            $table->integer('admin_count')->default(0);
            $table->integer('supervisor_count')->default(0);
            $table->integer('technician_count')->default(0);
            $table->integer('reader_count')->default(0);
            $table->integer('asset_count')->default(0);
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('trial_end')->nullable();
            $table->boolean('cancel_at_period_end')->default(false);
            $table->json('modules_json')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
