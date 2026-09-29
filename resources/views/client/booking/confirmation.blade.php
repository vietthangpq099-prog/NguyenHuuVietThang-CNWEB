@extends('layouts.app')
@section('title', 'Xác nhận đặt phòng – Radiant Hotel')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            {{-- Thông báo thành công --}}
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle mb-3" style="width:80px;height:80px;">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:3rem;"></i>
                </div>
                <h3 class="fw-bold text-success">Đặt phòng thành công!</h3>
                <p class="text-muted">Đơn đặt phòng của bạn đã được ghi nhận. Chúng tôi sẽ liên hệ xác nhận trong thời gian sớm nhất.</p>
            </div>

            {{-- Chi tiết đặt phòng --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2"></i>Chi tiết đặt phòng</h5>
                        <span class="badge bg-warning text-dark fs-6">{{ $booking->status_label }}</span>
                    </div>
                </div>
                <div class="card-body p-4">

                    {{-- Mã đặt phòng --}}
                    <div class="text-center py-3 mb-4 bg-light rounded-3">
                        <small class="text-muted d-block">Mã đặt phòng</small>
                        <h3 class="fw-bold text-primary mb-0">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</h3>
                    </div>

                    <div class="row g-4">
                        {{-- Thông tin phòng --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-door-open me-1"></i>Thông tin phòng</h6>
                            <div class="mb-2">
                                <small class="text-muted">Phòng</small>
                                <div class="fw-semibold">{{ $booking->room->room_number }} – {{ $booking->room->roomType->name }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Giá / đêm</small>
                                <div class="fw-semibold">{{ number_format($booking->room->effective_price, 0, ',', '.') }}đ</div>
                            </div>
                        </div>

                        {{-- Thời gian --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-3"><i class="bi bi-calendar-range me-1"></i>Thời gian lưu trú</h6>
                            <div class="mb-2">
                                <small class="text-muted">Nhận phòng</small>
                                <div class="fw-semibold">{{ $booking->check_in_date->format('d/m/Y') }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Trả phòng</small>
                                <div class="fw-semibold">{{ $booking->check_out_date->format('d/m/Y') }}</div>
                            </div>
                            <div>
                                <small class="text-muted">Số đêm</small>
                                <div class="fw-semibold">{{ $booking->nights }} đêm</div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Thông tin khách --}}
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-person me-1"></i>Thông tin khách hàng</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <small class="text-muted">Họ tên</small>
                            <div class="fw-semibold">{{ $booking->guest_name }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Số điện thoại</small>
                            <div class="fw-semibold">{{ $booking->guest_phone }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Số khách</small>
                            <div class="fw-semibold">{{ $booking->guests_count }} khách</div>
                        </div>
                    </div>

                    @if($booking->notes)
                    <div class="mt-3">
                        <small class="text-muted">Ghi chú</small>
                        <div class="fw-semibold">{{ $booking->notes }}</div>
                    </div>
                    @endif

                    <hr>

                    {{-- Tổng tiền --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">Tổng tiền phòng</span>
                        <span class="fw-bold fs-4" style="color:#e67e22;">{{ number_format($booking->total_price, 0, ',', '.') }}đ</span>
                    </div>
                    <small class="text-muted">(Chưa bao gồm dịch vụ phụ và thuế VAT 10%)</small>
                </div>

                <div class="card-footer bg-white border-top p-4">
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('home') }}" class="btn btn-outline-primary">
                            <i class="bi bi-house me-1"></i>Về trang chủ
                        </a>
                    </div>
                </div>
            </div>

            {{-- Lưu ý --}}
            <div class="alert alert-info mt-4 rounded-3">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Lưu ý:</strong> Đặt phòng của bạn đang ở trạng thái <strong>"Chờ xác nhận"</strong>.
                Nhân viên sẽ liên hệ qua số điện thoại <strong>{{ $booking->guest_phone }}</strong> để xác nhận.
                Vui lòng mang theo CMND/CCCD khi check-in.
            </div>
        </div>
    </div>
</div>
@endsection
