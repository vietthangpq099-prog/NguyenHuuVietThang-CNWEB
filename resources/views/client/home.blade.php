@extends('layouts.app')

@section('title', 'Radiant Hotel – Đặt phòng khách sạn cao cấp')

@section('content')

    {{-- ===================== HERO SECTION ===================== --}}
    <section class="hero-section text-white text-center">
        <div class="container">
            <h1 class="mb-3">Chào mừng đến Radiant Hotel</h1>
            <p class="lead mb-0">Trải nghiệm nghỉ dưỡng đẳng cấp 5 sao giữa lòng thành phố</p>
        </div>
    </section>

    {{-- ===================== BỘ LỌC TÌM KIẾM ===================== --}}
    <div class="container">
        <div class="search-box">
            <form method="GET" action="{{ route('rooms.index') }}">
                <div class="row g-3 align-items-end">
                    {{-- Ngày nhận phòng --}}
                    <div class="col-md-3 col-6 search-field">
                        <label class="form-label"><i class="bi bi-calendar-event me-1"></i>Ngày nhận phòng</label>
                        <input type="date" name="check_in" class="form-control"
                               value="{{ request('check_in', now()->format('Y-m-d')) }}"
                               min="{{ now()->format('Y-m-d') }}">
                    </div>

                    {{-- Ngày trả phòng --}}
                    <div class="col-md-3 col-6 search-field">
                        <label class="form-label"><i class="bi bi-calendar-check me-1"></i>Ngày trả phòng</label>
                        <input type="date" name="check_out" class="form-control"
                               value="{{ request('check_out', now()->addDay()->format('Y-m-d')) }}"
                               min="{{ now()->addDay()->format('Y-m-d') }}">
                    </div>

                    {{-- Loại phòng --}}
                    <div class="col-md-2 search-field">
                        <label class="form-label"><i class="bi bi-door-open me-1"></i>Loại phòng</label>
                        <select name="room_type" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach($roomTypes as $type)
                                <option value="{{ $type->id }}" {{ request('room_type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Mức giá --}}
                    <div class="col-md-2 search-field">
                        <label class="form-label"><i class="bi bi-cash me-1"></i>Mức giá</label>
                        <select name="price_range" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="0-500000" {{ request('price_range') == '0-500000' ? 'selected' : '' }}>Dưới 500.000đ</option>
                            <option value="500000-1000000" {{ request('price_range') == '500000-1000000' ? 'selected' : '' }}>500.000đ – 1.000.000đ</option>
                            <option value="1000000-2000000" {{ request('price_range') == '1000000-2000000' ? 'selected' : '' }}>1.000.000đ – 2.000.000đ</option>
                            <option value="2000000-" {{ request('price_range') == '2000000-' ? 'selected' : '' }}>Trên 2.000.000đ</option>
                        </select>
                    </div>

                    {{-- Nút tìm kiếm --}}
                    <div class="col-md-2 search-field">
                        <button type="submit" class="btn btn-search w-100">
                            <i class="bi bi-search me-1"></i>Tìm phòng
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== DANH SÁCH PHÒNG ===================== --}}
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Phòng dành cho bạn</h2>
            <span class="text-muted">
                <i class="bi bi-grid me-1"></i>{{ $rooms->count() }} phòng khả dụng
            </span>
        </div>

        @if($rooms->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-emoji-frown display-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Không tìm thấy phòng phù hợp</h4>
                <p class="text-muted">Vui lòng thử lại với bộ lọc khác hoặc chọn ngày khác.</p>
                <a href="{{ route('home') }}" class="btn btn-outline-primary mt-2">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại
                </a>
            </div>
        @else
            <div class="row g-4">
                @foreach($rooms as $room)
                    @php
                        $roomDetailUrl = route('rooms.show', $room);
                    @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card room-card">
                            {{-- Ảnh phòng (nhấp vào ảnh chuyển sang trang Xem chi tiết phòng) --}}
                            <a href="{{ $roomDetailUrl }}" class="img-wrapper d-block text-decoration-none" title="Nhấp để xem chi tiết phòng {{ $room->room_number }}">
                                <span class="badge-type">{{ $room->roomType->name }}</span>
                                <img src="{{ $room->image ?? $room->roomType->image }}"
                                     class="card-img-top"
                                     alt="Phòng {{ $room->room_number }}"
                                     loading="lazy">
                                <div class="img-hover-overlay">
                                    <span class="badge bg-white text-primary shadow px-3 py-2 fw-semibold">
                                        <i class="bi bi-eye-fill me-1"></i>Xem phòng & Cảnh quan
                                    </span>
                                </div>
                            </a>

                            <div class="card-body d-flex flex-column">
                                {{-- Tiêu đề --}}
                                <h5 class="room-title">
                                    <a href="{{ $roomDetailUrl }}" class="text-decoration-none" title="Xem phòng {{ $room->room_number }}">
                                        Phòng {{ $room->room_number }}
                                    </a>
                                </h5>
                                <p class="room-info mb-1">
                                    <i class="bi bi-arrows-angle-expand me-1"></i>{{ $room->roomType->area }}m²
                                    <span class="mx-1">•</span>
                                    <i class="bi bi-people me-1"></i>{{ $room->roomType->capacity }} khách
                                    <span class="mx-1">•</span>
                                    Tầng {{ $room->floor }}
                                </p>

                                {{-- Tiện nghi (icons) --}}
                                <div class="amenities">
                                    <span><i class="bi bi-wifi"></i>Wi-Fi</span>
                                    <span><i class="bi bi-snow"></i>Điều hoà</span>
                                    <span><i class="bi bi-tv"></i>TV</span>
                                    @if(in_array($room->roomType->name, ['Deluxe', 'Suite']))
                                        <span><i class="bi bi-droplet"></i>Bồn tắm</span>
                                    @endif
                                </div>

                                {{-- Mô tả ngắn --}}
                                <p class="text-muted small mb-3" style="flex-grow:1">
                                    {{ Str::limit($room->roomType->description, 80) }}
                                </p>

                                {{-- Giá tiền + nút xem phòng --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="price-tag">
                                        {{ number_format($room->effective_price, 0, ',', '.') }}đ
                                        <small>/ đêm</small>
                                    </div>
                                    @if($room->status === 'available')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                            <i class="bi bi-check-circle me-1"></i>Trống
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-muted border">
                                            {{ $room->status_label }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ $roomDetailUrl }}" class="btn btn-view-room">
                                    <i class="bi bi-eye me-1"></i>Xem phòng
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ===================== THỐNG KÊ NHANH ===================== --}}
    <div class="container mt-5 mb-4">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary mx-auto mb-2"><i class="bi bi-door-open"></i></div>
                    <h3 class="fw-bold mb-0">{{ $stats['total'] }}</h3>
                    <small class="text-muted">Tổng số phòng</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success mx-auto mb-2"><i class="bi bi-check-circle"></i></div>
                    <h3 class="fw-bold text-success mb-0">{{ $stats['available'] }}</h3>
                    <small class="text-muted">Phòng trống</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning mx-auto mb-2"><i class="bi bi-bookmark"></i></div>
                    <h3 class="fw-bold text-warning mb-0">{{ $stats['booked'] }}</h3>
                    <small class="text-muted">Đã đặt trước</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm rounded-3 p-3 text-center">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger mx-auto mb-2"><i class="bi bi-person-fill"></i></div>
                    <h3 class="fw-bold text-danger mb-0">{{ $stats['occupied'] }}</h3>
                    <small class="text-muted">Đang có khách</small>
                </div>
            </div>
        </div>
    </div>

@endsection
