<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Trang tổng quan Admin – có biểu đồ doanh thu.
     */
    public function index()
    {
        $today     = Carbon::today();
        $startMonth = $today->copy()->startOfMonth();
        $endMonth   = $today->copy()->endOfMonth();

        // === THỐNG KÊ CHÍNH ===
        $stats = [
            'totalRooms'      => Room::count(),
            'availableRooms'  => Room::where('status', 'available')->count(),
            'occupiedRooms'   => Room::where('status', 'occupied')->count(),
            'todayCheckins'   => Booking::where('status', 'checked_in')
                                        ->whereDate('check_in_date', $today)->count(),
            'todayCheckouts'  => Booking::where('status', 'checked_out')
                                        ->whereDate('check_out_date', $today)->count(),
            'pendingBookings' => Booking::where('status', 'pending')->count(),
            'monthlyRevenue'  => Invoice::where('status', 'paid')
                                        ->whereBetween('issued_at', [$startMonth, $endMonth])
                                        ->sum('total'),
            'todayRevenue'    => Invoice::where('status', 'paid')
                                        ->whereDate('issued_at', $today)
                                        ->sum('total'),
            'occupancyRate'   => Room::count() > 0
                                    ? round(Room::where('status', 'occupied')->count() / Room::count() * 100)
                                    : 0,
        ];

        // === DOANH THU 7 NGÀY GẦN NHẤT (cho biểu đồ cột) ===
        $revenueByDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $date  = $today->copy()->subDays($i);
            $label = $date->format('d/m');
            $amount = Invoice::where('status', 'paid')
                             ->whereDate('issued_at', $date)
                             ->sum('total');
            $revenueByDay[] = ['label' => $label, 'amount' => (int) $amount];
        }

        // === DOANH THU 6 THÁNG GẦN NHẤT (cho biểu đồ đường) ===
        $revenueByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = $today->copy()->subMonths($i)->startOfMonth();
            $monthEnd   = $today->copy()->subMonths($i)->endOfMonth();
            $label      = $monthStart->format('m/Y');
            $amount     = Invoice::where('status', 'paid')
                                 ->whereBetween('issued_at', [$monthStart, $monthEnd])
                                 ->sum('total');
            $revenueByMonth[] = ['label' => $label, 'amount' => (int) $amount];
        }

        // === TRẠNG THÁI PHÒNG (biểu đồ tròn) ===
        $roomStatuses = Room::select('status', DB::raw('count(*) as count'))
                            ->groupBy('status')
                            ->pluck('count', 'status')
                            ->toArray();

        // === BOOKING THEO TRẠNG THÁI (biểu đồ ngang) ===
        $bookingStatuses = Booking::select('status', DB::raw('count(*) as count'))
                                  ->groupBy('status')
                                  ->pluck('count', 'status')
                                  ->toArray();

        // === ĐẶT PHÒNG GẦN ĐÂY ===
        $recentBookings = Booking::with(['room.roomType'])
                                 ->latest()
                                 ->take(8)
                                 ->get();

        // === ĐẶT PHÒNG SẮP CHECK-IN HÔM NAY ===
        $upcomingCheckins = Booking::with(['room.roomType'])
                                   ->whereIn('status', ['confirmed', 'pending'])
                                   ->whereDate('check_in_date', $today)
                                   ->get();

        return view('admin.dashboard', compact(
            'stats', 'revenueByDay', 'revenueByMonth',
            'roomStatuses', 'bookingStatuses',
            'recentBookings', 'upcomingCheckins'
        ));
    }
}
