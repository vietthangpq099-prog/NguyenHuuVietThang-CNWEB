<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_code',
        'name',
        'university',
        'email',
        'phone',
        'card_issue_date',
        'card_expiry_date',
        'discount_percent',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'card_issue_date'  => 'date',
            'card_expiry_date' => 'date',
            'discount_percent' => 'integer',
        ];
    }

    /**
     * Một sinh viên có thể có nhiều đơn đặt phòng.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Kiểm tra thẻ sinh viên có hợp lệ và còn hạn sử dụng không.
     * Thẻ phải ở trạng thái active và ngày hết hạn >= hôm nay.
     */
    public function isValidCard(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        return $this->card_expiry_date->isFuture() || $this->card_expiry_date->isToday();
    }

    /**
     * Lấy trạng thái hạn thẻ dạng text tiếng Việt.
     */
    public function getCardStatusTextAttribute(): string
    {
        if ($this->status === 'locked') {
            return 'Thẻ bị khóa';
        }

        if ($this->status === 'graduated') {
            return 'Đã tốt nghiệp';
        }

        return $this->isValidCard() ? 'Còn hạn sử dụng' : 'Đã hết hạn';
    }

    /**
     * HTML Badge trạng thái hạn thẻ hiển thị trực quan.
     */
    public function getCardStatusBadgeAttribute(): string
    {
        if ($this->status === 'locked') {
            return '<span class="badge bg-danger"><i class="bi bi-lock me-1"></i>Bị khóa</span>';
        }

        if ($this->status === 'graduated') {
            return '<span class="badge bg-secondary"><i class="bi bi-mortarboard me-1"></i>Đã tốt nghiệp</span>';
        }

        if ($this->isValidCard()) {
            return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Còn hạn (' . $this->card_expiry_date->format('d/m/Y') . ')</span>';
        }

        return '<span class="badge bg-danger bg-opacity-75"><i class="bi bi-x-circle me-1"></i>Hết hạn (' . $this->card_expiry_date->format('d/m/Y') . ')</span>';
    }
}
