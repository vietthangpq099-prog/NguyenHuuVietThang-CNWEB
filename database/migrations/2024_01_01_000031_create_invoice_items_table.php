<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tạo bảng chi tiết hoá đơn.
     */
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')
                  ->constrained('invoices')
                  ->onDelete('cascade');                    // Xoá hoá đơn → xoá mục chi tiết
            $table->string('description');                  // Mô tả (VD: Phòng 101 × 3 đêm)
            $table->decimal('unit_price', 12, 0);          // Đơn giá
            $table->integer('quantity')->default(1);        // Số lượng
            $table->decimal('line_total', 12, 0);          // Thành tiền
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
