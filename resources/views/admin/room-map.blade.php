@extends('layouts.admin')

@section('title', 'Sơ đồ phòng – Radiant Hotel')

@section('content')

    {{-- ===================== TIÊU ĐỀ + CHÚ THÍCH ===================== --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-grid-3x3-gap me-2"></i>Sơ đồ phòng khách sạn</h4>
            <p class="text-muted mb-0">Cập nhật lúc: {{ now()->format('H:i – d/m/Y') }}</p>
        </div>
        <div class="mt-2 mt-md-0">
            <span class="legend-item"><span class="legend-dot available"></span>Trống</span>
            <span class="legend-item"><span class="legend-dot booked"></span>Đã đặt</span>
            <span class="legend-item"><span class="legend-dot occupied"></span>Đang ở</span>
            <span class="legend-item"><span class="legend-dot cleaning"></span>Dọn dẹp</span>
            <span class="legend-item"><span class="legend-dot maintenance"></span>Bảo trì</span>
        </div>
    </div>

    {{-- ===================== THỐNG KÊ NHANH ===================== --}}
    <div class="row g-3 mb-4">
        <div class="col-lg col-md-4 col-6">
            <div class="card stat-card bg-white">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div class="stat-number" style="font-size:1.6rem;">{{ $stats['total'] }}</div>
                        <div class="stat-label">Tổng phòng</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-6">
            <div class="card stat-card" style="background: linear-gradient(135deg, #e8f5e9, #f1f8f1);">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;background:rgba(39,174,96,0.15);color:#27ae60;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div class="stat-number" style="font-size:1.6rem;color:#27ae60;">{{ $stats['available'] }}</div>
                        <div class="stat-label">Trống</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-6">
            <div class="card stat-card" style="background: linear-gradient(135deg, #fef9e7, #fdf5e0);">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;background:rgba(243,156,18,0.15);color:#f39c12;">
                        <i class="bi bi-bookmark-fill"></i>
                    </div>
                    <div>
                        <div class="stat-number" style="font-size:1.6rem;color:#f39c12;">{{ $stats['booked'] }}</div>
                        <div class="stat-label">Đã đặt</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-6">
            <div class="card stat-card" style="background: linear-gradient(135deg, #fdedec, #fde8e6);">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;background:rgba(231,76,60,0.15);color:#e74c3c;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div class="stat-number" style="font-size:1.6rem;color:#e74c3c;">{{ $stats['occupied'] }}</div>
                        <div class="stat-label">Đang ở</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg col-md-4 col-6">
            <div class="card stat-card bg-white">
                <div class="d-flex align-items-center">
                    <div class="stat-icon me-3" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;background:rgba(149,165,166,0.15);color:#95a5a6;">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div>
                        <div class="stat-number" style="font-size:1.6rem;color:#95a5a6;">{{ $stats['maintenance'] + $stats['cleaning'] }}</div>
                        <div class="stat-label">Bảo trì/Dọn</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== SƠ ĐỒ PHÒNG THEO TẦNG ===================== --}}
    @foreach($floors as $floor => $rooms)
        <div class="floor-section">
            <div class="floor-label">
                <i class="bi bi-layers me-2"></i>Tầng {{ $floor }}
                <span class="text-muted fw-normal ms-2">({{ $rooms->count() }} phòng)</span>
            </div>
            <div class="room-grid">
                @foreach($rooms as $room)
                    <div class="room-cell status-{{ $room->status }}"
                         data-bs-toggle="modal"
                         data-bs-target="#roomModal"
                         data-room-id="{{ $room->id }}"
                         data-room-number="{{ $room->room_number }}"
                         data-room-type="{{ $room->roomType->name }}"
                         data-room-status="{{ $room->status }}"
                         data-room-status-label="{{ $room->status_label }}"
                         data-room-price="{{ number_format($room->effective_price, 0, ',', '.') }}"
                         data-room-capacity="{{ $room->roomType->capacity }}"
                         data-room-area="{{ $room->roomType->area }}"
                         data-booking-guest="{{ $room->activeBooking->guest_name ?? '' }}"
                         data-booking-phone="{{ $room->activeBooking->guest_phone ?? '' }}"
                         data-booking-checkin="{{ $room->activeBooking ? $room->activeBooking->check_in_date->format('d/m/Y') : '' }}"
                         data-booking-checkout="{{ $room->activeBooking ? $room->activeBooking->check_out_date->format('d/m/Y') : '' }}"
                         data-booking-payment="{{ $room->activeBooking->payment_status_label ?? '' }}"
                         data-booking-notes="{{ $room->activeBooking->notes ?? '' }}"
                         title="Phòng {{ $room->room_number }} – {{ $room->status_label }}">

                        <div class="room-number">{{ $room->room_number }}</div>
                        <div class="room-type-label">{{ $room->roomType->name }}</div>
                        <div class="room-status-label">{{ $room->status_label }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

    {{-- ===================== MODAL CHI TIẾT PHÒNG ===================== --}}
    <div class="modal fade modal-room-info" id="roomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                {{-- Header --}}
                <div class="modal-header text-white" id="modalHeader">
                    <h5 class="modal-title">
                        <i class="bi bi-door-open me-2"></i>
                        Phòng <span id="modalRoomNumber"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    {{-- Thông tin phòng --}}
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-info-circle me-1"></i>Thông tin phòng</h6>
                    <div class="info-row">
                        <span class="label">Loại phòng</span>
                        <span class="value" id="modalRoomType"></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Trạng thái</span>
                        <span class="value"><span class="badge" id="modalStatusBadge"></span></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Giá / đêm</span>
                        <span class="value text-primary" id="modalPrice"></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Sức chứa</span>
                        <span class="value" id="modalCapacity"></span>
                    </div>
                    <div class="info-row">
                        <span class="label">Diện tích</span>
                        <span class="value" id="modalArea"></span>
                    </div>

                    {{-- Thông tin khách (chỉ hiện khi có khách) --}}
                    <div id="guestInfoSection" class="mt-4" style="display:none;">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person me-1"></i>Thông tin khách</h6>
                        <div class="info-row">
                            <span class="label">Tên khách</span>
                            <span class="value" id="modalGuestName"></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Số điện thoại</span>
                            <span class="value" id="modalGuestPhone"></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Ngày nhận phòng</span>
                            <span class="value" id="modalCheckIn"></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Ngày trả phòng</span>
                            <span class="value" id="modalCheckOut"></span>
                        </div>
                        <div class="info-row">
                            <span class="label">Thanh toán</span>
                            <span class="value" id="modalPayment"></span>
                        </div>
                        <div class="info-row" id="notesRow" style="display:none;">
                            <span class="label">Ghi chú</span>
                            <span class="value" id="modalNotes"></span>
                        </div>
                    </div>
                </div>

                {{-- Nút thao tác nhanh --}}
                <div class="modal-footer justify-content-center flex-wrap gap-2" id="modalActions">
                    {{-- Nội dung được JS render động --}}
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const roomModal = document.getElementById('roomModal');

    roomModal.addEventListener('show.bs.modal', function (event) {
        const cell = event.relatedTarget;

        // Lấy dữ liệu từ data attributes
        const roomId      = cell.dataset.roomId;
        const roomNumber  = cell.dataset.roomNumber;
        const roomType    = cell.dataset.roomType;
        const status      = cell.dataset.roomStatus;
        const statusLabel = cell.dataset.roomStatusLabel;
        const price       = cell.dataset.roomPrice;
        const capacity    = cell.dataset.roomCapacity;
        const area        = cell.dataset.roomArea;
        const guestName   = cell.dataset.bookingGuest;
        const guestPhone  = cell.dataset.bookingPhone;
        const checkIn     = cell.dataset.bookingCheckin;
        const checkOut    = cell.dataset.bookingCheckout;
        const payment     = cell.dataset.bookingPayment;
        const notes       = cell.dataset.bookingNotes;

        // Màu header theo trạng thái
        const colors = {
            available:   'linear-gradient(135deg, #27ae60, #2ecc71)',
            booked:      'linear-gradient(135deg, #e67e22, #f39c12)',
            occupied:    'linear-gradient(135deg, #c0392b, #e74c3c)',
            maintenance: 'linear-gradient(135deg, #7f8c8d, #95a5a6)',
            cleaning:    'linear-gradient(135deg, #8e44ad, #9b59b6)'
        };

        const badgeColors = {
            available: 'bg-success', booked: 'bg-warning text-dark',
            occupied: 'bg-danger', maintenance: 'bg-secondary', cleaning: 'bg-info'
        };

        // Điền thông tin phòng
        document.getElementById('modalHeader').style.background = colors[status] || colors.available;
        document.getElementById('modalRoomNumber').textContent = roomNumber;
        document.getElementById('modalRoomType').textContent = roomType;
        document.getElementById('modalPrice').textContent = price + 'đ';
        document.getElementById('modalCapacity').textContent = capacity + ' khách';
        document.getElementById('modalArea').textContent = area + ' m²';

        const badge = document.getElementById('modalStatusBadge');
        badge.className = 'badge ' + (badgeColors[status] || 'bg-secondary');
        badge.textContent = statusLabel;

        // Thông tin khách (chỉ hiện khi đang ở hoặc đã đặt)
        const guestSection = document.getElementById('guestInfoSection');
        if (guestName && (status === 'occupied' || status === 'booked' || status === 'checked_in')) {
            guestSection.style.display = 'block';
            document.getElementById('modalGuestName').textContent = guestName;
            document.getElementById('modalGuestPhone').textContent = guestPhone;
            document.getElementById('modalCheckIn').textContent = checkIn;
            document.getElementById('modalCheckOut').textContent = checkOut;
            document.getElementById('modalPayment').textContent = payment;

            const notesRow = document.getElementById('notesRow');
            if (notes) {
                notesRow.style.display = 'flex';
                document.getElementById('modalNotes').textContent = notes;
            } else {
                notesRow.style.display = 'none';
            }
        } else {
            guestSection.style.display = 'none';
        }

        // Nút thao tác nhanh (tuỳ trạng thái)
        const actionsDiv = document.getElementById('modalActions');
        let html = '';

        switch (status) {
            case 'available':
                html = `
                    <a href="/admin/bookings/create?room=${roomId}" class="btn btn-success btn-status">
                        <i class="bi bi-calendar-plus me-1"></i>Đặt phòng
                    </a>
                    <button class="btn btn-secondary btn-status" onclick="changeStatus(${roomId}, 'maintenance')">
                        <i class="bi bi-tools me-1"></i>Bảo trì
                    </button>
                `;
                break;
            case 'booked':
                html = `
                    <button class="btn btn-success btn-status" onclick="changeStatus(${roomId}, 'occupied')">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Check-in
                    </button>
                    <button class="btn btn-danger btn-status" onclick="changeStatus(${roomId}, 'available')">
                        <i class="bi bi-x-circle me-1"></i>Huỷ đặt
                    </button>
                `;
                break;
            case 'occupied':
                html = `
                    <button class="btn btn-warning btn-status" onclick="changeStatus(${roomId}, 'cleaning')">
                        <i class="bi bi-box-arrow-right me-1"></i>Check-out
                    </button>
                `;
                break;
            case 'cleaning':
                html = `
                    <button class="btn btn-success btn-status" onclick="changeStatus(${roomId}, 'available')">
                        <i class="bi bi-check-lg me-1"></i>Hoàn tất dọn dẹp
                    </button>
                `;
                break;
            case 'maintenance':
                html = `
                    <button class="btn btn-success btn-status" onclick="changeStatus(${roomId}, 'available')">
                        <i class="bi bi-check-lg me-1"></i>Hoàn tất bảo trì
                    </button>
                `;
                break;
        }

        actionsDiv.innerHTML = html;
    });
});

/**
 * Đổi trạng thái phòng nhanh qua AJAX.
 */
function changeStatus(roomId, newStatus) {
    if (!confirm('Bạn có chắc muốn đổi trạng thái phòng?')) return;

    fetch(`/admin/rooms/${roomId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Tải lại trang để cập nhật sơ đồ
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể đổi trạng thái'));
        }
    })
    .catch(err => {
        alert('Có lỗi xảy ra. Vui lòng thử lại.');
        console.error(err);
    });
}
</script>
@endpush
