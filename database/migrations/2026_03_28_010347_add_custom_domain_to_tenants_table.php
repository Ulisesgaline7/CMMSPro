<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table): void {
            $table->string('custom_domain')->nullable()->unique()->after('subdomain');
            $table->boolean('custom_domain_verified')->default(false)->after('custom_domain');
            $table->string('reseller_id')->nullable()->after('white_label_level');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table): void {
            $table->dropColumn(['custom_domain', 'custom_domain_verified', 'reseller_id']);
        });
    }
};
