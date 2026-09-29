@extends('layouts.admin')
@section('title', 'Quản lý phòng – Radiant Hotel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-door-open me-2"></i>Quản lý danh sách phòng</h4>
        <p class="text-muted mb-0">Quản lý toàn bộ phòng nghỉ, số phòng, tầng và tình trạng sử dụng</p>
    </div>
    <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Thêm phòng mới
    </a>
</div>

{{-- Bộ lọc phòng --}}
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.rooms.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Loại phòng</label>
                <select name="room_type" class="form-select form-select-sm">
                    <option value="">Tất cả loại phòng</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}" {{ request('room_type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Tầng</label>
                <select name="floor" class="form-select form-select-sm">
                    <option value="">Tất cả các tầng</option>
                    @foreach($floors as $floor)
                        <option value="{{ $floor }}" {{ request('floor') == $floor ? 'selected' : '' }}>
                            Tầng {{ $floor }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Trạng thái</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tất cả trạng thái</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Trống</option>
                    <option value="booked" {{ request('status') == 'booked' ? 'selected' : '' }}>Đã đặt</option>
                    <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Đang ở</option>
                    <option value="cleaning" {{ request('status') == 'cleaning' ? 'selected' : '' }}>Dọn dẹp</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary btn-sm flex-grow-1">
                    <i class="bi bi-funnel me-1"></i>Lọc
                </button>
                <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Bảng danh sách phòng --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:70px;">Hình ảnh</th>
                        <th>Số phòng</th>
                        <th>Loại phòng</th>
                        <th>Tầng</th>
                        <th>Sức chứa</th>
                        <th>Giá / đêm</th>
                        <th>Trạng thái</th>
                        <th class="text-center" style="width:120px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                    <tr>
                        <td>
                            <img src="{{ $room->image ?? $room->roomType->image }}"
                                 alt="Phòng {{ $room->room_number }}"
                                 class="rounded-3 shadow-sm"
                                 style="width:56px;height:42px;object-fit:cover;">
                        </td>
                        <td>
                            <span class="fw-bold fs-6 text-primary">Phòng {{ $room->room_number }}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $room->roomType->name }}</span>
                            <small class="text-muted d-block">{{ $room->roomType->area }}m²</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary bg-opacity-10 text-dark">Tầng {{ $room->floor }}</span>
                        </td>
                        <td>
                            <i class="bi bi-people text-muted me-1"></i>{{ $room->roomType->capacity }} người
                        </td>
                        <td>
                            <span class="fw-semibold text-danger">
                                {{ number_format($room->effective_price, 0, ',', '.') }}đ
                            </span>
                            @if($room->price_override)
                                <small class="badge bg-warning text-dark d-block" style="width:fit-content;">Giá riêng</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClasses = [
                                    'available'   => 'bg-success',
                                    'booked'      => 'bg-warning text-dark',
                                    'occupied'    => 'bg-danger',
                                    'cleaning'    => 'bg-info text-dark',
                                    'maintenance' => 'bg-secondary',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$room->status] ?? 'bg-secondary' }}">
                                {{ $room->status_label }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-outline-primary" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" class="d-inline"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn xoá phòng {{ $room->room_number }}?')">
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
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-door-closed display-6 d-block mb-2"></i>
                            Không tìm thấy phòng nào phù hợp với bộ lọc.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $rooms->withQueryString()->links() }}
</div>
@endsection
