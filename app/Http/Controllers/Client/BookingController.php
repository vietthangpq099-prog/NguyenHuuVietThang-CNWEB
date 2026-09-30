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
            $studentCode = trim($validated['student_code']);
            $university  = $request->input('university', 'Trường ĐH/CĐ');
            $student     = Student::where('student_code', $studentCode)->first();

            // Nếu chưa có trong hệ thống, tự động nhận diện niên khóa
            if (!$student) {
                if (preg_match('/(?:SV|sv|[A-Za-z]+)?(\d{2})/', $studentCode, $matches)) {
                    $yearPrefix = (int) $matches[1];
                    $currentYear = (int) now()->format('y');
                    $entryYear = ($yearPrefix <= $currentYear + 1) ? (2000 + $yearPrefix) : (1900 + $yearPrefix);
                    $expiryDate = Carbon::create($entryYear + 4, 9, 30);

                    if ($expiryDate->isFuture() || $expiryDate->isToday()) {
                        $student = Student::create([
                            'student_code'     => $studentCode,
                            'name'             => $validated['guest_name'],
                            'university'       => $university,
                            'phone'            => $validated['guest_phone'],
                            'email'            => $validated['guest_email'] ?? null,
                            'card_issue_date'  => Carbon::create($entryYear, 9, 1),
                            'card_expiry_date' => $expiryDate,
                            'discount_percent' => 15,
                            'status'           => 'active',
                            'notes'            => 'Tự động kích hoạt ưu đãi khóa K' . $matches[1] . ' khi đặt phòng trực tuyến',
                        ]);
                    }
                }
            }

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
     * Hỗ trợ tự động nhận diện niên khóa theo 2 chữ số đầu của MSSV.
     */
    public function checkStudentCard(Request $request, string $code)
    {
        $code       = trim($code);
        $university = trim($request->input('university', ''));
        $student    = Student::where('student_code', $code)->first();

        // Trường hợp 1: Sinh viên đã có trong cơ sở dữ liệu
        if ($student) {
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
                'university'       => $student->university ?: ($university ?: 'Đại học / Cao đẳng'),
                'expiry_date'      => $student->card_expiry_date->format('d/m/Y'),
                'discount_percent' => $student->discount_percent,
                'message'          => "Thẻ sinh viên hợp lệ! Sinh viên: {$student->name} (" . ($student->university ?: $university) . ") – Áp dụng giảm {$student->discount_percent}%!",
            ]);
        }

        // Trường hợp 2: Sinh viên mới chưa có trong hệ thống -> Tự động nhận diện qua 2 số đầu MSSV
        if (strlen($code) < 5) {
            return response()->json([
                'valid'   => false,
                'message' => 'Mã sinh viên quá ngắn hoặc không đúng định dạng. Vui lòng kiểm tra lại.',
            ], 404);
        }

        if (preg_match('/(?:SV|sv|[A-Za-z]+)?(\d{2})/', $code, $matches)) {
            $yearPrefix  = (int) $matches[1];
            $currentYear = (int) now()->format('y');
            $entryYear   = ($yearPrefix <= $currentYear + 1) ? (2000 + $yearPrefix) : (1900 + $yearPrefix);
            $gradYear    = $entryYear + 4;
            $expiryDate  = Carbon::create($gradYear, 9, 30); // Giả định hạn thẻ là 30/09 năm tốt nghiệp
            $uniName     = $university ?: 'Trường Đại học / Cao đẳng';

            if ($expiryDate->isFuture() || $expiryDate->isToday()) {
                return response()->json([
                    'valid'            => true,
                    'is_auto_detected' => true,
                    'student_code'     => $code,
                    'cohort'           => 'Khóa K' . $matches[1] . ' (Nhập học ' . $entryYear . ')',
                    'university'       => $uniName,
                    'expiry_date'      => $expiryDate->format('d/m/Y'),
                    'discount_percent' => 15,
                    'message'          => "Xác thực thành công! Khóa K{$matches[1]} ({$entryYear} – {$gradYear}) tại {$uniName} — Thẻ còn hạn đến {$expiryDate->format('d/m/Y')}. Áp dụng giảm giá 15%!",
                ]);
            } else {
                return response()->json([
                    'valid'       => false,
                    'expired'     => true,
                    'expiry_date' => $expiryDate->format('d/m/Y'),
                    'message'     => "Dựa trên MSSV khóa K{$matches[1]} ({$entryYear} – {$gradYear}) tại {$uniName}, thời hạn sinh viên dự kiến đã kết thúc vào ngày {$expiryDate->format('d/m/Y')}. Không được áp dụng ưu đãi.",
                ], 422);
            }
        }

        return response()->json([
            'valid'   => false,
            'message' => 'Không thể nhận diện khóa học từ mã sinh viên "' . $code . '". Vui lòng nhập đúng MSSV có tiền tố năm (Ví dụ: 24..., 23...).',
        ], 404);
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
