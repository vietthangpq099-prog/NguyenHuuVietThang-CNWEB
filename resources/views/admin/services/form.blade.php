@extends('layouts.admin')
@section('title', ($isEdit ? 'Chỉnh sửa dịch vụ ' . $service->name : 'Thêm dịch vụ mới') . ' – Radiant Hotel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="bi bi-bell-fill me-2"></i>{{ $isEdit ? 'Chỉnh sửa dịch vụ: ' . $service->name : 'Thêm dịch vụ mới' }}
        </h4>
        <p class="text-muted mb-0">Cấu hình thông tin dịch vụ kèm theo giá niêm yết</p>
    </div>
    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary btn-sm">
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

                <form method="POST" action="{{ $isEdit ? route('admin.services.update', $service) : route('admin.services.store') }}">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Tên dịch vụ <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Ví dụ: Bữa sáng buffet, Giặt ủi, Spa massage..."
                                   value="{{ old('name', $service->name) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Đơn giá (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control"
                                   placeholder="Ví dụ: 150000"
                                   value="{{ old('price', $service->price) }}" min="0" step="5000" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả chi tiết dịch vụ</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Mô tả phạm vi dịch vụ, giờ phục vụ, các lưu ý...">{{ old('description', $service->description) }}</textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="isActiveSwitch"
                                   name="is_active" value="1"
                                   {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isActiveSwitch">
                                Kích hoạt phục vụ (Hiển thị cho lễ tân khi gắn dịch vụ cho khách)
                            </label>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>{{ $isEdit ? 'Cập nhật dịch vụ' : 'Lưu dịch vụ mới' }}
                        </button>
                        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary px-4">Huỷ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
