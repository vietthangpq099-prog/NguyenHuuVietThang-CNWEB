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

                        {{-- Ưu đãi Thẻ sinh viên --}}
                        <div class="card border border-primary bg-primary bg-opacity-10 rounded-3 p-3 mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-mortarboard-fill text-primary fs-5 me-2"></i>
                                <h6 class="fw-bold text-primary mb-0">Bạn là sinh viên? Nhận ngay ưu đãi giảm 15% tiền phòng!</h6>
                            </div>
                            <p class="text-muted small mb-3">Chọn trường và nhập mã sinh viên (MSSV) để hệ thống tự động nhận diện khóa học, kiểm tra hạn thẻ và kích hoạt giảm giá.</p>
                            
                            <div class="row g-2 mb-2">
                                <div class="col-md-6 col-12">
                                    <label class="form-label small fw-semibold text-dark mb-1">
                                        <i class="bi bi-building me-1 text-primary"></i>Trường Đại học / Cao đẳng
                                    </label>
                                    <select name="university" id="universitySelect" class="form-select form-select-sm">
                                        <option value="ĐH Khoa học Tự nhiên (HCMUS)">ĐH Khoa học Tự nhiên TP.HCM (HCMUS)</option>
                                        <option value="ĐH Bách Khoa TP.HCM (HCMUT)">ĐH Bách Khoa TP.HCM (HCMUT)</option>
                                        <option value="ĐH Công nghệ Thông tin (UIT)">ĐH Công nghệ Thông tin (UIT)</option>
                                        <option value="ĐH Kinh tế TP.HCM (UEH)">ĐH Kinh tế TP.HCM (UEH)</option>
                                        <option value="ĐH Sư phạm Kỹ thuật (HCMUTE)">ĐH Sư phạm Kỹ thuật (HCMUTE)</option>
                                        <option value="ĐH Quốc tế (IU)">ĐH Quốc tế (IU)</option>
                                        <option value="ĐH FPT">ĐH FPT</option>
                                        <option value="ĐH Giao thông Vận tải">ĐH Giao thông Vận tải</option>
                                        <option value="ĐH Ngoại thương (FTU)">ĐH Ngoại thương (FTU)</option>
                                        <option value="ĐH Tôn Đức Thắng (TDTU)">ĐH Tôn Đức Thắng (TDTU)</option>
                                        <option value="Trường Đại học / Cao đẳng khác">Trường Đại học / Cao đẳng khác</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label small fw-semibold text-dark mb-1">
                                        <i class="bi bi-person-badge me-1 text-primary"></i>Mã số sinh viên (MSSV)
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="student_code" id="studentCodeInput" class="form-control"
                                               value="{{ old('student_code') }}"
                                               placeholder="Ví dụ: 2451220102">
                                        <button type="button" class="btn btn-primary" id="btnCheckStudent" onclick="verifyStudentCard()">
                                            <i class="bi bi-shield-check me-1"></i>Kiểm tra thẻ
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted fst-italic" style="font-size: 0.8rem;">
                                💡 Mẹo: Hệ thống tự động tính hạn thẻ theo 2 số đầu MSSV (VD: <strong>24...</strong> là K24 tốt nghiệp 2028).
                            </small>
                            <div id="studentFeedback" class="mt-2" style="display: none;"></div>
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
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tiền phòng gốc</span>
                        <span class="fw-semibold" id="originalPriceDisplay">{{ number_format($totalPrice, 0, ',', '.') }}đ</span>
                    </div>

                    {{-- Dòng ưu đãi sinh viên --}}
                    <div class="d-flex justify-content-between mb-2 text-success" id="studentDiscountRow" style="display: none !important;">
                        <span><i class="bi bi-mortarboard-fill me-1"></i>Ưu đãi sinh viên (<span id="discountPercentText">0%</span>)</span>
                        <span class="fw-bold" id="discountAmountDisplay">-0đ</span>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-5">Tổng thanh toán</span>
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
// Tự động cập nhật số đêm & tổng tiền khi thay đổi ngày và mã giảm giá sinh viên
const pricePerNight = {{ $room->effective_price }};
const checkInEl     = document.getElementById('checkIn');
const checkOutEl    = document.getElementById('checkOut');
let currentDiscountPercent = 0;

