@extends('layouts.admin')

@section('title', 'Quản lý đặt phòng – Radiant Hotel')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2"></i>Quản lý đặt phòng</h4>
        <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tạo đặt phòng mới
        </a>
    </div>

    {{-- Bộ lọc --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Trạng thái</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Đang ở</option>
                        <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Đã trả phòng</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã huỷ</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Từ ngày</label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Đến ngày</label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-funnel me-1"></i>Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bảng danh sách --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Khách hàng</th>
                            <th>Phòng</th>
                            <th>Nhận phòng</th>
                            <th>Trả phòng</th>
                            <th>Số đêm</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                            <th class="text-end">Tổng tiền</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td class="text-muted">{{ $booking->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $booking->guest_name }}</div>
                                <small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $booking->guest_phone }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $booking->room->room_number }}</span>
                                <br><small class="text-muted">{{ $booking->room->roomType->name }}</small>
                            </td>
                            <td>{{ $booking->check_in_date->format('d/m/Y') }}</td>
                            <td>{{ $booking->check_out_date->format('d/m/Y') }}</td>
                            <td><span class="badge bg-light text-dark">{{ $booking->nights }} đêm</span></td>
                            <td>
                                @php
                                    $colors = [
                                        'pending' => 'bg-secondary', 'confirmed' => 'bg-info text-dark',
                                        'checked_in' => 'bg-success', 'checked_out' => 'bg-dark',
                                        'cancelled' => 'bg-danger',
                                    ];
                                @endphp
                                <span class="badge {{ $colors[$booking->status] ?? 'bg-secondary' }}">{{ $booking->status_label }}</span>
                            </td>
                            <td>
                                @php
                                    $payColors = ['unpaid' => 'text-danger', 'partial' => 'text-warning', 'paid' => 'text-success'];
                                @endphp
                                <span class="fw-semibold {{ $payColors[$booking->payment_status] ?? '' }}">
                                    <i class="bi bi-{{ $booking->payment_status === 'paid' ? 'check-circle' : ($booking->payment_status === 'partial' ? 'exclamation-circle' : 'x-circle') }} me-1"></i>
                                    {{ $booking->payment_status_label }}
                                </span>
                            </td>
                            <td class="text-end fw-bold">{{ number_format($booking->total_price, 0, ',', '.') }}đ</td>
                            <td class="text-center">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                Chưa có đơn đặt phòng nào.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="mt-3">
        {{ $bookings->withQueryString()->links() }}
    </div>

@endsection
