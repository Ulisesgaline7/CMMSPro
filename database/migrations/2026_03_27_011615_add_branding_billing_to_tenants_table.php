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
        Schema::table('tenants', function (Blueprint $table): void {
            $table->string('subdomain', 100)->nullable()->unique()->after('slug');
            $table->string('logo_path', 500)->nullable();
            $table->string('primary_color', 7)->nullable()->default('#3B82F6');
            $table->string('secondary_color', 7)->nullable()->default('#1E40AF');
            $table->string('brand_name', 255)->nullable();
            $table->unsignedTinyInteger('white_label_level')->default(0);
            $table->string('stripe_customer_id', 255)->nullable()->unique();
            $table->string('billing_email', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table): void {
            $table->dropColumn([
                'subdomain',
                'logo_path',
                'primary_color',
                'secondary_color',
                'brand_name',
                'white_label_level',
                'stripe_customer_id',
                'billing_email',
            ]);
        });
    }
};
