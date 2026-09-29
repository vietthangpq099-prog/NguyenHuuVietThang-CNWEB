@extends('layouts.admin')
@section('title', ($isEdit ? 'Chỉnh sửa loại phòng ' . $roomType->name : 'Thêm loại phòng mới') . ' – Radiant Hotel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="bi bi-tags me-2"></i>{{ $isEdit ? 'Chỉnh sửa loại phòng: ' . $roomType->name : 'Thêm loại phòng mới' }}
        </h4>
        <p class="text-muted mb-0">Cập nhật thông tin tiêu chuẩn của loại phòng</p>
    </div>
    <a href="{{ route('admin.room-types.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Quay lại danh sách
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ $isEdit ? route('admin.room-types.update', $roomType) : route('admin.room-types.store') }}">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tên loại phòng <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Ví dụ: Standard, Deluxe, Presidential Suite..."
                                   value="{{ old('name', $roomType->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giá cơ bản (VNĐ / đêm) <span class="text-danger">*</span></label>
                            <input type="number" name="base_price" class="form-control"
                                   placeholder="Ví dụ: 750000"
                                   value="{{ old('base_price', $roomType->base_price) }}" min="0" step="10000" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sức chứa tối đa (người) <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" class="form-control"
                                   placeholder="Ví dụ: 2, 4..."
                                   value="{{ old('capacity', $roomType->capacity ?? 2) }}" min="1" max="20" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Diện tích (m²)</label>
                            <input type="number" name="area" class="form-control"
                                   placeholder="Ví dụ: 28, 45..."
                                   value="{{ old('area', $roomType->area) }}" min="0" step="0.5">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Đường dẫn hình ảnh đại diện (URL)</label>
                        <input type="url" name="image" id="typeImageInput" class="form-control"
                               placeholder="https://images.unsplash.com/..."
                               value="{{ old('image', $roomType->image) }}">
                        <small class="text-muted">Link ảnh minh hoạ chất lượng cao (Unsplash hoặc CDN).</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả tiện nghi & chi tiết loại phòng</label>
                        <textarea name="description" class="form-control" rows="4"
                                  placeholder="Mô tả các tiện nghi có sẵn trong phòng (giường, TV, minibar, bồn tắm, tầm nhìn...)">{{ old('description', $roomType->description) }}</textarea>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>{{ $isEdit ? 'Cập nhật loại phòng' : 'Lưu loại phòng' }}
                        </button>
                        <a href="{{ route('admin.room-types.index') }}" class="btn btn-outline-secondary px-4">Huỷ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Xem trước ảnh --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top:90px;">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-image me-2"></i>Ảnh minh hoạ</h6>
            </div>
            <div class="card-body text-center p-3">
                <img id="typeImagePreview"
                     src="{{ $roomType->image ?? 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800' }}"
                     alt="Xem trước ảnh loại phòng"
                     class="img-fluid rounded-3 shadow-sm mb-2"
                     style="height: 220px; width: 100%; object-fit: cover;">
                <small class="text-muted d-block">Ảnh sẽ hiển thị trên trang chủ và danh sách phòng.</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const typeImageInput = document.getElementById('typeImageInput');
    const typeImagePreview = document.getElementById('typeImagePreview');

    typeImageInput.addEventListener('input', function() {
        if (this.value) {
            typeImagePreview.src = this.value;
        }
    });
</script>
@endpush
