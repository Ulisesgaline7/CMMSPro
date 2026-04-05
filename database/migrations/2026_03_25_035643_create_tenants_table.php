<?php

use App\Enums\TenantPlan;
use App\Enums\TenantStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('plan')->default(TenantPlan::Starter->value);
            $table->string('status')->default(TenantStatus::Trial->value);
            $table->json('settings')->nullable();
            $table->unsignedSmallInteger('max_users')->default(5);
            $table->unsignedSmallInteger('max_assets')->default(50);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
