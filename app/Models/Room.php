<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_type_id',
        'floor',
        'status',
        'price_override',
        'image',
    ];

    /**
     * Phòng thuộc một loại phòng.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Phòng có nhiều đặt phòng.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Booking đang hoạt động (đang ở hoặc đã xác nhận).
     */
    public function activeBooking(): HasOne
    {
        return $this->hasOne(Booking::class)
                    ->whereIn('status', ['checked_in', 'confirmed', 'booked'])
                    ->latest('check_in_date');
    }

    /**
     * Lấy giá thực tế (ưu tiên giá ghi đè, nếu không lấy giá loại phòng).
     */
    public function getEffectivePriceAttribute(): float
    {
        return $this->price_override ?? $this->roomType->base_price;
    }

    /**
     * Nhãn trạng thái tiếng Việt.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available'   => 'Trống',
            'booked'      => 'Đã đặt',
            'occupied'    => 'Đang ở',
            'maintenance' => 'Bảo trì',
            'cleaning'    => 'Dọn dẹp',
            default       => $this->status,
        };
    }
}
