<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomMapController extends Controller
{
    /**
     * Sơ đồ phòng trực quan – Room Grid theo tầng.
     */
    public function index()
    {
        // Lấy tất cả phòng, eager load loại phòng + booking đang hoạt động
        $rooms = Room::with(['roomType', 'activeBooking'])
                     ->orderBy('room_number')
                     ->get();

        // Nhóm theo tầng
        $floors = $rooms->groupBy('floor')->sortKeys();

        // Thống kê
        $stats = [
            'total'       => $rooms->count(),
            'available'   => $rooms->where('status', 'available')->count(),
            'booked'      => $rooms->where('status', 'booked')->count(),
            'occupied'    => $rooms->where('status', 'occupied')->count(),
            'maintenance' => $rooms->where('status', 'maintenance')->count(),
            'cleaning'    => $rooms->where('status', 'cleaning')->count(),
        ];

        return view('admin.room-map', compact('floors', 'stats'));
    }

    /**
     * Đổi trạng thái phòng nhanh (AJAX).
     */
    public function updateStatus(Request $request, Room $room)
    {
        $request->validate([
            'status' => 'required|in:available,booked,occupied,maintenance,cleaning',
        ]);

        $room->update(['status' => $request->status]);

        // Nếu check-out (từ occupied → cleaning), cập nhật booking
        if ($request->status === 'cleaning' && $room->activeBooking) {
            $room->activeBooking->update([
                'status'         => 'checked_out',
                'payment_status' => 'paid',
            ]);
        }

        // Nếu check-in (từ booked → occupied), cập nhật booking
        if ($request->status === 'occupied' && $room->activeBooking) {
            $room->activeBooking->update(['status' => 'checked_in']);
        }

        return response()->json([
            'success' => true,
            'message' => "Phòng {$room->room_number} đã chuyển sang: {$room->status_label}",
        ]);
    }
}
