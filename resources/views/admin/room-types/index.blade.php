@extends('layouts.admin')
@section('title', 'Quản lý loại phòng – Radiant Hotel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-tags me-2"></i>Quản lý hạng phòng / Loại phòng</h4>
        <p class="text-muted mb-0">Thiết lập các tiêu chuẩn hạng phòng, giá niêm yết, tiện nghi và sức chứa</p>
    </div>
    <a href="{{ route('admin.room-types.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm loại phòng mới
    </a>
</div>

<div class="row g-4">
    @foreach($roomTypes as $type)
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100 overflow-hidden">
            <div class="row g-0 h-100">
                <div class="col-md-5">
                    <img src="{{ $type->image }}" alt="{{ $type->name }}"
                         class="img-fluid h-100 w-100" style="object-fit:cover;min-height:220px;">
                </div>
                <div class="col-md-7 d-flex flex-column">
                    <div class="card-body p-4 flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold text-primary mb-0">{{ $type->name }}</h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                {{ $type->rooms_count }} phòng
                            </span>
                        </div>
                        <p class="text-muted small mb-3" style="min-height:48px;">
                            {{ Str::limit($type->description, 110) }}
                        </p>
                        <div class="d-flex gap-3 text-muted small mb-3">
                            <span><i class="bi bi-arrows-angle-expand me-1"></i>{{ $type->area }} m²</span>
                            <span><i class="bi bi-people me-1"></i>Tối đa {{ $type->capacity }} khách</span>
                        </div>
                        <div class="fs-5 fw-bold text-danger">
                            {{ number_format($type->base_price, 0, ',', '.') }}đ
                            <small class="text-muted fs-6 fw-normal">/ đêm</small>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-3 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.room-types.edit', $type) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Chỉnh sửa
                        </a>
                        <form method="POST" action="{{ route('admin.room-types.destroy', $type) }}" class="d-inline"
                              onsubmit="return confirm('Bạn có chắc chắn muốn xoá loại phòng {{ $type->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" {{ $type->rooms_count > 0 ? 'disabled title=Còn_phòng_thuộc_loại_này' : '' }}>
                                <i class="bi bi-trash me-1"></i>Xoá
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
