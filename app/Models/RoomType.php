<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'capacity',
        'area',
        'image',
    ];

    /**
     * Một loại phòng có nhiều phòng.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Lấy giá hiển thị đã format (VNĐ).
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->base_price, 0, ',', '.') . 'đ';
    }
}
