<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Student;
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
            'student_code'   => 'nullable|string|max:50',
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

        // Tính tổng tiền gốc
        $nights        = Carbon::parse($validated['check_in_date'])->diffInDays(Carbon::parse($validated['check_out_date']));
        $originalPrice = $room->effective_price * $nights;
        $discountAmount = 0;
        $studentId      = null;
        $appliedStudentCode = null;

        // Xử lý kiểm tra thẻ sinh viên và giảm giá
        if (!empty($validated['student_code'])) {
            $student = Student::where('student_code', trim($validated['student_code']))->first();
            if ($student && $student->isValidCard()) {
                $studentId          = $student->id;
                $appliedStudentCode = $student->student_code;
                $discountAmount     = round($originalPrice * ($student->discount_percent / 100));
            }
        }

        $finalPrice = max(0, $originalPrice - $discountAmount);

        $booking = Booking::create([
            'user_id'         => Auth::id(),
            'room_id'         => $room->id,
            'student_id'      => $studentId,
            'student_code'    => $appliedStudentCode,
            'guest_name'      => $validated['guest_name'],
            'guest_phone'     => $validated['guest_phone'],
            'guest_email'     => $validated['guest_email'] ?? null,
            'check_in_date'   => $validated['check_in_date'],
            'check_out_date'  => $validated['check_out_date'],
            'guests_count'    => $validated['guests_count'],
            'status'          => 'pending',
            'payment_status'  => 'unpaid',
            'total_price'     => $finalPrice,
            'original_price'  => $originalPrice,
            'discount_amount' => $discountAmount,
            'notes'           => $validated['notes'] ?? null,
        ]);

        return redirect()->route('booking.confirmation', $booking)->with('success', 'Đặt phòng thành công!');
    }

    /**
     * API kiểm tra thẻ sinh viên khi khách nhập mã trên form.
     */
    public function checkStudentCard(string $code)
    {
        $code = trim($code);
        $student = Student::where('student_code', $code)->first();

        if (!$student) {
            return response()->json([
                'valid'   => false,
                'message' => 'Không tìm thấy thẻ sinh viên với mã "' . $code . '" trong hệ thống.',
            ], 404);
        }

        if (!$student->isValidCard()) {
            $reason = 'Thẻ sinh viên đã hết hạn vào ngày ' . $student->card_expiry_date->format('d/m/Y');
            if ($student->status === 'locked') {
                $reason = 'Thẻ sinh viên hiện đang bị khóa tạm thời';
            } elseif ($student->status === 'graduated') {
                $reason = 'Sinh viên đã tốt nghiệp, không còn hiệu lực ưu đãi';
            }

            return response()->json([
                'valid'       => false,
                'expired'     => true,
                'name'        => $student->name,
                'expiry_date' => $student->card_expiry_date->format('d/m/Y'),
                'message'     => "{$reason}. Không thể áp dụng giảm giá ưu đãi.",
            ], 422);
        }

        return response()->json([
            'valid'            => true,
            'student_code'     => $student->student_code,
            'name'             => $student->name,
            'university'       => $student->university ?: 'Đại học / Cao đẳng',
            'expiry_date'      => $student->card_expiry_date->format('d/m/Y'),
            'discount_percent' => $student->discount_percent,
            'message'          => "Thẻ sinh viên hợp lệ! Sinh viên {$student->name} ({$student->university}) – Được giảm giá {$student->discount_percent}% tổng tiền phòng!",
        ]);
    }

    /**
     * Trang xác nhận đặt phòng (Client).
     */
    public function confirmation(Booking $booking)
    {
        $booking->load(['room.roomType', 'student']);

        $depositAmount = round($booking->total_price * 0.5);
        $fullQrUrl     = \App\Services\VietQrService::generateBookingQrUrl($booking, $booking->total_price);
        $depositQrUrl  = \App\Services\VietQrService::generateBookingQrUrl($booking, $depositAmount);
        $bankDetails   = \App\Services\VietQrService::getBankDetails();

        return view('client.booking.confirmation', compact(
            'booking',
            'fullQrUrl',
            'depositQrUrl',
            'depositAmount',
            'bankDetails'
        ));
    }
}
