@extends('layouts.admin')
@section('title', 'Quản lý dịch vụ phụ – Radiant Hotel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-bell-fill me-2"></i>Quản lý danh mục dịch vụ phụ</h4>
        <p class="text-muted mb-0">Quản lý danh sách dịch vụ bổ sung tính phí (ăn uống, giặt ủi, đưa đón, spa...)</p>
    </div>
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm dịch vụ mới
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Tên dịch vụ</th>
                        <th>Mô tả chi tiết</th>
                        <th class="text-end" style="width:180px;">Đơn giá</th>
                        <th class="text-center" style="width:140px;">Trạng thái</th>
                        <th class="text-center" style="width:120px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $i => $service)
                    <tr>
                        <td class="text-muted">{{ $services->firstItem() + $i }}</td>
                        <td>
                            <span class="fw-bold text-primary fs-6">{{ $service->name }}</span>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $service->description ?? 'Chưa có mô tả' }}</span>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-success fs-6">
                                {{ number_format($service->price, 0, ',', '.') }}đ
                            </span>
                        </td>
                        <td class="text-center">
                            @if($service->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                    <i class="bi bi-check-circle me-1"></i>Đang phục vụ
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                    <i class="bi bi-pause-circle me-1"></i>Tạm dừng
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-outline-primary" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xoá dịch vụ {{ $service->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Xoá">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="bi bi-inbox display-6 d-block mb-2"></i>
                            Chưa có dịch vụ phụ nào được cấu hình.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $services->links() }}
</div>
@endsection
