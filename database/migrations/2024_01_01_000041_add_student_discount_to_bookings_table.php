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
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->after('room_id')->constrained('students')->nullOnDelete();
            $table->string('student_code')->nullable()->after('student_id');
            $table->decimal('original_price', 12, 2)->nullable()->after('total_price');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('original_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropColumn(['student_id', 'student_code', 'original_price', 'discount_amount']);
        });
    }
};
