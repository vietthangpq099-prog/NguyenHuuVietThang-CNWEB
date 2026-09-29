<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tạo bảng loại phòng: Standard, Superior, Deluxe, Suite.
     */
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // Tên loại phòng
            $table->text('description')->nullable();       // Mô tả tiện nghi
            $table->decimal('base_price', 12, 0);          // Giá cơ bản mỗi đêm (VNĐ)
            $table->integer('capacity');                    // Sức chứa tối đa (số khách)
            $table->float('area')->nullable();              // Diện tích (m²)
            $table->string('image')->nullable();            // Ảnh minh hoạ (URL Unsplash)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};
