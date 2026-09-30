@extends('layouts.admin')
@section('title', 'Quản lý Sinh viên & Ưu đãi Thẻ SV – Radiant Hotel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Quản lý Sinh viên & Ưu đãi Thẻ SV</h4>
        <p class="text-muted mb-0">Tra cứu sinh viên, kiểm soát thời hạn thẻ sinh viên và chính sách giảm giá phòng</p>
    </div>
    <a href="{{ route('admin.students.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i>Thêm sinh viên mới
    </a>
</div>

{{-- THỐNG KÊ NHANH --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center">
                <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary me-3">
                    <i class="bi bi-people-fill fs-4"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                    <small class="text-muted">Tổng số sinh viên trong hệ thống</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card border-0 shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center">
                <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success me-3">
                    <i class="bi bi-check-circle-fill fs-4"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-success mb-0">{{ $stats['valid'] }}</h3>
                    <small class="text-muted">Thẻ còn hạn (Được áp dụng giảm giá)</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="card border-0 shadow-sm rounded-3 p-3">
            <div class="d-flex align-items-center">
                <div class="rounded-3 p-3 bg-danger bg-opacity-10 text-danger me-3">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </div>
                <div>
                    <h3 class="fw-bold text-danger mb-0">{{ $stats['expired'] }}</h3>
                    <small class="text-muted">Thẻ hết hạn / Khóa (Không giảm giá)</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BỘ LỌC TÌM KIẾM --}}
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('admin.students.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6 col-12">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" 
                           placeholder="Tìm theo Mã SV, họ tên, trường, số điện thoại..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4 col-8">
                <select name="card_status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả tình trạng thẻ</option>
                    <option value="valid" {{ request('card_status') == 'valid' ? 'selected' : '' }}>✅ Thẻ còn hạn sử dụng</option>
                    <option value="expired" {{ request('card_status') == 'expired' ? 'selected' : '' }}>❌ Thẻ đã hết hạn / Khóa</option>
                </select>
            </div>
            <div class="col-md-2 col-4">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-funnel me-1"></i>Lọc
                </button>
            </div>
        </form>
    </div>
</div>

{{-- BẢNG DANH SÁCH SINH VIÊN --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Mã sinh viên</th>
                        <th>Họ và tên</th>
                        <th>Trường Đại học / Cao đẳng</th>
                        <th>Thời hạn thẻ</th>
                        <th class="text-center">Tình trạng ưu đãi</th>
                        <th class="text-center">Mức giảm</th>
                        <th class="text-center" style="width:120px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $i => $student)
                    <tr>
                        <td class="text-muted">{{ $students->firstItem() + $i }}</td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 fs-6">
                                <i class="bi bi-upc-scan me-1"></i>{{ $student->student_code }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $student->name }}</div>
                            <small class="text-muted">
                                @if($student->phone)<i class="bi bi-telephone me-1"></i>{{ $student->phone }}@endif
                                @if($student->email)<span class="mx-1">•</span><i class="bi bi-envelope me-1"></i>{{ $student->email }}@endif
                            </small>
                        </td>
                        <td>
                            <span class="text-secondary fw-semibold">{{ $student->university ?: 'Chưa cập nhật' }}</span>
                        </td>
                        <td>
                            <div class="small">
                                <span class="text-muted">Hết hạn:</span>
                                <strong class="{{ $student->isValidCard() ? 'text-dark' : 'text-danger' }}">
                                    {{ $student->card_expiry_date->format('d/m/Y') }}
                                </strong>
                            </div>
                            @if($student->card_issue_date)
                                <small class="text-muted">Cấp: {{ $student->card_issue_date->format('d/m/Y') }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            {!! $student->card_status_badge !!}
                        </td>
                        <td class="text-center">
                            @if($student->isValidCard())
                                <span class="badge bg-success fs-6 px-2 py-1">
                                    <i class="bi bi-percent me-1"></i>Giảm {{ $student->discount_percent }}%
                                </span>
                            @else
                                <span class="badge bg-secondary fs-6 px-2 py-1 text-decoration-line-through">
                                    Giảm {{ $student->discount_percent }}%
                                </span>
                                <div class="text-danger small mt-1">Hết hạn - Không áp dụng</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.students.edit', $student) }}" class="btn btn-outline-primary" title="Chỉnh sửa / Gia hạn thẻ">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.students.destroy', $student) }}" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xóa sinh viên {{ $student->name }} ({{ $student->student_code }})?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-mortarboard display-4 d-block mb-2 text-muted"></i>
                            Không tìm thấy sinh viên nào phù hợp.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($students->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $students->links() }}
    </div>
    @endif
</div>
@endsection
