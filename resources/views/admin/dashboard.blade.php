@extends('layouts.admin')
@section('title', 'Tổng quan – Radiant Hotel')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2"></i>Tổng quan hệ thống</h4>
        <span class="text-muted"><i class="bi bi-clock me-1"></i>{{ now()->format('H:i – d/m/Y') }}</span>
    </div>

    {{-- ===================== THỐNG KÊ CHÍNH ===================== --}}
    <div class="row g-3 mb-4">
        <div class="col-xl col-md-4 col-6">
            <div class="card stat-card bg-white">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(26,82,118,0.1);color:#1a5276;font-size:1.2rem;">
                        <i class="bi bi-building"></i>
                    </div>
                    <div>
                        <div style="font-size:1.5rem;font-weight:700;">{{ $stats['totalRooms'] }}</div>
                        <div class="stat-label">Tổng phòng</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4 col-6">
            <div class="card stat-card" style="background:linear-gradient(135deg,#e8f5e9,#f1f8f1);">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(39,174,96,0.15);color:#27ae60;font-size:1.2rem;">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div>
                        <div style="font-size:1.5rem;font-weight:700;color:#27ae60;">{{ $stats['availableRooms'] }}</div>
                        <div class="stat-label">Phòng trống</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4 col-6">
            <div class="card stat-card" style="background:linear-gradient(135deg,#fdedec,#fde8e6);">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(231,76,60,0.15);color:#e74c3c;font-size:1.2rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div style="font-size:1.5rem;font-weight:700;color:#e74c3c;">{{ $stats['occupiedRooms'] }}</div>
                        <div class="stat-label">Đang có khách</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4 col-6">
            <div class="card stat-card" style="background:linear-gradient(135deg,#fef9e7,#fdf5e0);">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(243,156,18,0.15);color:#f39c12;font-size:1.2rem;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div>
                        <div style="font-size:1.5rem;font-weight:700;color:#f39c12;">{{ $stats['pendingBookings'] }}</div>
                        <div class="stat-label">Chờ xác nhận</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-md-4 col-6">
            <div class="card stat-card" style="background:linear-gradient(135deg,#eaf2f8,#d6eaf8);">
                <div class="d-flex align-items-center">
                    <div class="me-3" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:rgba(41,128,185,0.15);color:#2980b9;font-size:1.2rem;">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div>
                        <div style="font-size:1.5rem;font-weight:700;color:#2980b9;">{{ $stats['occupancyRate'] }}%</div>
                        <div class="stat-label">Tỷ lệ lấp đầy</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== DOANH THU ===================== --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 text-center" style="background:linear-gradient(135deg,#1a5276,#2980b9);color:white;">
                <small class="opacity-75">Doanh thu hôm nay</small>
                <div class="fs-3 fw-bold">{{ number_format($stats['todayRevenue'], 0, ',', '.') }}đ</div>
                <small class="opacity-75"><i class="bi bi-arrow-down-right me-1"></i>Check-in: {{ $stats['todayCheckins'] }} | Check-out: {{ $stats['todayCheckouts'] }}</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-3 text-center" style="background:linear-gradient(135deg,#e67e22,#f39c12);color:white;">
                <small class="opacity-75">Doanh thu tháng {{ now()->format('m/Y') }}</small>
                <div class="fs-3 fw-bold">{{ number_format($stats['monthlyRevenue'], 0, ',', '.') }}đ</div>
                <small class="opacity-75"><i class="bi bi-graph-up-arrow me-1"></i>Tổng hoá đơn đã thanh toán</small>
            </div>
        </div>
    </div>

    {{-- ===================== BIỂU ĐỒ ===================== --}}
    <div class="row g-4 mb-4">
        {{-- Biểu đồ doanh thu 7 ngày --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart me-2"></i>Doanh thu 7 ngày gần nhất</h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary active" onclick="showChart('daily')">Ngày</button>
                        <button class="btn btn-outline-primary" onclick="showChart('monthly')">Tháng</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="280"></canvas>
                </div>
            </div>
        </div>

        {{-- Biểu đồ tròn trạng thái phòng --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-pie-chart me-2"></i>Trạng thái phòng</h6>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <canvas id="roomStatusChart" height="250" style="max-width:280px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== SẮP CHECK-IN HÔM NAY ===================== --}}
    @if($upcomingCheckins->count() > 0)
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-0 pt-3">
            <h6 class="fw-bold mb-0"><i class="bi bi-bell text-warning me-2"></i>Sắp check-in hôm nay ({{ $upcomingCheckins->count() }})</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-warning">
                        <tr><th>Khách</th><th>Phòng</th><th>Trạng thái</th><th class="text-end">Tổng tiền</th><th class="text-center">Thao tác</th></tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingCheckins as $b)
                        <tr>
                            <td><div class="fw-semibold">{{ $b->guest_name }}</div><small class="text-muted">{{ $b->guest_phone }}</small></td>
                            <td><span class="badge bg-primary">{{ $b->room->room_number }}</span> {{ $b->room->roomType->name }}</td>
                            <td><span class="badge bg-secondary">{{ $b->status_label }}</span></td>
                            <td class="text-end fw-bold">{{ number_format($b->total_price, 0, ',', '.') }}đ</td>
                            <td class="text-center">
                                <a href="{{ route('admin.bookings.show', $b) }}" class="btn btn-success btn-sm"><i class="bi bi-box-arrow-in-right me-1"></i>Check-in</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ===================== ĐẶT PHÒNG GẦN ĐÂY ===================== --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Đặt phòng gần đây</h6>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-primary btn-sm">Xem tất cả</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Khách hàng</th><th>Phòng</th><th>Nhận phòng</th><th>Trả phòng</th><th>Trạng thái</th><th>Thanh toán</th><th class="text-end">Tổng tiền</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $booking)
                        <tr>
                            <td><div class="fw-semibold">{{ $booking->guest_name }}</div><small class="text-muted">{{ $booking->guest_phone }}</small></td>
                            <td><span class="badge bg-primary">{{ $booking->room->room_number }}</span><br><small class="text-muted">{{ $booking->room->roomType->name }}</small></td>
                            <td>{{ $booking->check_in_date->format('d/m/Y') }}</td>
                            <td>{{ $booking->check_out_date->format('d/m/Y') }}</td>
                            <td>
                                @php $sc = ['pending'=>'bg-secondary','confirmed'=>'bg-info text-dark','checked_in'=>'bg-success','checked_out'=>'bg-dark','cancelled'=>'bg-danger']; @endphp
                                <span class="badge {{ $sc[$booking->status] ?? 'bg-secondary' }}">{{ $booking->status_label }}</span>
                            </td>
                            <td>
                                @php $pc = ['unpaid'=>'text-danger','partial'=>'text-warning','paid'=>'text-success']; @endphp
                                <span class="fw-semibold {{ $pc[$booking->payment_status] ?? '' }}">{{ $booking->payment_status_label }}</span>
                            </td>
                            <td class="text-end fw-bold">{{ number_format($booking->total_price, 0, ',', '.') }}đ</td>
                            <td><a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-outline-primary btn-sm p-1"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    // === DỮ LIỆU TỪ CONTROLLER ===
    const dailyData   = @json($revenueByDay);
    const monthlyData = @json($revenueByMonth);
    const roomStatus  = @json($roomStatuses);

    // === BIỂU ĐỒ DOANH THU ===
    const ctx = document.getElementById('revenueChart').getContext('2d');
    let revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dailyData.map(d => d.label),
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: dailyData.map(d => d.amount),
                backgroundColor: 'rgba(41, 128, 185, 0.7)',
                borderColor: '#2980b9',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.raw.toLocaleString('vi-VN') + 'đ'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => (v / 1000000).toFixed(1) + 'M'
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { grid: { display: false } }
            }
        }
    });

    function showChart(type) {
        const data = type === 'daily' ? dailyData : monthlyData;
        revenueChart.data.labels = data.map(d => d.label);
        revenueChart.data.datasets[0].data = data.map(d => d.amount);
        revenueChart.data.datasets[0].type = type === 'daily' ? 'bar' : 'line';
        if (type === 'monthly') {
            revenueChart.data.datasets[0].fill = true;
            revenueChart.data.datasets[0].backgroundColor = 'rgba(230,126,34,0.15)';
            revenueChart.data.datasets[0].borderColor = '#e67e22';
            revenueChart.data.datasets[0].tension = 0.4;
        } else {
            revenueChart.data.datasets[0].fill = false;
            revenueChart.data.datasets[0].backgroundColor = 'rgba(41,128,185,0.7)';
            revenueChart.data.datasets[0].borderColor = '#2980b9';
        }
        revenueChart.update();
        // Toggle button active
        document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
        event.target.classList.add('active');
    }

    // === BIỂU ĐỒ TRÒN TRẠNG THÁI PHÒNG ===
    const statusLabels = {
        available: 'Trống', booked: 'Đã đặt', occupied: 'Đang ở',
        maintenance: 'Bảo trì', cleaning: 'Dọn dẹp'
    };
    const statusColors = {
        available: '#27ae60', booked: '#f39c12', occupied: '#e74c3c',
        maintenance: '#95a5a6', cleaning: '#8e44ad'
    };

    const rsLabels = Object.keys(roomStatus).map(k => statusLabels[k] || k);
    const rsData   = Object.values(roomStatus);
    const rsColors = Object.keys(roomStatus).map(k => statusColors[k] || '#bbb');

    new Chart(document.getElementById('roomStatusChart'), {
        type: 'doughnut',
        data: {
            labels: rsLabels,
            datasets: [{
                data: rsData,
                backgroundColor: rsColors,
                borderWidth: 3,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 15, font: { size: 12 } } }
            },
            cutout: '55%',
        }
    });
</script>
@endpush
