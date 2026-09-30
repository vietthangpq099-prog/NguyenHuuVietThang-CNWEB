<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoomMapController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ROUTES CÔNG KHAI (Client)
|--------------------------------------------------------------------------
*/

// Trang chủ + Danh sách phòng
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/rooms', [HomeController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [HomeController::class, 'show'])->name('rooms.show');

// Đặt phòng (Client)
Route::get('/booking/create', [ClientBookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [ClientBookingController::class, 'store'])->name('booking.store');
Route::get('/booking/{booking}/confirmation', [ClientBookingController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/api/check-student/{code}', [ClientBookingController::class, 'checkStudentCard'])->name('api.check-student');

/*
|--------------------------------------------------------------------------
| XÁC THỰC (Authentication)
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| ROUTES ADMIN / LỄ TÂN (yêu cầu đăng nhập + phân quyền Role)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'role:admin,receptionist'])->name('admin.')->group(function () {

    // Dashboard tổng quan
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Sơ đồ phòng trực quan
    Route::get('/room-map', [RoomMapController::class, 'index'])->name('room-map');
    Route::patch('/rooms/{room}/status', [RoomMapController::class, 'updateStatus'])->name('rooms.update-status');

    // ──── Quản lý phòng & hạng phòng ────
    Route::resource('rooms', RoomController::class);
    Route::resource('room-types', RoomTypeController::class);

    // ──── Quản lý danh mục dịch vụ phụ ────
    Route::resource('services', ServiceController::class);

    // ──── Quản lý đặt phòng ────
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [AdminBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');

    // Thao tác trạng thái booking (Check-in, Check-out, Xác nhận, Huỷ)
    Route::patch('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::patch('/bookings/{booking}/checkin', [AdminBookingController::class, 'checkin'])->name('bookings.checkin');
    Route::patch('/bookings/{booking}/checkout', [AdminBookingController::class, 'checkout'])->name('bookings.checkout');
    Route::patch('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::patch('/bookings/{booking}/payment', [AdminBookingController::class, 'updatePayment'])->name('bookings.update-payment');

    // Gắn / huỷ dịch vụ phụ cho khách đang ở
    Route::post('/bookings/{booking}/services', [AdminBookingController::class, 'addService'])->name('bookings.add-service');
    Route::delete('/bookings/{booking}/services/{service}', [AdminBookingController::class, 'removeService'])->name('bookings.remove-service');

    // ──── Quản lý hoá đơn & Thanh toán ────
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::patch('/invoices/{invoice}/paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::patch('/invoices/{invoice}/cancel', [InvoiceController::class, 'markCancelled'])->name('invoices.mark-cancelled');

    // ──── Quản lý sinh viên & ưu đãi thẻ sinh viên ────
    Route::resource('students', StudentController::class);
});
