<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tạo bảng đặt phòng.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users');                   // Tài khoản đặt (null = khách vãng lai)
            $table->foreignId('room_id')
                  ->constrained('rooms');                   // Phòng được đặt
            $table->string('guest_name');                   // Tên khách hàng
            $table->string('guest_phone', 20);              // Số điện thoại khách
            $table->string('guest_email')->nullable();      // Email khách (tuỳ chọn)
            $table->date('check_in_date');                  // Ngày nhận phòng
            $table->date('check_out_date');                 // Ngày trả phòng
            $table->integer('guests_count')->default(1);    // Số lượng khách
            $table->enum('status', [
                'pending',       // Chờ xác nhận
                'confirmed',     // Đã xác nhận
                'checked_in',    // Đã nhận phòng
                'checked_out',   // Đã trả phòng
                'cancelled',     // Đã huỷ
            ])->default('pending');
            $table->enum('payment_status', [
                'unpaid',        // Chưa thanh toán
                'partial',       // Thanh toán một phần
                'paid',          // Đã thanh toán
            ])->default('unpaid');
            $table->decimal('total_price', 12, 0)->default(0); // Tổng tiền (VNĐ)
            $table->text('notes')->nullable();              // Ghi chú
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
