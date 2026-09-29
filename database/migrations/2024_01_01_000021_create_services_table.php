<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tạo bảng danh mục dịch vụ phụ.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Tên dịch vụ
            $table->text('description')->nullable();       // Mô tả
            $table->decimal('price', 12, 0);               // Đơn giá (VNĐ)
            $table->boolean('is_active')->default(true);   // Đang hoạt động
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
