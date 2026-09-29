@extends('layouts.admin')
@section('title', ($isEdit ? 'Chỉnh sửa phòng ' . $room->room_number : 'Thêm phòng mới') . ' – Radiant Hotel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="bi bi-door-open me-2"></i>{{ $isEdit ? 'Chỉnh sửa phòng ' . $room->room_number : 'Thêm phòng mới' }}
        </h4>
        <p class="text-muted mb-0">Điền thông tin chi tiết của phòng bên dưới</p>
    </div>
    <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary btn-sm">
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

                <form method="POST" action="{{ $isEdit ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}">
                    @csrf
                    @if($isEdit)
                        @method('PUT')
                    @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số phòng <span class="text-danger">*</span></label>
                            <input type="text" name="room_number" class="form-control"
                                   placeholder="Ví dụ: 101, 202A..."
                                   value="{{ old('room_number', $room->room_number) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tầng <span class="text-danger">*</span></label>
                            <input type="number" name="floor" class="form-control"
                                   placeholder="Ví dụ: 1, 2, 3..."
                                   value="{{ old('floor', $room->floor ?? 1) }}" min="1" max="50" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Loại phòng <span class="text-danger">*</span></label>
                            <select name="room_type_id" class="form-select" required id="roomTypeSelect">
                                <option value="">-- Chọn loại phòng --</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}"
                                            data-price="{{ $type->base_price }}"
                                            data-img="{{ $type->image }}"
                                            {{ old('room_type_id', $room->room_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }} (Giá gốc: {{ number_format($type->base_price, 0, ',', '.') }}đ/đêm)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="available" {{ old('status', $room->status ?? 'available') == 'available' ? 'selected' : '' }}>Trống (Sẵn sàng)</option>
                                <option value="booked" {{ old('status', $room->status) == 'booked' ? 'selected' : '' }}>Đã đặt</option>
                                <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>Đang ở</option>
                                <option value="cleaning" {{ old('status', $room->status) == 'cleaning' ? 'selected' : '' }}>Dọn dẹp</option>
                                <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giá ghi đè riêng (VNĐ/đêm)</label>
                            <input type="number" name="price_override" class="form-control"
                                   placeholder="Để trống nếu áp dụng giá loại phòng"
                                   value="{{ old('price_override', $room->price_override) }}" min="0" step="10000">
                            <small class="text-muted">Nếu phòng này có giá đặc biệt khác với giá mặc định của loại phòng.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Đường dẫn hình ảnh (URL)</label>
                            <input type="url" name="image" id="roomImageInput" class="form-control"
                                   placeholder="https://images.unsplash.com/..."
                                   value="{{ old('image', $room->image) }}">
                            <small class="text-muted">Để trống để lấy ảnh mặc định của loại phòng.</small>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>{{ $isEdit ? 'Cập nhật phòng' : 'Lưu phòng mới' }}
                        </button>
                        <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary px-4">Huỷ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Xem trước hình ảnh --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top:90px;">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-image me-2"></i>Xem trước ảnh phòng</h6>
            </div>
            <div class="card-body text-center p-3">
                <img id="imagePreview"
                     src="{{ $room->image ?? ($room->roomType->image ?? 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600') }}"
                     alt="Xem trước ảnh"
                     class="img-fluid rounded-3 shadow-sm mb-2"
                     style="height: 200px; width: 100%; object-fit: cover;">
                <small class="text-muted d-block">Ảnh sẽ hiển thị cho khách hàng khi chọn phòng này.</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const imageInput = document.getElementById('roomImageInput');
    const imagePreview = document.getElementById('imagePreview');
    const roomTypeSelect = document.getElementById('roomTypeSelect');

    imageInput.addEventListener('input', function() {
        if (this.value) {
            imagePreview.src = this.value;
        } else {
            const selectedOpt = roomTypeSelect.selectedOptions[0];
            if (selectedOpt && selectedOpt.dataset.img) {
                imagePreview.src = selectedOpt.dataset.img;
            }
        }
    });

    roomTypeSelect.addEventListener('change', function() {
        if (!imageInput.value) {
            const selectedOpt = this.selectedOptions[0];
            if (selectedOpt && selectedOpt.dataset.img) {
                imagePreview.src = selectedOpt.dataset.img;
            }
        }
    });
</script>
@endpush