function updatePrice() {
    const checkIn  = new Date(checkInEl.value);
    const checkOut = new Date(checkOutEl.value);
    if (checkIn && checkOut && checkOut > checkIn) {
        const nights = Math.round((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        const originalTotal = pricePerNight * nights;
        const discountAmount = Math.round(originalTotal * (currentDiscountPercent / 100));
        const finalTotal = Math.max(0, originalTotal - discountAmount);

        document.getElementById('nightsDisplay').textContent = nights + ' đêm';
        document.getElementById('originalPriceDisplay').textContent = originalTotal.toLocaleString('vi-VN') + 'đ';

        const discountRow = document.getElementById('studentDiscountRow');
        if (currentDiscountPercent > 0) {
            discountRow.style.setProperty('display', 'flex', 'important');
            document.getElementById('discountPercentText').textContent = currentDiscountPercent + '%';
            document.getElementById('discountAmountDisplay').textContent = '-' + discountAmount.toLocaleString('vi-VN') + 'đ';
        } else {
            discountRow.style.setProperty('display', 'none', 'important');
        }

        document.getElementById('totalDisplay').textContent = finalTotal.toLocaleString('vi-VN') + 'đ';
    }
}

function verifyStudentCard() {
    const codeInput = document.getElementById('studentCodeInput');
    const uniSelect = document.getElementById('universitySelect');
    const feedback = document.getElementById('studentFeedback');
    const btn = document.getElementById('btnCheckStudent');
    const code = codeInput.value.trim();
    const uni  = uniSelect.value;

    if (!code) {
        feedback.style.display = 'block';
        feedback.innerHTML = '<div class="alert alert-warning py-2 px-3 small mb-0"><i class="bi bi-exclamation-circle me-1"></i>Vui lòng nhập mã số thẻ sinh viên.</div>';
        currentDiscountPercent = 0;
        updatePrice();
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Đang kiểm tra...';
    feedback.style.display = 'none';

    fetch('{{ url("/api/check-student") }}/' + encodeURIComponent(code) + '?university=' + encodeURIComponent(uni))
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(result => {
            feedback.style.display = 'block';
            if (result.status === 200 && result.body.valid) {
                currentDiscountPercent = result.body.discount_percent;
                const studentTitle = result.body.name ? `Sinh viên: <b>${result.body.name}</b>` : `MSSV: <b>${result.body.student_code}</b>`;
                const cohortInfo = result.body.cohort ? `<span class="badge bg-primary me-1">${result.body.cohort}</span>` : '';
                feedback.innerHTML = `
                    <div class="alert alert-success py-2 px-3 small mb-0 border-success">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        <strong>Thẻ hợp lệ!</strong> ${studentTitle} (${result.body.university})<br>
                        ${cohortInfo}Hạn thẻ: <span class="badge bg-success">${result.body.expiry_date}</span> — Áp dụng giảm giá <b>${result.body.discount_percent}%</b>!
                    </div>
                `;
            } else if (result.status === 422) {
                // Thẻ đã hết hạn
                currentDiscountPercent = 0;
                feedback.innerHTML = `
                    <div class="alert alert-danger py-2 px-3 small mb-0 border-danger">
                        <i class="bi bi-x-circle-fill me-1"></i>
                        <strong>Thẻ hết hạn!</strong> ${result.body.message}
                    </div>
                `;
            } else {
                // Không tìm thấy hoặc sai định dạng
                currentDiscountPercent = 0;
                feedback.innerHTML = `
                    <div class="alert alert-danger py-2 px-3 small mb-0 border-danger">
                        <i class="bi bi-question-circle-fill me-1"></i>
                        ${result.body.message || 'Mã thẻ không hợp lệ.'}
                    </div>
                `;
            }
            updatePrice();
        })
        .catch(err => {
            feedback.style.display = 'block';
            feedback.innerHTML = '<div class="alert alert-danger py-2 px-3 small mb-0">Lỗi kết nối máy chủ khi kiểm tra thẻ.</div>';
            currentDiscountPercent = 0;
            updatePrice();
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-shield-check me-1"></i>Kiểm tra thẻ';
        });
}

checkInEl.addEventListener('change', function() {
    const next = new Date(this.value);
    next.setDate(next.getDate() + 1);
    checkOutEl.min = next.toISOString().split('T')[0];
    if (new Date(checkOutEl.value) <= new Date(this.value)) {
        checkOutEl.value = next.toISOString().split('T')[0];
    }
    updatePrice();
});

checkOutEl.addEventListener('change', updatePrice);

// Tự động kiểm tra thẻ nếu người dùng nhập sẵn từ trước
document.getElementById('studentCodeInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        verifyStudentCard();
    }
});
</script>
@endpush
