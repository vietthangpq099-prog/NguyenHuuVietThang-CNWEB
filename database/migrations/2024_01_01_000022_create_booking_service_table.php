<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Bảng trung gian: Đặt phòng ↔ Dịch vụ đã sử dụng.
     */
    public function up(): void
    {
        Schema::create('booking_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                  ->constrained('bookings')
                  ->onDelete('cascade');                    // Xoá đặt phòng → xoá dịch vụ kèm
            $table->foreignId('service_id')
                  ->constrained('services');                // Liên kết dịch vụ
            $table->integer('quantity')->default(1);        // Số lượng
            $table->decimal('unit_price', 12, 0);          // Đơn giá tại thời điểm sử dụng
            $table->decimal('total_price', 12, 0);         // Thành tiền = đơn giá × số lượng
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_service');
    }
};
