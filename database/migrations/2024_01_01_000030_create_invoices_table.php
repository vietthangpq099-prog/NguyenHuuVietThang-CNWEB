<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tạo bảng hoá đơn.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                  ->constrained('bookings');                // Liên kết đặt phòng
            $table->string('invoice_number')->unique();    // Số hoá đơn (VD: INV-2024-0001)
            $table->date('issued_at');                     // Ngày lập hoá đơn
            $table->date('due_date')->nullable();          // Hạn thanh toán
            $table->decimal('subtotal', 12, 0);            // Tạm tính (trước thuế)
            $table->decimal('tax', 12, 0)->default(0);     // Thuế VAT
            $table->decimal('total', 12, 0);               // Tổng cộng (sau thuế)
            $table->enum('status', [
                'draft',         // Nháp
                'paid',          // Đã thanh toán
                'cancelled',     // Đã huỷ
            ])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
