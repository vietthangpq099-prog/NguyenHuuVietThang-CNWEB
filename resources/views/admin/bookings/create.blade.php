@extends('layouts.admin')
@section('title', 'Tạo đặt phòng – Radiant Hotel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-calendar-plus me-2"></i>Tạo đặt phòng mới (Tại quầy)</h4>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<div class="row g-4">
    {{-- Form --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <div><i class="bi bi-exclamation-triangle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.bookings.store') }}" id="adminBookingForm">
                    @csrf

                    {{-- Chọn phòng --}}
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-door-open me-1"></i>Chọn phòng</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Loại phòng</label>
                            <select id="filterRoomType" class="form-select">
                                <option value="">Tất cả loại phòng</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }} – {{ number_format($type->base_price, 0, ',', '.') }}đ/đêm</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phòng <span class="text-danger">*</span></label>
                            <select name="room_id" id="roomSelect" class="form-select" required>
                                <option value="">-- Chọn phòng --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}"
                                            data-type="{{ $room->room_type_id }}"
                                            data-price="{{ $room->effective_price }}"
                                            data-capacity="{{ $room->roomType->capacity }}"
                                            data-info="{{ $room->roomType->name }} | {{ $room->roomType->area }}m² | Tối đa {{ $room->roomType->capacity }} khách"
                                            {{ (old('room_id', $selectedRoom->id ?? '') == $room->id) ? 'selected' : '' }}>
                                        {{ $room->room_number }} – {{ $room->roomType->name }} ({{ number_format($room->effective_price, 0, ',', '.') }}đ)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Thông tin khách --}}
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person me-1"></i>Thông tin khách hàng</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Họ tên <span class="text-danger">*</span></label>
                            <input type="text" name="guest_name" class="form-control" value="{{ old('guest_name') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="tel" name="guest_phone" class="form-control" value="{{ old('guest_phone') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="guest_email" class="form-control" value="{{ old('guest_email') }}">
                        </div>
                    </div>

                    {{-- Thời gian --}}
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-calendar-range me-1"></i>Thời gian lưu trú</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngày nhận phòng <span class="text-danger">*</span></label>
                            <input type="date" name="check_in_date" id="adminCheckIn" class="form-control"
                                   value="{{ old('check_in_date', now()->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngày trả phòng <span class="text-danger">*</span></label>
                            <input type="date" name="check_out_date" id="adminCheckOut" class="form-control"
                                   value="{{ old('check_out_date', now()->addDay()->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Số khách <span class="text-danger">*</span></label>
                            <input type="number" name="guests_count" class="form-control" value="{{ old('guests_count', 1) }}" min="1" required>
                        </div>
                    </div>

                    {{-- Ghi chú --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold"><i class="bi bi-chat-left-text me-1"></i>Ghi chú</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100 fw-semibold">
                        <i class="bi bi-check-circle me-1"></i>Tạo đặt phòng & Xác nhận
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Panel tóm tắt giá --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top:80px;">
            <div class="card-header bg-dark text-white py-3">
                <h6 class="mb-0"><i class="bi bi-calculator me-2"></i>Tóm tắt đơn giá</h6>
            </div>
            <div class="card-body p-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Phòng</span>
                    <span class="fw-semibold" id="summaryRoom">—</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Giá / đêm</span>
                    <span class="fw-semibold" id="summaryPrice">—</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Số đêm</span>
                    <span class="fw-semibold" id="summaryNights">—</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold fs-5">Tổng</span>
                    <span class="fw-bold fs-5 text-primary" id="summaryTotal">—</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomSelect   = document.getElementById('roomSelect');
    const filterType   = document.getElementById('filterRoomType');
    const checkInEl    = document.getElementById('adminCheckIn');
    const checkOutEl   = document.getElementById('adminCheckOut');

    // Lọc phòng theo loại
    filterType.addEventListener('change', function() {
        const typeId = this.value;
        Array.from(roomSelect.options).forEach(opt => {
            if (!opt.value) return;
            opt.style.display = (!typeId || opt.dataset.type === typeId) ? '' : 'none';
        });
        roomSelect.value = '';
        updateSummary();
    });

    // Cập nhật tóm tắt
    function updateSummary() {
        const opt = roomSelect.selectedOptions[0];
        if (!opt || !opt.value) {
            document.getElementById('summaryRoom').textContent = '—';
            document.getElementById('summaryPrice').textContent = '—';
            document.getElementById('summaryNights').textContent = '—';
            document.getElementById('summaryTotal').textContent = '—';
            return;
        }
        const price    = parseInt(opt.dataset.price);
        const checkIn  = new Date(checkInEl.value);
        const checkOut = new Date(checkOutEl.value);
        const nights   = Math.max(1, Math.round((checkOut - checkIn) / 86400000));
        const total    = price * nights;

        document.getElementById('summaryRoom').textContent = opt.text.split('–')[0].trim();
        document.getElementById('summaryPrice').textContent = price.toLocaleString('vi-VN') + 'đ';
        document.getElementById('summaryNights').textContent = nights + ' đêm';
        document.getElementById('summaryTotal').textContent = total.toLocaleString('vi-VN') + 'đ';
    }

    roomSelect.addEventListener('change', updateSummary);
    checkInEl.addEventListener('change', updateSummary);
    checkOutEl.addEventListener('change', updateSummary);
    updateSummary();
});
</script>
@endpush
