<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Room;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Danh sách đặt phòng (Admin/Lễ tân).
     */
    public function index(Request $request)
    {
        $query = Booking::with(['room.roomType'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('from')) {
            $query->whereDate('check_in_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('check_out_date', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%")
                  ->orWhereHas('room', fn($r) => $r->where('room_number', 'like', "%{$search}%"));
            });
        }

        $bookings = $query->paginate(15);
        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Form tạo đặt phòng mới (Admin/Lễ tân – tại quầy).
     */
    public function create(Request $request)
    {
        $rooms     = Room::with('roomType')->where('status', 'available')->orderBy('room_number')->get();
        $roomTypes = \App\Models\RoomType::orderBy('base_price')->get();
        $selectedRoom = $request->room ? Room::with('roomType')->find($request->room) : null;

        return view('admin.bookings.create', compact('rooms', 'roomTypes', 'selectedRoom'));
    }

    /**
     * Lưu đặt phòng mới (Admin/Lễ tân).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'        => 'required|exists:rooms,id',
            'guest_name'     => 'required|string|max:255',
            'guest_phone'    => 'required|string|max:20',
            'guest_email'    => 'nullable|email|max:255',
            'check_in_date'  => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests_count'   => 'required|integer|min:1',
            'notes'          => 'nullable|string|max:1000',
        ]);

        $room = Room::with('roomType')->findOrFail($validated['room_id']);

        // Kiểm tra trùng lịch
        $conflict = Booking::where('room_id', $room->id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in_date', '<', $validated['check_out_date'])
            ->where('check_out_date', '>', $validated['check_in_date'])
            ->exists();

        if ($conflict) {
            return back()->withInput()->withErrors(['room_id' => 'Phòng đã có lịch đặt trong khoảng ngày này.']);
        }

        $nights     = Carbon::parse($validated['check_in_date'])->diffInDays(Carbon::parse($validated['check_out_date']));
        $totalPrice = $room->effective_price * $nights;

        $booking = Booking::create([
            'user_id'        => null, // đặt tại quầy
            'room_id'        => $room->id,
            'guest_name'     => $validated['guest_name'],
            'guest_phone'    => $validated['guest_phone'],
            'guest_email'    => $validated['guest_email'] ?? null,
            'check_in_date'  => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'guests_count'   => $validated['guests_count'],
            'status'         => 'confirmed',
            'payment_status' => 'unpaid',
            'total_price'    => $totalPrice,
            'notes'          => $validated['notes'] ?? null,
        ]);

        // Cập nhật trạng thái phòng
        $room->update(['status' => 'booked']);

        return redirect()->route('admin.bookings.show', $booking)
                         ->with('success', "Đặt phòng {$room->room_number} thành công cho khách {$booking->guest_name}!");
    }

    /**
     * Chi tiết đặt phòng (Admin).
     */
    public function show(Booking $booking)
    {
        $booking->load(['room.roomType', 'services', 'invoice.items', 'user']);
        $allServices = Service::where('is_active', true)->orderBy('name')->get();

        $fullQrUrl     = \App\Services\VietQrService::generateBookingQrUrl($booking, $booking->total_price);
        $depositAmount = round($booking->total_price * 0.5);
        $depositQrUrl  = \App\Services\VietQrService::generateBookingQrUrl($booking, $depositAmount);
        $bankDetails   = \App\Services\VietQrService::getBankDetails();

        return view('admin.bookings.show', compact('booking', 'allServices', 'fullQrUrl', 'depositQrUrl', 'depositAmount', 'bankDetails'));
    }

    /**
     * Cập nhật trạng thái thanh toán (unpaid, partial, paid).
     */
    public function updatePayment(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,partial,paid',
        ]);

        $booking->update(['payment_status' => $validated['payment_status']]);

        return back()->with('success', "Đã cập nhật trạng thái thanh toán đặt phòng #{$booking->id} sang {$booking->payment_status_label}.");
    }

    /**
     * Xác nhận đặt phòng (pending → confirmed).
     */
    public function confirm(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể xác nhận đặt phòng đang chờ.');
        }

        $booking->update(['status' => 'confirmed']);
        $booking->room->update(['status' => 'booked']);

        return back()->with('success', "Đã xác nhận đặt phòng #{$booking->id}.");
    }

    /**
     * Check-in: confirmed/booked → checked_in.
     */
    public function checkin(Booking $booking)
    {
        if (!in_array($booking->status, ['confirmed', 'pending'])) {
            return back()->with('error', 'Không thể check-in đặt phòng này.');
        }

        $booking->update(['status' => 'checked_in']);
        $booking->room->update(['status' => 'occupied']);

        return back()->with('success', "Check-in thành công! Khách {$booking->guest_name} đã nhận phòng {$booking->room->room_number}.");
    }

    /**
     * Check-out: checked_in → checked_out + Tạo hoá đơn.
     */
    public function checkout(Booking $booking)
    {
        if ($booking->status !== 'checked_in') {
            return back()->with('error', 'Chỉ có thể check-out phòng đang ở.');
        }

        $booking->load(['room.roomType', 'services']);

        // Tính tổng tiền phòng
        $nights    = $booking->check_in_date->diffInDays($booking->check_out_date);
        $roomTotal = $booking->room->effective_price * $nights;

        // Tính tổng tiền dịch vụ
        $serviceTotal = $booking->services->sum('pivot.total_price');

        // Tạm tính & thuế
        $subtotal = $roomTotal + $serviceTotal;
        $tax      = round($subtotal * 0.1); // VAT 10%
        $total    = $subtotal + $tax;

        // Cập nhật tổng tiền booking
        $booking->update([
            'status'         => 'checked_out',
            'payment_status' => 'paid',
            'total_price'    => $subtotal,
        ]);

        // Cập nhật phòng
        $booking->room->update(['status' => 'cleaning']);

        // Tạo hoá đơn
        $invoiceNumber = 'INV-' . now()->format('Y') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'booking_id'     => $booking->id,
            'invoice_number' => $invoiceNumber,
            'issued_at'      => now(),
            'due_date'       => now(),
            'subtotal'       => $subtotal,
            'tax'            => $tax,
            'total'          => $total,
            'status'         => 'paid',
        ]);

        // Mục chi tiết: Tiền phòng
        InvoiceItem::create([
            'invoice_id'  => $invoice->id,
            'description' => "Phòng {$booking->room->room_number} ({$booking->room->roomType->name}) × {$nights} đêm",
            'unit_price'  => $booking->room->effective_price,
            'quantity'    => $nights,
            'line_total'  => $roomTotal,
        ]);

        // Mục chi tiết: Từng dịch vụ
        foreach ($booking->services as $service) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $service->name . ' × ' . $service->pivot->quantity,
                'unit_price'  => $service->pivot->unit_price,
                'quantity'    => $service->pivot->quantity,
                'line_total'  => $service->pivot->total_price,
            ]);
        }

        return redirect()->route('admin.invoices.show', $invoice)
                         ->with('success', "Check-out thành công! Hoá đơn {$invoiceNumber} đã được tạo.");
    }

    /**
     * Thêm dịch vụ vào đặt phòng đang ở.
     */
    public function addService(Request $request, Booking $booking)
    {
        if ($booking->status !== 'checked_in') {
            return back()->with('error', 'Chỉ có thể thêm dịch vụ khi khách đang ở.');
        }

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $service    = Service::findOrFail($validated['service_id']);
        $quantity   = $validated['quantity'];
        $totalPrice = $service->price * $quantity;

        // Kiểm tra đã có dịch vụ này chưa → cộng dồn
        $existing = $booking->services()->where('service_id', $service->id)->first();

        if ($existing) {
            $newQty   = $existing->pivot->quantity + $quantity;
            $newTotal = $service->price * $newQty;
            $booking->services()->updateExistingPivot($service->id, [
                'quantity'    => $newQty,
                'total_price' => $newTotal,
            ]);
        } else {
            $booking->services()->attach($service->id, [
                'quantity'    => $quantity,
                'unit_price'  => $service->price,
                'total_price' => $totalPrice,
            ]);
        }

        return back()->with('success', "Đã thêm {$quantity}× {$service->name} ({$this->formatVnd($totalPrice)}).");
    }

    /**
     * Xoá dịch vụ khỏi đặt phòng.
     */
    public function removeService(Booking $booking, Service $service)
    {
        $booking->services()->detach($service->id);
        return back()->with('success', "Đã xoá dịch vụ {$service->name}.");
    }

    /**
     * Huỷ đặt phòng.
     */
    public function cancel(Booking $booking)
    {
        if (in_array($booking->status, ['checked_out', 'cancelled'])) {
            return back()->with('error', 'Không thể huỷ đặt phòng này.');
        }

        $booking->update(['status' => 'cancelled']);

        // Trả phòng về trống nếu đang booked/occupied
        if (in_array($booking->room->status, ['booked', 'occupied'])) {
            $booking->room->update(['status' => 'available']);
        }

        return back()->with('success', "Đã huỷ đặt phòng #{$booking->id}.");
    }

    /**
     * Format VNĐ.
     */
    private function formatVnd($amount): string
    {
        return number_format($amount, 0, ',', '.') . 'đ';
    }
}
