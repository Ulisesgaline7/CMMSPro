<?php

use App\Enums\AssetCriticality;
use App\Enums\AssetStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('asset_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->string('serial_number')->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->year('manufacture_year')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('warranty_expires_at')->nullable();
            $table->decimal('purchase_cost', 12, 2)->nullable();
            $table->string('status')->default(AssetStatus::Active->value);
            $table->string('criticality')->default(AssetCriticality::Medium->value);
            $table->json('specs')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'criticality']);
            $table->index(['tenant_id', 'location_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
