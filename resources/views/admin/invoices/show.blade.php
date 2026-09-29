@extends('layouts.admin')
@section('title', 'Hoá đơn ' . $invoice->invoice_number . ' – Radiant Hotel')

@push('styles')
<style>
    @media print {
        .sidebar, .navbar, .no-print, .modal-footer { display: none !important; }
        .flex-grow-1 { margin-left: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        body { background: white !important; }
    }
    .invoice-header { background: linear-gradient(135deg, #1a5276, #2980b9); }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2"></i>{{ $invoice->invoice_number }}</h4>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-printer me-1"></i>In hoá đơn
        </button>
        @if($invoice->status === 'draft')
            <form method="POST" action="{{ route('admin.invoices.mark-paid', $invoice) }}" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-success btn-sm"><i class="bi bi-check-circle me-1"></i>Đánh dấu đã thanh toán</button>
            </form>
        @endif
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    {{-- Header hoá đơn --}}
    <div class="invoice-header text-white p-4 rounded-top-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="fw-bold mb-1"><i class="bi bi-building me-2"></i>Radiant Hotel</h3>
                <p class="mb-0 small opacity-75">123 Nguyễn Huệ, Quận 1, TP.HCM</p>
                <p class="mb-0 small opacity-75">ĐT: (028) 1234 5678 | Email: info@radianthotel.vn</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <h2 class="fw-bold mb-1">HOÁ ĐƠN</h2>
                <div class="fs-5 fw-semibold">{{ $invoice->invoice_number }}</div>
                <div class="small opacity-75">Ngày lập: {{ $invoice->issued_at->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <div class="card-body p-4">
        {{-- Thông tin khách & phòng --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="fw-bold text-muted mb-2">KHÁCH HÀNG</h6>
                <div class="fw-semibold fs-5">{{ $invoice->booking->guest_name }}</div>
                <div class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $invoice->booking->guest_phone }}</div>
                @if($invoice->booking->guest_email)
                    <div class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $invoice->booking->guest_email }}</div>
                @endif
            </div>
            <div class="col-md-6 text-md-end">
                <h6 class="fw-bold text-muted mb-2">CHI TIẾT LƯU TRÚ</h6>
                <div>Phòng <strong>{{ $invoice->booking->room->room_number }}</strong> ({{ $invoice->booking->room->roomType->name }})</div>
                <div>Nhận: <strong>{{ $invoice->booking->check_in_date->format('d/m/Y') }}</strong></div>
                <div>Trả: <strong>{{ $invoice->booking->check_out_date->format('d/m/Y') }}</strong></div>
                <div>Số đêm: <strong>{{ $invoice->booking->nights }}</strong></div>
            </div>
        </div>

        {{-- Bảng chi tiết --}}
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Mô tả</th>
                        <th class="text-center" style="width:80px;">SL</th>
                        <th class="text-end" style="width:150px;">Đơn giá</th>
                        <th class="text-end" style="width:150px;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
                        <td class="text-end fw-semibold">{{ number_format($item->line_total, 0, ',', '.') }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-semibold">Tạm tính:</td>
                        <td class="text-end fw-semibold">{{ number_format($invoice->subtotal, 0, ',', '.') }}đ</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end fw-semibold">Thuế VAT (10%):</td>
                        <td class="text-end fw-semibold">{{ number_format($invoice->tax, 0, ',', '.') }}đ</td>
                    </tr>
                    <tr class="table-primary">
                        <td colspan="4" class="text-end fw-bold fs-5">TỔNG CỘNG:</td>
                        <td class="text-end fw-bold fs-5">{{ number_format($invoice->total, 0, ',', '.') }}đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Trạng thái thanh toán --}}
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="p-3 rounded-3 {{ $invoice->status === 'paid' ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10' }}">
                    @php
                        $invLabels = ['draft' => 'Chờ thanh toán', 'paid' => 'Đã thanh toán', 'cancelled' => 'Đã huỷ'];
                        $invIcons  = ['draft' => 'hourglass-split', 'paid' => 'check-circle-fill', 'cancelled' => 'x-circle-fill'];
                    @endphp
                    <i class="bi bi-{{ $invIcons[$invoice->status] ?? 'question-circle' }} me-2 {{ $invoice->status === 'paid' ? 'text-success' : 'text-warning' }}"></i>
                    <strong>Trạng thái:</strong> {{ $invLabels[$invoice->status] ?? $invoice->status }}
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <p class="text-muted small mb-0">Cảm ơn quý khách đã sử dụng dịch vụ của Radiant Hotel.</p>
                <p class="text-muted small mb-0">Hẹn gặp lại!</p>
            </div>
        </div>
    </div>
</div>
@endsection
