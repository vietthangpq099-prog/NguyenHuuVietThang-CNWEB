<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_code')->unique();           // Mã sinh viên (VD: SV202401)
            $table->string('name');                             // Họ và tên
            $table->string('university')->nullable();           // Trường ĐH/CĐ
            $table->string('email')->nullable();                // Email
            $table->string('phone')->nullable();                // Số điện thoại
            $table->date('card_issue_date')->nullable();        // Ngày cấp thẻ
            $table->date('card_expiry_date');                   // Ngày hết hạn thẻ sinh viên
            $table->unsignedInteger('discount_percent')->default(15); // Mức giảm giá (%)
            $table->enum('status', ['active', 'graduated', 'locked'])->default('active'); // Trạng thái
            $table->text('notes')->nullable();                  // Ghi chú thêm
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
