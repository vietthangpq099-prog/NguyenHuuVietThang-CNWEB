<?php

namespace Tests\Unit;

use App\Models\Student;
use Carbon\Carbon;
use Tests\TestCase;

class StudentTest extends TestCase
{
    public function test_card_is_valid_when_future_expiry_and_active(): void
    {
        $student = new Student([
            'status'           => 'active',
            'card_expiry_date' => Carbon::today()->addDays(30),
            'discount_percent' => 15,
        ]);

        $this->assertTrue($student->isValidCard());
        $this->assertEquals('Còn hạn sử dụng', $student->card_status_text);
    }

    public function test_card_is_invalid_when_past_expiry(): void
    {
        $student = new Student([
            'status'           => 'active',
            'card_expiry_date' => Carbon::yesterday(),
            'discount_percent' => 15,
        ]);

        $this->assertFalse($student->isValidCard());
        $this->assertEquals('Đã hết hạn', $student->card_status_text);
    }

    public function test_card_is_invalid_when_status_is_locked_or_graduated(): void
    {
        $studentLocked = new Student([
            'status'           => 'locked',
            'card_expiry_date' => Carbon::today()->addDays(30),
        ]);
        $this->assertFalse($studentLocked->isValidCard());
        $this->assertEquals('Thẻ bị khóa', $studentLocked->card_status_text);

        $studentGraduated = new Student([
            'status'           => 'graduated',
            'card_expiry_date' => Carbon::today()->addDays(30),
        ]);
        $this->assertFalse($studentGraduated->isValidCard());
        $this->assertEquals('Đã tốt nghiệp', $studentGraduated->card_status_text);
    }
}
