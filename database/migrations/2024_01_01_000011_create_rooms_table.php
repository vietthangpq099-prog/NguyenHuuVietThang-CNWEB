<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tạo bảng danh sách phòng cụ thể.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();       // Số phòng (101, 202A...)
            $table->foreignId('room_type_id')
                  ->constrained('room_types');              // Liên kết loại phòng
            $table->integer('floor');                       // Tầng
            $table->enum('status', [
                'available',     // Trống / sẵn sàng
                'booked',        // Đã đặt
                'occupied',      // Đang có khách ở
                'maintenance',   // Đang bảo trì
                'cleaning',      // Đang dọn dẹp
            ])->default('available');
            $table->decimal('price_override', 12, 0)->nullable(); // Giá ghi đè (nếu khác loại phòng)
            $table->string('image')->nullable();            // Ảnh phòng (URL Unsplash)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
