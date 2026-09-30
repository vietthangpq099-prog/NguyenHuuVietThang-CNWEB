@extends('layouts.admin')
@section('title', 'Chỉnh sửa sinh viên & gia hạn thẻ – Radiant Hotel')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1"><i class="bi bi-pencil-square me-2 text-primary"></i>Chỉnh sửa & Gia hạn thẻ sinh viên</h4>
                <p class="text-muted mb-0">Cập nhật thông tin thẻ SV: {{ $student->student_code }} – {{ $student->name }}</p>
            </div>
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Quay lại
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.students.update', $student) }}">
                    @csrf
                    @method('PUT')

                    <div class="p-3 bg-light rounded-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="text-muted small d-block">Trạng thái thẻ hiện tại:</span>
                            {!! $student->card_status_badge !!}
                        </div>
                        <div>
                            <span class="text-muted small d-block">Mức giảm hiện áp dụng:</span>
                            <span class="fw-bold text-success fs-5">{{ $student->discount_percent }}%</span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-card-text me-1"></i>Thông tin thẻ sinh viên</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mã sinh viên <span class="text-danger">*</span></label>
                            <input type="text" name="student_code" class="form-control" 
                                   value="{{ old('student_code', $student->student_code) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ old('name', $student->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trường Đại học / Cao đẳng</label>
                            <input type="text" name="university" class="form-control" 
                                   value="{{ old('university', $student->university) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mức giảm giá phòng (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="discount_percent" class="form-control" 
                                       value="{{ old('discount_percent', $student->discount_percent) }}" min="0" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-calendar-event me-1"></i>Thời hạn hiệu lực của thẻ</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày cấp thẻ</label>
                            <input type="date" name="card_issue_date" class="form-control" 
                                   value="{{ old('card_issue_date', $student->card_issue_date ? $student->card_issue_date->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày hết hạn thẻ <span class="text-danger">*</span></label>
                            <input type="date" name="card_expiry_date" class="form-control" 
                                   value="{{ old('card_expiry_date', $student->card_expiry_date->format('Y-m-d')) }}" required>
                            <small class="text-muted">Gia hạn bằng cách tăng ngày hết hạn lên</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái sinh viên</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Đang học (Hoạt động bình thường)</option>
                                <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Đã tốt nghiệp (Hết ưu đãi)</option>
                                <option value="locked" {{ old('status', $student->status) == 'locked' ? 'selected' : '' }}>Khóa thẻ (Tạm dừng ưu đãi)</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-telephone me-1"></i>Thông tin liên hệ & Ghi chú</h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <input type="tel" name="phone" class="form-control" 
                                   value="{{ old('phone', $student->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="{{ old('email', $student->email) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ghi chú thêm</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $student->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.students.index') }}" class="btn btn-light border px-4">Hủy</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-check-circle me-1"></i>Cập nhật thông tin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
