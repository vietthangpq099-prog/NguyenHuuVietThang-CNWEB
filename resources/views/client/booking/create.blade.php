@extends('layouts.app')
@section('title', 'Đặt phòng ' . $room->room_number . ' – Radiant Hotel')

@section('content')
<div class="container py-5">
    <div class="row g-4">

        {{-- CỘT TRÁI: Form đặt phòng --}}
        <div class="col-lg-7">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}">Phòng</a></li>
                    <li class="breadcrumb-item active">Đặt phòng {{ $room->room_number }}</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4 py-3">
                    <h5 class="mb-0"><i class="bi bi-calendar-plus me-2"></i>Thông tin đặt phòng</h5>
                </div>
                <div class="card-body p-4">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('booking.store') }}" id="bookingForm">
                        @csrf
                        <input type="hidden" name="room_id" value="{{ $room->id }}">

                        {{-- Thông tin khách --}}
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person me-1"></i>Thông tin khách hàng</h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ tên <span class="text-danger">*</span></label>
                                <input type="text" name="guest_name" class="form-control"
                                       value="{{ old('guest_name', auth()->user()->name ?? '') }}"
                                       placeholder="Nguyễn Văn A" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="guest_phone" class="form-control"
                                       value="{{ old('guest_phone', auth()->user()->phone ?? '') }}"
                                       placeholder="0901234567" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="guest_email" class="form-control"
                                       value="{{ old('guest_email', auth()->user()->email ?? '') }}"
                                       placeholder="email@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số khách <span class="text-danger">*</span></label>
                                <select name="guests_count" class="form-select" required>
                                    @for($i = 1; $i <= $room->roomType->capacity; $i++)
                                        <option value="{{ $i }}" {{ old('guests_count', 1) == $i ? 'selected' : '' }}>
                                            {{ $i }} khách
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- Ngày nhận/trả phòng --}}
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-calendar-range me-1"></i>Thời gian lưu trú</h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ngày nhận phòng <span class="text-danger">*</span></label>
                                <input type="date" name="check_in_date" id="checkIn" class="form-control"
                                       value="{{ old('check_in_date', $checkIn) }}"
                                       min="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ngày trả phòng <span class="text-danger">*</span></label>
                                <input type="date" name="check_out_date" id="checkOut" class="form-control"
                                       value="{{ old('check_out_date', $checkOut) }}"
                                       min="{{ now()->addDay()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        {{-- Ghi chú --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold"><i class="bi bi-chat-left-text me-1"></i>Ghi chú (tuỳ chọn)</label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Yêu cầu đặc biệt: phòng yên tĩnh, giường phụ, trang trí...">{{ old('notes') }}</textarea>
                        </div>

                        {{-- Nút đặt --}}
                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-semibold">
                            <i class="bi bi-check-circle me-1"></i>Xác nhận đặt phòng
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: Tóm tắt phòng & giá --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 90px;">
                <img src="{{ $room->image ?? $room->roomType->image }}" class="card-img-top rounded-top-4"
                     alt="Phòng {{ $room->room_number }}" style="height: 220px; object-fit: cover;">

                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold mb-0" style="color:#1a5276;">Phòng {{ $room->room_number }}</h5>
                        <span class="badge bg-primary">{{ $room->roomType->name }}</span>
                    </div>

                    <p class="text-muted small mb-3">
                        <i class="bi bi-arrows-angle-expand me-1"></i>{{ $room->roomType->area }}m²
                        <span class="mx-1">•</span>
                        <i class="bi bi-people me-1"></i>Tối đa {{ $room->roomType->capacity }} khách
                        <span class="mx-1">•</span>
                        Tầng {{ $room->floor }}
                    </p>

                    {{-- Tiện nghi --}}
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-dark border"><i class="bi bi-wifi me-1"></i>Wi-Fi</span>
                        <span class="badge bg-light text-dark border"><i class="bi bi-snow me-1"></i>Điều hoà</span>
                        <span class="badge bg-light text-dark border"><i class="bi bi-tv me-1"></i>TV</span>
                        @if(in_array($room->roomType->name, ['Deluxe', 'Suite']))
                            <span class="badge bg-light text-dark border"><i class="bi bi-droplet me-1"></i>Bồn tắm</span>
                            <span class="badge bg-light text-dark border"><i class="bi bi-cup-hot me-1"></i>Minibar</span>
                        @endif
                        @if($room->roomType->name === 'Suite')
                            <span class="badge bg-light text-dark border"><i class="bi bi-music-note me-1"></i>Âm thanh</span>
                        @endif
                    </div>

                    <hr>

                    {{-- Chi tiết giá --}}
                    <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-1"></i>Chi tiết giá</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Giá phòng / đêm</span>
                        <span class="fw-semibold">{{ number_format($room->effective_price, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Số đêm</span>
                        <span class="fw-semibold" id="nightsDisplay">{{ $nights }} đêm</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-5">Tổng cộng</span>
                        <span class="fw-bold fs-5" style="color: #e67e22;" id="totalDisplay">
                            {{ number_format($totalPrice, 0, ',', '.') }}đ
                        </span>
                    </div>
                    <small class="text-muted d-block mt-1">(Chưa bao gồm dịch vụ phụ và thuế VAT 10%)</small>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
// Tự động cập nhật số đêm & tổng tiền khi thay đổi ngày
const pricePerNight = {{ $room->effective_price }};
const checkInEl     = document.getElementById('checkIn');
const checkOutEl    = document.getElementById('checkOut');

function updatePrice() {
    const checkIn  = new Date(checkInEl.value);
    const checkOut = new Date(checkOutEl.value);
    if (checkIn && checkOut && checkOut > checkIn) {
        const nights = Math.round((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        const total  = pricePerNight * nights;
        document.getElementById('nightsDisplay').textContent = nights + ' đêm';
        document.getElementById('totalDisplay').textContent =
            total.toLocaleString('vi-VN') + 'đ';
    }
}

checkInEl.addEventListener('change', function() {
    // Đặt min cho ngày trả = ngày nhận + 1
    const next = new Date(this.value);
    next.setDate(next.getDate() + 1);
    checkOutEl.min = next.toISOString().split('T')[0];
    if (new Date(checkOutEl.value) <= new Date(this.value)) {
        checkOutEl.value = next.toISOString().split('T')[0];
    }
    updatePrice();
});

checkOutEl.addEventListener('change', updatePrice);
</script>
@endpush
