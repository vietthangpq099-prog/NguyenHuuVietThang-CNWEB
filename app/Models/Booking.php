<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'check_in_date',
        'check_out_date',
        'guests_count',
        'status',
        'payment_status',
        'total_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date'  => 'date',
            'check_out_date' => 'date',
        ];
    }

    /**
     * Đặt phòng thuộc một tài khoản khách hàng (nullable).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Đặt phòng thuộc một phòng.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Đặt phòng có nhiều dịch vụ qua bảng trung gian.
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'booking_service')
                    ->withPivot('quantity', 'unit_price', 'total_price')
                    ->withTimestamps();
    }

    /**
     * Đặt phòng có một hoá đơn.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Tính số đêm lưu trú.
     */
    public function getNightsAttribute(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }

    /**
     * Nhãn trạng thái đặt phòng (tiếng Việt).
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'      => 'Chờ xác nhận',
            'confirmed'    => 'Đã xác nhận',
            'checked_in'   => 'Đã nhận phòng',
            'checked_out'  => 'Đã trả phòng',
            'cancelled'    => 'Đã huỷ',
            default        => $this->status,
        };
    }

    /**
     * Nhãn trạng thái thanh toán (tiếng Việt).
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid'  => 'Chưa thanh toán',
            'partial' => 'Thanh toán một phần',
            'paid'    => 'Đã thanh toán',
            default   => $this->payment_status,
        };
    }
}
