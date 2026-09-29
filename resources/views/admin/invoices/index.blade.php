@extends('layouts.admin')
@section('title', 'Danh sách hoá đơn – Radiant Hotel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Quản lý hoá đơn</h4>
</div>

{{-- Thống kê --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card bg-white">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3" style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:rgba(41,128,185,0.1);color:#2980b9;">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:700;">{{ $stats['totalInvoices'] }}</div>
                    <div class="stat-label">Tổng hoá đơn</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card bg-white">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3" style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:rgba(39,174,96,0.1);color:#27ae60;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:700;color:#27ae60;">{{ $stats['paidCount'] }}</div>
                    <div class="stat-label">Đã thanh toán</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card bg-white">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3" style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:rgba(243,156,18,0.1);color:#f39c12;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:700;color:#f39c12;">{{ $stats['draftCount'] }}</div>
                    <div class="stat-label">Chờ thanh toán</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card bg-white">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3" style="width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:rgba(230,126,34,0.1);color:#e67e22;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <div style="font-size:1.1rem;font-weight:700;color:#e67e22;">{{ number_format($stats['totalRevenue'], 0, ',', '.') }}đ</div>
                    <div class="stat-label">Tổng doanh thu</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bộ lọc --}}
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Trạng thái</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tất cả</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
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
                <button type="submit" class="btn btn-outline-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Lọc</button>
            </div>
        </form>
    </div>
</div>

{{-- Bảng hoá đơn --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Số hoá đơn</th>
                        <th>Khách hàng</th>
                        <th>Phòng</th>
                        <th>Ngày lập</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Tổng tiền</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td class="fw-bold text-primary">{{ $invoice->invoice_number }}</td>
                        <td>
                            <div class="fw-semibold">{{ $invoice->booking->guest_name }}</div>
                            <small class="text-muted">{{ $invoice->booking->guest_phone }}</small>
                        </td>
                        <td><span class="badge bg-primary">{{ $invoice->booking->room->room_number }}</span></td>
                        <td>{{ $invoice->issued_at->format('d/m/Y') }}</td>
                        <td>
                            @php
                                $invColors = ['draft' => 'bg-warning text-dark', 'paid' => 'bg-success', 'cancelled' => 'bg-danger'];
                                $invLabels = ['draft' => 'Nháp', 'paid' => 'Đã thanh toán', 'cancelled' => 'Đã huỷ'];
                            @endphp
                            <span class="badge {{ $invColors[$invoice->status] ?? 'bg-secondary' }}">{{ $invLabels[$invoice->status] ?? $invoice->status }}</span>
                        </td>
                        <td class="text-end fw-bold">{{ number_format($invoice->total, 0, ',', '.') }}đ</td>
                        <td class="text-center">
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-6 d-block mb-2"></i>Chưa có hoá đơn nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $invoices->withQueryString()->links() }}</div>
@endsection
