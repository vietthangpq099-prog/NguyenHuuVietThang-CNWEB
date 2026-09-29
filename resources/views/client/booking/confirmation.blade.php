@extends('layouts.app')
@section('title', 'Xác nhận đặt phòng – Radiant Hotel')

@push('styles')
<style>
    .qr-container {
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
        border: 2px dashed #93c5fd;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
    }
    .qr-container:hover {
        border-color: var(--primary);
        box-shadow: 0 8px 25px rgba(41, 128, 185, 0.15);
    }
    .qr-image-wrapper {
        background: white;
        padding: 12px;
        border-radius: 14px;
        display: inline-block;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        max-width: 100%;
    }
    .qr-image-wrapper img {
        max-width: 260px;
        width: 100%;
        height: auto;
        border-radius: 8px;
    }
    .copy-btn {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .copy-btn:hover {
        background: var(--primary);
        color: white;
    }
    .btn-pay-option.active {
        background-color: var(--primary) !important;
        color: white !important;
        border-color: var(--primary) !important;
    }
</style>
@endpush

@section('content')
<div class="container py-5">

    {{-- Thông báo chúc mừng --}}
    <div class="text-center mb-5">
        <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3 shadow-sm" style="width:76px;height:76px;">
            <i class="bi bi-check-circle-fill text-success" style="font-size:2.8rem;"></i>
        </div>
        <h2 class="fw-bold text-success mb-2">Đặt phòng thành công!</h2>
        <p class="text-muted fs-6">Đơn đặt phòng của quý khách đã được ghi nhận vào hệ thống Radiant Hotel.</p>
    </div>

    <div class="row g-4 justify-content-center">

        {{-- CỘT TRÁI (6/12): CHI TIẾT ĐẶT PHÒNG --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-receipt me-2"></i>Phiếu thông tin đặt phòng</h5>
                        <span class="badge bg-warning text-dark fs-6">{{ $booking->status_label }}</span>
                    </div>
                </div>
                <div class="card-body p-4">

                    {{-- Mã đặt phòng --}}
                    <div class="text-center py-3 mb-4 bg-light rounded-3 border">
                        <small class="text-muted d-block text-uppercase fw-semibold" style="letter-spacing: 1px;">Mã đặt phòng</small>
                        <h3 class="fw-bold text-primary mb-0">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</h3>
                    </div>

                    {{-- Thông tin phòng --}}
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-door-open me-2 text-primary"></i>Thông tin phòng</h6>
                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Phòng / Hạng:</span>
                            <span class="fw-bold text-primary">Phòng {{ $booking->room->room_number }} – {{ $booking->room->roomType->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Đơn giá:</span>
                            <span class="fw-semibold">{{ number_format($booking->room->effective_price, 0, ',', '.') }}đ / đêm</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Nhận phòng:</span>
                            <span class="fw-semibold">{{ $booking->check_in_date->format('d/m/Y') }} (từ 14:00)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Trả phòng:</span>
                            <span class="fw-semibold">{{ $booking->check_out_date->format('d/m/Y') }} (trước 12:00)</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Thời gian lưu trú:</span>
                            <span class="badge bg-primary px-3 py-1">{{ $booking->nights }} đêm</span>
                        </div>
                    </div>

                    {{-- Thông tin khách hàng --}}
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person me-2 text-primary"></i>Thông tin khách lưu trú</h6>
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="p-2 border rounded-2">
                                <small class="text-muted d-block">Họ và tên</small>
                                <span class="fw-semibold">{{ $booking->guest_name }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-2">
                                <small class="text-muted d-block">Số điện thoại</small>
                                <span class="fw-semibold">{{ $booking->guest_phone }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-2">
                                <small class="text-muted d-block">Số lượng khách</small>
                                <span class="fw-semibold">{{ $booking->guests_count }} người</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-2">
                                <small class="text-muted d-block">Email</small>
                                <span class="fw-semibold text-truncate d-block">{{ $booking->guest_email ?: 'Không có' }}</span>
                            </div>
                        </div>
                    </div>

                    @if($booking->notes)
                    <div class="alert alert-secondary small mb-4 py-2">
                        <strong>Ghi chú:</strong> {{ $booking->notes }}
                    </div>
                    @endif

                    {{-- Tổng tiền --}}
                    <div class="p-3 rounded-3" style="background: #fef9e7; border: 1px solid #f9e79f;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">Tổng tiền phòng:</span>
                            <span class="fw-bold fs-4 text-danger">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small text-muted">
                            <span>Mức tiền đặt cọc tối thiểu (50%):</span>
                            <span class="fw-bold text-primary">{{ number_format($depositAmount, 0, ',', '.') }}đ</span>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-1"></i>Về trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI (6/12): THANH TOÁN VIETQR QUÉT MÃ TỨC THÌ --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100" style="border-top: 5px solid #2980b9 !important;">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-qr-code-scan me-2"></i>Thanh toán qua VietQR
                        </h5>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                            <i class="bi bi-shield-lock-fill me-1"></i>Napas 247 Chuẩn Quốc Gia
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">

                    {{-- Tùy chọn thanh toán: Đặt cọc 50% hoặc Trả đủ 100% --}}
                    <label class="form-label small fw-semibold text-muted mb-2">Chọn phương thức thanh toán chuyển khoản:</label>
                    <div class="btn-group w-100 mb-3" role="group">
                        <button type="button" class="btn btn-outline-primary btn-pay-option active py-2" id="btnDeposit" onclick="selectPayOption('deposit')">
                            <i class="bi bi-pie-chart-fill me-1"></i>Đặt cọc 50%<br>
                            <strong>{{ number_format($depositAmount, 0, ',', '.') }}đ</strong>
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-pay-option py-2" id="btnFull" onclick="selectPayOption('full')">
                            <i class="bi bi-wallet2 me-1"></i>Thanh toán đủ 100%<br>
                            <strong>{{ number_format($booking->total_price, 0, ',', '.') }}đ</strong>
                        </button>
                    </div>

                    {{-- Khung ảnh mã QR VietQR --}}
                    <div class="qr-container mb-3">
                        <div class="qr-image-wrapper">
                            <img id="vietQrImg" src="{{ $depositQrUrl }}" alt="Mã VietQR Radiant Hotel">
                        </div>
                        <div class="mt-2">
                            <small class="text-muted d-block">
                                <i class="bi bi-phone me-1 text-primary"></i>Quét bằng mọi ứng dụng Ngân hàng hoặc MoMo, ZaloPay
                            </small>
                            <a id="downloadQrLink" href="{{ $depositQrUrl }}" target="_blank" class="btn btn-sm btn-link text-decoration-none mt-1">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Mở ảnh mã QR to
                            </a>
                        </div>
                    </div>

                    {{-- Thông tin chuyển khoản chi tiết kèm nút copy --}}
                    <div class="bg-light p-3 rounded-3 border mb-3">
                        <h6 class="fw-bold text-dark mb-2 small text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="bi bi-info-circle me-1 text-primary"></i>Thông tin chuyển khoản thủ công
                        </h6>

                        {{-- Ngân hàng --}}
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <span class="small text-muted">Ngân hàng:</span>
                            <span class="fw-bold text-dark">{{ $bankDetails['bank_name'] }}</span>
                        </div>

                        {{-- Số tài khoản --}}
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <span class="small text-muted">Số tài khoản:</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-primary fs-6" id="txtAccountNo">{{ $bankDetails['account_no'] }}</span>
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 copy-btn" onclick="copyText('txtAccountNo', 'Số tài khoản')">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Chủ tài khoản --}}
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <span class="small text-muted">Chủ tài khoản:</span>
                            <span class="fw-semibold text-dark">{{ $bankDetails['account_name'] }}</span>
                        </div>

                        {{-- Số tiền --}}
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <span class="small text-muted">Số tiền:</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-danger fs-6" id="txtAmount">{{ number_format($depositAmount, 0, ',', '.') }}đ</span>
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 copy-btn" onclick="copyText('txtAmountVal', 'Số tiền')">
                                    <i class="bi bi-copy"></i>
                                </button>
                                <input type="hidden" id="txtAmountVal" value="{{ $depositAmount }}">
                            </div>
                        </div>

                        {{-- Nội dung chuyển khoản --}}
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <span class="small text-muted">Nội dung CK:</span>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $booking->guest_phone ?? '');
                                    $memo = 'RADIANT DP' . $booking->id . ' ' . substr($cleanPhone, -4);
                                @endphp
                                <span class="fw-bold text-success fs-6" id="txtMemo">{{ $memo }}</span>
                                <button type="button" class="btn btn-sm btn-light border py-0 px-2 copy-btn" onclick="copyText('txtMemo', 'Nội dung CK')">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Thông báo xác nhận --}}
                    <div class="alert alert-info py-2 px-3 small rounded-3 mb-0">
                        <i class="bi bi-clock-history me-1"></i>
                        Hệ thống sẽ tự động đối soát và xác nhận đơn phòng của quý khách sau 1-3 phút kể từ khi nhận chuyển khoản thành công.
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const depositUrl = @json($depositQrUrl);
    const fullUrl    = @json($fullQrUrl);
    const depositVal = {{ $depositAmount }};
    const fullVal    = {{ $booking->total_price }};

    function selectPayOption(type) {
        const btnDep = document.getElementById('btnDeposit');
        const btnFull = document.getElementById('btnFull');
        const img = document.getElementById('vietQrImg');
        const downloadLink = document.getElementById('downloadQrLink');
        const txtAmount = document.getElementById('txtAmount');
        const txtAmountVal = document.getElementById('txtAmountVal');

        if (type === 'deposit') {
            btnDep.classList.add('active');
            btnFull.classList.remove('active');
            img.src = depositUrl;
            downloadLink.href = depositUrl;
            txtAmount.textContent = depositVal.toLocaleString('vi-VN') + 'đ';
            txtAmountVal.value = depositVal;
        } else {
            btnFull.classList.add('active');
            btnDep.classList.remove('active');
            img.src = fullUrl;
            downloadLink.href = fullUrl;
            txtAmount.textContent = fullVal.toLocaleString('vi-VN') + 'đ';
            txtAmountVal.value = fullVal;
        }
    }

    function copyText(elementId, label) {
        const el = document.getElementById(elementId);
        let text = el.value !== undefined ? el.value : el.innerText;
        text = text.replace(/đ/g, '').trim();

        navigator.clipboard.writeText(text).then(() => {
            alert('Đã sao chép ' + label + ': ' + text);
        }).catch(() => {
            alert('Vui lòng sao chép thủ công: ' + text);
        });
    }
</script>
@endpush
