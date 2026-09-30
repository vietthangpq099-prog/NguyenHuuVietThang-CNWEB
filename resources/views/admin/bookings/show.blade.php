@extends('layouts.admin')
@section('title', 'Đặt phòng #' . $booking->id . ' – Radiant Hotel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-receipt-cutoff me-2"></i>Đặt phòng #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
    </h4>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại
    </a>
</div>

<div class="row g-4">
    {{-- CỘT TRÁI: Chi tiết booking --}}
    <div class="col-lg-8">

        {{-- Trạng thái + Nút hành động --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        @php
                            $statusColors = [
                                'pending' => 'bg-secondary', 'confirmed' => 'bg-info text-dark',
                                'checked_in' => 'bg-success', 'checked_out' => 'bg-dark', 'cancelled' => 'bg-danger',
                            ];
                            $payColors = ['unpaid' => 'bg-danger', 'partial' => 'bg-warning text-dark', 'paid' => 'bg-success'];
                        @endphp
                        <span class="badge {{ $statusColors[$booking->status] ?? 'bg-secondary' }} fs-6 me-2">
                            {{ $booking->status_label }}
                        </span>
                        <span class="badge {{ $payColors[$booking->payment_status] ?? 'bg-secondary' }} fs-6">
                            {{ $booking->payment_status_label }}
                        </span>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($booking->status === 'pending')
                            <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-info btn-sm fw-semibold"><i class="bi bi-check-lg me-1"></i>Xác nhận</button>
                            </form>
                        @endif

                        @if(in_array($booking->status, ['pending', 'confirmed']))
                            <form method="POST" action="{{ route('admin.bookings.checkin', $booking) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-success btn-sm fw-semibold"><i class="bi bi-box-arrow-in-right me-1"></i>Check-in</button>
                            </form>
                        @endif

                        @if($booking->status === 'checked_in')
                            <form method="POST" action="{{ route('admin.bookings.checkout', $booking) }}" class="d-inline"
                                  onsubmit="return confirm('Xác nhận Check-out và tạo hoá đơn?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-warning btn-sm fw-semibold"><i class="bi bi-box-arrow-right me-1"></i>Check-out & Xuất hoá đơn</button>
                            </form>
                        @endif

                        @if(!in_array($booking->status, ['checked_out', 'cancelled']))
                            <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" class="d-inline"
                                  onsubmit="return confirm('Bạn có chắc muốn huỷ đặt phòng này?')">
                                @csrf @method('PATCH')
                                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle me-1"></i>Huỷ</button>
                            </form>
                        @endif

                        @if($booking->invoice)
                            <a href="{{ route('admin.invoices.show', $booking->invoice) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-file-earmark-text me-1"></i>Xem hoá đơn
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Thông tin phòng & khách --}}
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-door-open me-1"></i>Thông tin phòng</h6>
                        <div class="mb-2"><small class="text-muted">Phòng</small>
                            <div class="fw-semibold"><span class="badge bg-primary me-1">{{ $booking->room->room_number }}</span>{{ $booking->room->roomType->name }}</div>
                        </div>
                        <div class="mb-2"><small class="text-muted">Diện tích / Sức chứa</small>
                            <div class="fw-semibold">{{ $booking->room->roomType->area }}m² – Tối đa {{ $booking->room->roomType->capacity }} khách</div>
                        </div>
                        <div><small class="text-muted">Giá / đêm</small>
                            <div class="fw-semibold text-primary">{{ number_format($booking->room->effective_price, 0, ',', '.') }}đ</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body p-4">
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person me-1"></i>Thông tin khách</h6>
                        <div class="mb-2"><small class="text-muted">Họ tên</small><div class="fw-semibold">{{ $booking->guest_name }}</div></div>
                        <div class="mb-2"><small class="text-muted">Điện thoại</small><div class="fw-semibold">{{ $booking->guest_phone }}</div></div>
                        <div class="mb-2"><small class="text-muted">Email</small><div class="fw-semibold">{{ $booking->guest_email ?? '—' }}</div></div>
                        <div><small class="text-muted">Số khách</small><div class="fw-semibold">{{ $booking->guests_count }} khách</div></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lịch trình --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <h6 class="fw-bold text-muted mb-3"><i class="bi bi-calendar-range me-1"></i>Lịch trình lưu trú</h6>
                <div class="row text-center">
                    <div class="col-4">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Nhận phòng</small>
                            <div class="fw-bold text-success fs-5">{{ $booking->check_in_date->format('d/m') }}</div>
                            <small class="text-muted">{{ $booking->check_in_date->format('Y') }}</small>
                        </div>
                    </div>
                    <div class="col-4 d-flex align-items-center justify-content-center">
                        <div>
                            <i class="bi bi-arrow-right fs-3 text-muted"></i>
                            <div class="fw-bold text-primary">{{ $booking->nights }} đêm</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-light rounded-3">
                            <small class="text-muted d-block">Trả phòng</small>
                            <div class="fw-bold text-danger fs-5">{{ $booking->check_out_date->format('d/m') }}</div>
                            <small class="text-muted">{{ $booking->check_out_date->format('Y') }}</small>
                        </div>
                    </div>
                </div>
                @if($booking->notes)
                    <div class="mt-3 p-3 bg-warning bg-opacity-10 rounded-3">
                        <i class="bi bi-chat-left-text me-1"></i><strong>Ghi chú:</strong> {{ $booking->notes }}
                    </div>
                @endif
            </div>
        </div>

        {{-- DỊCH VỤ PHỤ --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-cart-plus me-2"></i>Dịch vụ đã sử dụng</h6>
                @if($booking->status === 'checked_in')
                    <button class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#addServiceForm">
                        <i class="bi bi-plus-lg me-1"></i>Thêm dịch vụ
                    </button>
                @endif
            </div>
            <div class="card-body p-0">
                {{-- Form thêm dịch vụ (ẩn, toggle khi bấm) --}}
                @if($booking->status === 'checked_in')
                <div class="collapse p-4 bg-light border-bottom" id="addServiceForm">
                    <form method="POST" action="{{ route('admin.bookings.add-service', $booking) }}" class="row g-3 align-items-end">
                        @csrf
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Dịch vụ</label>
                            <select name="service_id" class="form-select" required>
                                <option value="">-- Chọn dịch vụ --</option>
                                @foreach($allServices as $svc)
                                    <option value="{{ $svc->id }}">{{ $svc->name }} – {{ number_format($svc->price, 0, ',', '.') }}đ</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Số lượng</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" max="99" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-plus-circle me-1"></i>Thêm
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                {{-- Bảng dịch vụ --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Dịch vụ</th>
                                <th class="text-center">SL</th>
                                <th class="text-end">Đơn giá</th>
                                <th class="text-end">Thành tiền</th>
                                @if($booking->status === 'checked_in')
                                    <th class="text-center" style="width:60px;"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($booking->services as $service)
                            <tr>
                                <td class="fw-semibold">{{ $service->name }}</td>
                                <td class="text-center">{{ $service->pivot->quantity }}</td>
                                <td class="text-end">{{ number_format($service->pivot->unit_price, 0, ',', '.') }}đ</td>
                                <td class="text-end fw-bold">{{ number_format($service->pivot->total_price, 0, ',', '.') }}đ</td>
                                @if($booking->status === 'checked_in')
                                <td class="text-center">
                                    <form method="POST" action="{{ route('admin.bookings.remove-service', [$booking, $service]) }}"
                                          onsubmit="return confirm('Xoá dịch vụ này?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm p-1"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Chưa có dịch vụ nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- CỘT PHẢI: Tóm tắt tài chính --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top:80px;">
            <div class="card-header bg-dark text-white py-3">
                <h6 class="mb-0"><i class="bi bi-calculator me-2"></i>Tổng kết tài chính</h6>
            </div>
            <div class="card-body p-4">
                @php
                    $nights       = $booking->nights;
                    $roomTotal    = $booking->original_price ?? ($booking->room->effective_price * $nights);
                    $discountVal  = $booking->discount_amount ?? 0;
                    $serviceTotal = $booking->services->sum('pivot.total_price');
                    $subtotal     = max(0, $roomTotal - $discountVal) + $serviceTotal;
                    $tax          = round($subtotal * 0.1);
                    $grandTotal   = $subtotal + $tax;
                @endphp

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tiền phòng ({{ $nights }} đêm)</span>
                    <span class="fw-semibold">{{ number_format($roomTotal, 0, ',', '.') }}đ</span>
                </div>

                @if($discountVal > 0)
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span><i class="bi bi-mortarboard-fill me-1"></i>Ưu đãi sinh viên ({{ $booking->student_code }})</span>
                    <span class="fw-bold">-{{ number_format($discountVal, 0, ',', '.') }}đ</span>
                </div>
                @endif

                @if($serviceTotal > 0)
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Dịch vụ phụ</span>
                    <span class="fw-semibold">{{ number_format($serviceTotal, 0, ',', '.') }}đ</span>
                </div>
                @endif

                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tạm tính</span>
                    <span class="fw-semibold">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Thuế VAT (10%)</span>
                    <span class="fw-semibold">{{ number_format($tax, 0, ',', '.') }}đ</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold fs-5">Tổng cộng</span>
                    <span class="fw-bold fs-5 text-primary">{{ number_format($grandTotal, 0, ',', '.') }}đ</span>
                </div>
            </div>
        </div>

        {{-- THẺ THANH TOÁN VIETQR & ĐỐI SOÁT DÀNH CHO LỄ TÂN --}}
        <div class="card border-0 shadow-sm rounded-3 mt-4" style="border-top: 4px solid #2980b9 !important;">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-qr-code-scan me-1"></i>Thanh toán VietQR
                    </h6>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success small">Napas 247</span>
                </div>
            </div>
            <div class="card-body p-4 text-center">
                <div class="p-2 border rounded-3 bg-light d-inline-block mb-3">
                    <img src="{{ $depositQrUrl }}" alt="Mã VietQR" class="rounded" style="max-width: 170px; height: auto;">
                </div>
                <div class="small text-muted mb-3">
                    STK: <strong class="text-primary">{{ $bankDetails['account_no'] }}</strong> ({{ $bankDetails['bank_name'] }})<br>
                    Cú pháp: <span class="badge bg-light text-dark border">RADIANT DP{{ $booking->id }} {{ substr(preg_replace('/[^0-9]/', '', $booking->guest_phone ?? ''), -4) }}</span>
                </div>

                {{-- Nút xem QR to / gửi khách --}}
                <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-3" data-bs-toggle="modal" data-bs-target="#adminQrModal">
                    <i class="bi bi-arrows-fullscreen me-1"></i>Mở mã QR to cho khách quét
                </button>

                <hr class="my-3">

                {{-- Cập nhật trạng thái thanh toán nhanh --}}
                <label class="form-label small fw-semibold text-muted d-block text-start">Cập nhật trạng thái thanh toán:</label>
                <form method="POST" action="{{ route('admin.bookings.update-payment', $booking) }}">
                    @csrf @method('PATCH')
                    <div class="input-group input-group-sm mb-2">
                        <select name="payment_status" class="form-select">
                            <option value="unpaid" {{ $booking->payment_status === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                            <option value="partial" {{ $booking->payment_status === 'partial' ? 'selected' : '' }}>Đã đặt cọc 50%</option>
                            <option value="paid" {{ $booking->payment_status === 'paid' ? 'selected' : '' }}>Đã thanh toán đủ</option>
                        </select>
                        <button class="btn btn-primary" type="submit">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL XEM MÃ VIETQR TO TRONG ADMIN --}}
<div class="modal fade" id="adminQrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-primary">
                    <i class="bi bi-qr-code-scan me-2"></i>Mã thanh toán VietQR – Đặt phòng #{{ $booking->id }}
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="btn-group w-100 mb-3" role="group">
                    <button type="button" class="btn btn-outline-primary active btn-sm" id="adminBtnDep" onclick="toggleAdminQr('deposit')">
                        Cọc 50% ({{ number_format($depositAmount, 0, ',', '.') }}đ)
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="adminBtnFull" onclick="toggleAdminQr('full')">
                        100% ({{ number_format($booking->total_price, 0, ',', '.') }}đ)
                    </button>
                </div>
                <div class="p-3 border rounded-3 bg-white shadow-sm d-inline-block mb-3">
                    <img id="adminQrImg" src="{{ $depositQrUrl }}" alt="VietQR Đặt phòng" style="max-width: 250px; width: 100%; height: auto;">
                </div>
                <div class="text-start bg-light p-3 rounded-3 small">
                    <div class="mb-1"><strong>Ngân hàng:</strong> {{ $bankDetails['bank_name'] }}</div>
                    <div class="mb-1"><strong>Số tài khoản:</strong> <span class="fw-bold text-primary">{{ $bankDetails['account_no'] }}</span></div>
                    <div class="mb-1"><strong>Tên thụ hưởng:</strong> {{ $bankDetails['account_name'] }}</div>
                    <div><strong>Nội dung CK:</strong> <span class="badge bg-white text-dark border">RADIANT DP{{ $booking->id }} {{ substr(preg_replace('/[^0-9]/', '', $booking->guest_phone ?? ''), -4) }}</span></div>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const qrDep = @json($depositQrUrl);
    const qrFull = @json($fullQrUrl);

    function toggleAdminQr(type) {
        const btnDep = document.getElementById('adminBtnDep');
        const btnFull = document.getElementById('adminBtnFull');
        const img = document.getElementById('adminQrImg');

        if (type === 'deposit') {
            btnDep.classList.add('active');
            btnFull.classList.remove('active');
            img.src = qrDep;
        } else {
            btnFull.classList.add('active');
            btnDep.classList.remove('active');
            img.src = qrFull;
        }
    }
</script>
@endpush
@endsection
