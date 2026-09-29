<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Hiển thị form đặt phòng (Client).
     */
    public function create(Request $request)
    {
        $room = Room::with('roomType')->findOrFail($request->room);

        $checkIn  = $request->check_in  ?? now()->format('Y-m-d');
        $checkOut = $request->check_out ?? now()->addDay()->format('Y-m-d');

        $nights = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
        $nights = max($nights, 1);
        $totalPrice = $room->effective_price * $nights;

        return view('client.booking.create', compact('room', 'checkIn', 'checkOut', 'nights', 'totalPrice'));
    }

    /**
     * Lưu đơn đặt phòng mới (Client).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'        => 'required|exists:rooms,id',
            'guest_name'     => 'required|string|max:255',
            'guest_phone'    => 'required|string|max:20',
            'guest_email'    => 'nullable|email|max:255',
            'check_in_date'  => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests_count'   => 'required|integer|min:1',
            'notes'          => 'nullable|string|max:500',
        ]);

        $room = Room::with('roomType')->findOrFail($validated['room_id']);

        // Kiểm tra phòng còn khả dụng trong khoảng ngày
        $conflict = Booking::where('room_id', $room->id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in_date', '<', $validated['check_out_date'])
            ->where('check_out_date', '>', $validated['check_in_date'])
            ->exists();

        if ($conflict) {
            return back()->withInput()->withErrors(['room_id' => 'Phòng đã được đặt trong khoảng ngày này. Vui lòng chọn ngày khác.']);
        }

        // Kiểm tra sức chứa
        if ($validated['guests_count'] > $room->roomType->capacity) {
            return back()->withInput()->withErrors(['guests_count' => "Phòng này chỉ chứa tối đa {$room->roomType->capacity} khách."]);
        }

        // Tính tổng tiền
        $nights     = Carbon::parse($validated['check_in_date'])->diffInDays(Carbon::parse($validated['check_out_date']));
        $totalPrice = $room->effective_price * $nights;

        $booking = Booking::create([
            'user_id'        => Auth::id(),
            'room_id'        => $room->id,
            'guest_name'     => $validated['guest_name'],
            'guest_phone'    => $validated['guest_phone'],
            'guest_email'    => $validated['guest_email'] ?? null,
            'check_in_date'  => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'guests_count'   => $validated['guests_count'],
            'status'         => 'pending',
            'payment_status' => 'unpaid',
            'total_price'    => $totalPrice,
            'notes'          => $validated['notes'] ?? null,
        ]);

        return redirect()->route('booking.confirmation', $booking)->with('success', 'Đặt phòng thành công!');
    }

    /**
     * Trang xác nhận đặt phòng (Client).
     */
    public function confirmation(Booking $booking)
    {
        $booking->load('room.roomType');
        return view('client.booking.confirmation', compact('booking'));
    }
}
