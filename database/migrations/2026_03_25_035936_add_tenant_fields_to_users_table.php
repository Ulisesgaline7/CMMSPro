<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete()->after('id');
            $table->string('role')->default(UserRole::Technician->value)->after('email_verified_at');
            $table->string('status')->default(UserStatus::Active->value)->after('role');
            $table->string('phone', 30)->nullable()->after('status');
            $table->string('employee_code', 50)->nullable()->after('phone');

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'status']);
            $table->dropIndex(['tenant_id', 'role']);
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'role', 'status', 'phone', 'employee_code']);
        });
    }
};
