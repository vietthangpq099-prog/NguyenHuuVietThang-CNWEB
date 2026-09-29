<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Thêm cột role_id (vai trò) và phone (số điện thoại) vào bảng users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                  ->after('password')
                  ->constrained('roles');               // Liên kết vai trò
            $table->string('phone', 20)
                  ->nullable()
                  ->after('role_id');                    // Số điện thoại
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'phone']);
        });
    }
};
