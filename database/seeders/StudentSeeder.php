<?php

namespace Database\Seeders;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();

        $students = [
            [
                'student_code'     => 'SV202401',
                'name'             => 'Nguyễn Hữu Việt Thắng',
                'university'       => 'Đại học Khoa học Tự nhiên (CNWEB)',
                'email'            => 'vietthangpq099@gmail.com',
                'phone'            => '0901234567',
                'card_issue_date'  => $today->copy()->subYears(2),
                'card_expiry_date' => $today->copy()->addYears(2), // Còn hạn đến 2028 (hợp lệ)
                'discount_percent' => 15,
                'status'           => 'active',
                'notes'            => 'Sinh viên tiêu biểu - Ưu đãi giảm giá 15%',
            ],
            [
                'student_code'     => 'SV202402',
                'name'             => 'Trần Thị Thu Thảo',
                'university'       => 'Đại học Bách Khoa TP.HCM',
                'email'            => 'thuthao.bk@gmail.com',
                'phone'            => '0912345678',
                'card_issue_date'  => $today->copy()->subYear(),
                'card_expiry_date' => $today->copy()->addYear(), // Còn hạn đến 2027 (hợp lệ)
                'discount_percent' => 20,
                'status'           => 'active',
                'notes'            => 'Thẻ sinh viên VIP - Ưu đãi giảm giá 20%',
            ],
            [
                'student_code'     => 'SV202299',
                'name'             => 'Lê Hoàng Nam',
                'university'       => 'Đại học Kinh Tế TP.HCM',
                'email'            => 'hoangnam.ueh@gmail.com',
                'phone'            => '0987654321',
                'card_issue_date'  => $today->copy()->subYears(5),
                'card_expiry_date' => $today->copy()->subMonths(6), // Đã hết hạn 6 tháng trước
                'discount_percent' => 15,
                'status'           => 'active',
                'notes'            => 'Thẻ sinh viên đã hết hạn sử dụng',
            ],
            [
                'student_code'     => 'SV202403',
                'name'             => 'Phạm Minh Tuấn',
                'university'       => 'Đại học Sư Phạm Kỹ Thuật',
                'email'            => 'minhtuan.hcmute@gmail.com',
                'phone'            => '0933221100',
                'card_issue_date'  => $today->copy()->subMonths(8),
                'card_expiry_date' => $today->copy()->addMonths(16), // Còn hạn (hợp lệ)
                'discount_percent' => 10,
                'status'           => 'active',
                'notes'            => 'Ưu đãi sinh viên 10%',
            ],
        ];

        foreach ($students as $student) {
            Student::updateOrCreate(
                ['student_code' => $student['student_code']],
                $student
            );
        }
    }
}
