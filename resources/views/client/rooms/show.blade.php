@extends('layouts.app')

@section('title', 'Phòng ' . $room->room_number . ' (' . $room->roomType->name . ') – Radiant Hotel')

@push('styles')
<style>
    /* Nút mũi tên điều hướng trên ảnh lớn */
    .gallery-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.45);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        cursor: pointer;
        transition: all 0.25s ease;
        z-index: 10;
        backdrop-filter: blur(4px);
    }
    .gallery-nav-btn:hover {
        background: rgba(41, 128, 185, 0.9);
        color: white;
        border-color: white;
        transform: translateY(-50%) scale(1.1);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }
    .gallery-nav-btn.prev-btn { left: 16px; }
    .gallery-nav-btn.next-btn { right: 16px; }

    /* Dải thumbnails cuộn ngang */
    .thumb-strip {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 8px;
        scroll-behavior: smooth;
    }
    .thumb-strip::-webkit-scrollbar {
        height: 6px;
    }
    .thumb-strip::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .thumb-card {
        flex: 0 0 130px;
        height: 90px;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        position: relative;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }
    .thumb-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .thumb-card:hover img {
        transform: scale(1.08);
    }
    .thumb-card.active {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(41, 128, 185, 0.35);
    }

    /* Hiệu ứng kính lúp mở to khi rê vào ảnh lớn */
    .main-gallery-view {
        cursor: zoom-in;
    }
    .zoom-hint {
        position: absolute;
        top: 16px;
        right: 16px;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.8rem;
        z-index: 5;
        backdrop-filter: blur(4px);
        pointer-events: none;
        transition: all 0.2s ease;
    }
    .main-gallery-view:hover .zoom-hint {
        background: var(--primary);
    }

    /* Thẻ tiện ích bên trong khách sạn */
    .facility-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .facility-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12) !important;
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('rooms.index') }}" class="text-decoration-none">Danh sách phòng</a></li>
            <li class="breadcrumb-item active" aria-current="page">Phòng {{ $room->room_number }} ({{ $room->roomType->name }})</li>
        </ol>
    </nav>

    {{-- Tiêu đề phòng & thông tin tổng quan --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary fs-6">Hạng {{ $room->roomType->name }}</span>
                <span class="badge bg-secondary bg-opacity-10 text-dark border">Tầng {{ $room->floor }}</span>
                <span class="badge bg-success bg-opacity-10 text-success border border-success">
                    <i class="bi bi-shield-check me-1"></i>Đã vệ sinh khử khuẩn
                </span>
            </div>
            <h1 class="fw-bold mb-1" style="color: #1a5276;">Phòng {{ $room->room_number }} – {{ $room->roomType->name }}</h1>
            <p class="text-muted mb-0">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i>123 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh
                <span class="mx-2">•</span>
                <i class="bi bi-arrows-angle-expand me-1"></i>Diện tích: <strong>{{ $room->roomType->area }} m²</strong>
                <span class="mx-2">•</span>
                <i class="bi bi-people-fill me-1"></i>Sức chứa: <strong>Tối đa {{ $room->roomType->capacity }} khách</strong>
            </p>
        </div>

        <div class="text-md-end">
            <div class="fs-6 text-muted">Giá phòng niêm yết:</div>
            <div class="fs-2 fw-bold text-danger mb-0">
                {{ number_format($room->effective_price, 0, ',', '.') }}đ
                <small class="fs-6 text-muted fw-normal">/ đêm</small>
            </div>
            <small class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Đã bao gồm thuế & phí dịch vụ</small>
        </div>
    </div>

    {{-- ===================== BỘ SƯU TẬP HÌNH ẢNH NÂNG CAO ===================== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-images me-2"></i>Bộ sưu tập hình ảnh phòng & tiện ích
                </h5>
                <span class="badge bg-dark bg-opacity-75" id="photoCounter">Ảnh 1 / {{ count($gallery) }}</span>
            </div>
            {{-- Tabs lọc nhanh danh mục ảnh --}}
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-primary active" onclick="filterCategory('all', this)">
                    Tất cả ({{ count($gallery) }})
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="filterCategory('Góc chụp phòng ngủ', this)">
                    Góc chụp phòng ngủ
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="filterCategory('Tiện ích bên trong khách sạn', this)">
                    Tiện ích khách sạn
                </button>
            </div>
        </div>

        <div class="p-3 bg-light">
            {{-- KHUNG ẢNH TO NHẤT CÓ 2 NÚT MŨI TÊN ĐIỀU HƯỚNG --}}
            <div class="main-gallery-view position-relative rounded-4 overflow-hidden mb-3 shadow-sm"
                 style="height: 480px; background-color: #000;"
                 onclick="openLightbox()">

                {{-- Nút mũi tên TRƯỚC --}}
                <button class="gallery-nav-btn prev-btn" type="button" onclick="event.stopPropagation(); prevImage()" title="Xem ảnh trước">
                    <i class="bi bi-chevron-left"></i>
                </button>

                {{-- Nút mũi tên SAU --}}
                <button class="gallery-nav-btn next-btn" type="button" onclick="event.stopPropagation(); nextImage()" title="Xem ảnh tiếp theo">
                    <i class="bi bi-chevron-right"></i>
                </button>

                {{-- Huy hiệu gợi ý phóng to --}}
                <div class="zoom-hint">
                    <i class="bi bi-arrows-fullscreen me-1"></i>Bấm vào ảnh để phóng to
                </div>

                {{-- Ảnh to nhất --}}
                <img id="mainImage" src="{{ $gallery[0]['url'] }}"
                     alt="{{ $gallery[0]['title'] }}"
                     class="w-100 h-100 object-fit-cover"
                     style="transition: opacity 0.25s ease;">

                {{-- Chú thích ảnh góc dưới --}}
                <div class="position-absolute bottom-0 start-0 end-0 p-4 text-white"
                     style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 60%, transparent 100%);">
                    <span class="badge bg-primary mb-2 px-3 py-1" id="mainCategory">{{ $gallery[0]['category'] }}</span>
                    <h4 class="fw-bold mb-1" id="mainTitle">{{ $gallery[0]['title'] }}</h4>
                    <p class="small mb-0 opacity-90" id="mainDesc">{{ $gallery[0]['desc'] }}</p>
                </div>
            </div>

            {{-- DẢI THUMBNAILS NHỎ BÊN DƯỚI ĐỂ CHỌN NHANH --}}
            <div class="thumb-strip" id="thumbStrip">
                @foreach($gallery as $index => $img)
                    <div class="thumb-card {{ $index === 0 ? 'active' : '' }}"
                         data-category="{{ $img['category'] }}"
                         data-index="{{ $index }}"
                         onclick="goToImage({{ $index }})"
                         title="{{ $img['title'] }}">
                        <img src="{{ $img['url'] }}" alt="{{ $img['title'] }}">
                        <div class="position-absolute bottom-0 start-0 end-0 p-1 text-white text-center small text-truncate"
                             style="background: rgba(0,0,0,0.7); font-size: 0.68rem;">
                            {{ $img['category'] === 'Góc chụp phòng ngủ' ? 'Phòng ngủ' : ($img['category'] === 'Phòng tắm' ? 'Phòng tắm' : 'Tiện ích') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===================== NỘI DUNG CHI TIẾT & FORM ĐẶT PHÒNG ===================== --}}
    <div class="row g-4">

        {{-- CỘT TRÁI (8/12): ĐÁNH GIÁ ĐỘ YÊN TĨNH & TIỆN ÍCH BÊN TRONG KHÁCH SẠN --}}
        <div class="col-lg-8">

            {{-- KHỐI 1: ĐÁNH GIÁ ĐỘ YÊN TĨNH (ACOUSTIC & QUIETNESS) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: linear-gradient(135deg, #f0f7fc 0%, #ffffff 100%); border-left: 5px solid #2980b9 !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 rounded-circle bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-volume-mute-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-primary">Độ yên tĩnh & Khả năng cách âm của phòng</h5>
                                <small class="text-muted">Được đo lường và đánh giá theo tiêu chuẩn không gian nghỉ dưỡng quốc tế</small>
                            </div>
                        </div>
                        <span class="badge bg-{{ $environmentInfo['quietness_class'] }} fs-6 px-3 py-2 rounded-pill">
                            <i class="bi bi-star-fill me-1"></i>{{ $environmentInfo['quietness_badge'] }}
                        </span>
                    </div>

                    {{-- Thanh điểm độ yên tĩnh --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between small fw-semibold text-muted mb-1">
                            <span>Chỉ số tĩnh lặng:</span>
                            <span class="text-primary fw-bold">{{ $environmentInfo['quietness_score'] }} / 100 điểm</span>
                        </div>
                        <div class="progress" style="height: 10px; border-radius: 6px;">
                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" role="progressbar"
                                 style="width: {{ $environmentInfo['quietness_score'] }}%;"></div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border">
                                <div class="fw-bold text-dark mb-1">
                                    <i class="bi bi-soundwave text-primary me-2"></i>Mức độ tiếng ồn đo được:
                                </div>
                                <div class="text-muted small">{{ $environmentInfo['noise_level'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border">
                                <div class="fw-bold text-dark mb-1">
                                    <i class="bi bi-shield-shaded text-success me-2"></i>Hệ thống cửa & kính:
                                </div>
                                <div class="text-muted small">{{ $environmentInfo['soundproof_system'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border">
                                <div class="fw-bold text-dark mb-1">
                                    <i class="bi bi-door-closed text-warning me-2"></i>Vị trí hành lang & Thang máy:
                                </div>
                                <div class="text-muted small">{{ $environmentInfo['corridor_privacy'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border">
                                <div class="fw-bold text-dark mb-1">
                                    <i class="bi bi-moon-stars text-info me-2"></i>Chất lượng giấc ngủ ban đêm:
                                </div>
                                <div class="text-muted small">{{ $environmentInfo['night_sleep_quality'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KHỐI 2: TIỆN ÍCH BÊN TRONG KHÁCH SẠN (ĐỒNG NHẤT 100% ĐỊA ĐIỂM) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-primary mb-0">
                            <i class="bi bi-building-check me-2"></i>Tiện ích bên trong khách sạn Radiant Hotel
                        </h5>
                        <small class="text-muted">Đồng nhất vị trí & dịch vụ 5 sao</small>
                    </div>

                    <div class="row g-3">
                        {{-- Tiện ích 1 --}}
                        <div class="col-md-6">
                            <div class="card border rounded-3 p-3 facility-card h-100" onclick="goToImage(4)">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=200"
                                         class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;" alt="Sảnh Lobby">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-primary">Sảnh đón tiếp Grand Lobby</h6>
                                        <small class="text-muted d-block">Không gian lộng lẫy, tiếp đón chu đáo và hỗ trợ khách 24/7.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tiện ích 2 --}}
                        <div class="col-md-6">
                            <div class="card border rounded-3 p-3 facility-card h-100" onclick="goToImage(5)">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=200"
                                         class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;" alt="Hồ bơi">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-primary">Hồ bơi vô cực nước ấm</h6>
                                        <small class="text-muted d-block">Hồ bơi tầng cao ngắm nhìn toàn cảnh thành phố, mở cửa miễn phí.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tiện ích 3 --}}
                        <div class="col-md-6">
                            <div class="card border rounded-3 p-3 facility-card h-100" onclick="goToImage(6)">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?w=200"
                                         class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;" alt="Nhà hàng">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-primary">Nhà hàng Fine Dining & Buffet</h6>
                                        <small class="text-muted d-block">Bữa sáng buffet phong phú ẩm thực Á - Âu chất lượng hảo hạng.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tiện ích 4 --}}
                        <div class="col-md-6">
                            <div class="card border rounded-3 p-3 facility-card h-100" onclick="goToImage(7)">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=200"
                                         class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;" alt="Sky Lounge">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-primary">Sky Lounge & Cafe ngắm cảnh</h6>
                                        <small class="text-muted d-block">Không gian thư giãn nhẹ nhàng với trà chiều và cocktail hoàng hôn.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tiện ích 5 --}}
                        <div class="col-md-6">
                            <div class="card border rounded-3 p-3 facility-card h-100" onclick="goToImage(8)">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=200"
                                         class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;" alt="Gym">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-primary">Phòng tập Gym & Fitness 24/7</h6>
                                        <small class="text-muted d-block">Trang thiết bị Technogym hiện đại, phòng tập thoáng khí view đẹp.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tiện ích 6 --}}
                        <div class="col-md-6">
                            <div class="card border rounded-3 p-3 facility-card h-100" onclick="goToImage(9)">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=200"
                                         class="rounded-3" style="width: 70px; height: 70px; object-fit: cover;" alt="Spa">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-primary">Khu Spa & Xông hơi thảo dược</h6>
                                        <small class="text-muted d-block">Liệu trình massage thư giãn và xông hơi đá muối giải toả căng thẳng.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KHỐI 3: TIỆN NGHI CHI TIẾT TRONG PHÒNG --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-primary mb-3">
                        <i class="bi bi-grid-fill me-2"></i>Trang thiết bị & Tiện nghi phòng
                    </h5>

                    <div class="row row-cols-2 row-cols-md-3 g-3">
                        <div class="col">
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-wifi text-primary fs-5"></i>
                                <span>Wi-Fi 6 tốc độ cao miễn phí</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-snow text-primary fs-5"></i>
                                <span>Điều hoà 2 chiều Daikin</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-tv text-primary fs-5"></i>
                                <span>Smart TV 4K Netflix/Youtube</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-cup-hot text-primary fs-5"></i>
                                <span>Ấm siêu tốc & Trà/Cà phê miễn phí</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-droplet text-primary fs-5"></i>
                                <span>Vòi sen nóng lạnh tăng áp</span>
                            </div>
                        </div>
                        @if(in_array($room->roomType->name, ['Deluxe', 'Suite']))
                        <div class="col">
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-water text-primary fs-5"></i>
                                <span>Bồn tắm ngâm mình Jacuzzi</span>
                            </div>
                        </div>
                        @endif
                        <div class="col">
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-shield-lock text-primary fs-5"></i>
                                <span>Két sắt an toàn điện tử</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-wind text-primary fs-5"></i>
                                <span>Máy sấy tóc ion âm</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center gap-2 text-muted small">
                                <i class="bi bi-plug text-primary fs-5"></i>
                                <span>Ổ cắm sạc đa năng quốc tế</span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-2">Mô tả chi tiết phòng từ khách sạn:</h6>
                    <p class="text-muted leading-relaxed mb-0">{{ $room->roomType->description }}</p>
                </div>
            </div>

            {{-- KHỐI 4: QUY ĐỊNH & CHÍNH SÁCH PHÒNG --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-primary mb-3">
                        <i class="bi bi-info-circle-fill me-2"></i>Quy định lưu trú & Chính sách
                    </h5>
                    <div class="row g-3 small text-muted">
                        <div class="col-md-6">
                            <i class="bi bi-clock-fill text-primary me-2"></i><strong>Nhận phòng:</strong> từ 14:00
                        </div>
                        <div class="col-md-6">
                            <i class="bi bi-clock-history text-primary me-2"></i><strong>Trả phòng:</strong> trước 12:00 trưa
                        </div>
                        <div class="col-md-6">
                            <i class="bi bi-x-octagon-fill text-danger me-2"></i><strong>Hút thuốc:</strong> Nghiêm cấm trong phòng
                        </div>
                        <div class="col-md-6">
                            <i class="bi bi-check-circle-fill text-success me-2"></i><strong>Huỷ phòng:</strong> Miễn phí huỷ trước 24 giờ nhận phòng
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- CỘT PHẢI (4/12): HỘP ĐẶT PHÒNG NHANH (STICKY BOOKING BOX) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 90px; border-top: 5px solid var(--accent) !important;">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-primary mb-1">Đặt giữ phòng này</h5>
                    <p class="text-muted small mb-3">Sau khi xem hình ảnh & độ yên tĩnh, hãy chọn ngày để đặt</p>

                    <form method="GET" action="{{ route('booking.create') }}">
                        <input type="hidden" name="room" value="{{ $room->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Ngày nhận phòng</label>
                            <input type="date" name="check_in" id="detailCheckIn" class="form-control"
                                   value="{{ request('check_in', now()->format('Y-m-d')) }}"
                                   min="{{ now()->format('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Ngày trả phòng</label>
                            <input type="date" name="check_out" id="detailCheckOut" class="form-control"
                                   value="{{ request('check_out', now()->addDay()->format('Y-m-d')) }}"
                                   min="{{ now()->addDay()->format('Y-m-d') }}" required>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Đơn giá:</span>
                            <span class="fw-semibold text-dark">{{ number_format($room->effective_price, 0, ',', '.') }}đ / đêm</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small text-muted">
                            <span>Thời gian lưu trú:</span>
                            <span class="fw-semibold text-dark" id="calcNights">1 đêm</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 fs-5 fw-bold">
                            <span>Tổng tạm tính:</span>
                            <span class="text-danger" id="calcTotal">{{ number_format($room->effective_price, 0, ',', '.') }}đ</span>
                        </div>

                        @if($room->status === 'available')
                            <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold shadow-sm py-3" style="background-color: var(--accent); border-color: var(--accent); color: white;">
                                <i class="bi bi-calendar-check-fill me-2"></i>Tiến hành đặt phòng
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary btn-lg w-100 fw-bold" disabled>
                                <i class="bi bi-x-circle me-1"></i>Phòng hiện đang có khách
                            </button>
                        @endif
                    </form>

                    <div class="mt-3 text-center">
                        <small class="text-muted d-block mb-1">
                            <i class="bi bi-telephone-fill me-1 text-primary"></i>Hỗ trợ 24/7: <strong>(028) 1234 5678</strong>
                        </small>
                        <small class="text-muted">Không thu phụ phí khi đặt trực tuyến</small>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ===================== GỢI Ý CÁC PHÒNG KHÁC ===================== --}}
    @if($relatedRooms->isNotEmpty())
    <div class="mt-5 pt-4 border-top">
        <h3 class="fw-bold mb-4" style="color: #1a5276;">Có thể bạn cũng thích</h3>
        <div class="row g-4">
            @foreach($relatedRooms as $relRoom)
                <div class="col-lg-3 col-md-6">
                    <div class="card room-card h-100">
                        <a href="{{ route('rooms.show', $relRoom) }}" class="img-wrapper d-block text-decoration-none">
                            <span class="badge-type">{{ $relRoom->roomType->name }}</span>
                            <img src="{{ $relRoom->image ?? $relRoom->roomType->image }}"
                                 class="card-img-top"
                                 alt="Phòng {{ $relRoom->room_number }}">
                            <div class="img-hover-overlay">
                                <span class="badge bg-white text-primary shadow px-3 py-2 fw-semibold">
                                    <i class="bi bi-eye me-1"></i>Xem phòng
                                </span>
                            </div>
                        </a>
                        <div class="card-body d-flex flex-column p-3">
                            <h6 class="fw-bold mb-1">
                                <a href="{{ route('rooms.show', $relRoom) }}" class="text-decoration-none text-dark">
                                    Phòng {{ $relRoom->room_number }}
                                </a>
                            </h6>
                            <small class="text-muted mb-2">Tầng {{ $relRoom->floor }} • {{ $relRoom->roomType->area }} m²</small>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-danger">{{ number_format($relRoom->effective_price, 0, ',', '.') }}đ</span>
                                <a href="{{ route('rooms.show', $relRoom) }}" class="btn btn-outline-primary btn-sm">
                                    Xem phòng
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- ===================== MODAL LIGHTBOX XEM ẢNH TOÀN MÀN HÌNH ===================== --}}
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark text-white border-0 rounded-4 overflow-hidden">
            <div class="modal-header border-secondary py-2 px-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary" id="modalCategory">Danh mục</span>
                    <span class="fw-semibold small" id="modalTitle">Tiêu đề ảnh</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-secondary" id="modalCounter">1 / 10</span>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 position-relative d-flex align-items-center justify-content-center" style="min-height: 520px; max-height: 80vh; background: #0b0f19;">
                {{-- Mũi tên trước --}}
                <button class="gallery-nav-btn prev-btn" type="button" onclick="prevImage()" title="Ảnh trước (Phím ←)">
                    <i class="bi bi-chevron-left"></i>
                </button>

                {{-- Mũi tên sau --}}
                <button class="gallery-nav-btn next-btn" type="button" onclick="nextImage()" title="Ảnh sau (Phím →)">
                    <i class="bi bi-chevron-right"></i>
                </button>

                {{-- Ảnh phóng to --}}
                <img id="modalImage" src="" alt="Ảnh phóng to"
                     class="img-fluid" style="max-height: 75vh; object-fit: contain; transition: opacity 0.25s ease;">
            </div>
            <div class="modal-footer border-secondary py-2 px-3 justify-content-between">
                <small class="text-white-50" id="modalDesc">Mô tả ảnh</small>
                <small class="text-white-50">Dùng phím mũi tên <strong>←</strong> và <strong>→</strong> trên bàn phím để chuyển ảnh</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Dữ liệu toàn bộ album ảnh từ Controller
    const galleryData = @json($gallery);
    let currentIndex = 0;
    let lightboxModalInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('lightboxModal');
        if (modalEl) {
            lightboxModalInstance = new bootstrap.Modal(modalEl);
        }

        // Bắt phím bàn phím: Mũi tên trái/phải để chuyển ảnh
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                prevImage();
            } else if (e.key === 'ArrowRight') {
                nextImage();
            }
        });
    });

    // Mở Modal Lightbox phóng to ảnh
    function openLightbox() {
        updateModalDisplay();
        if (lightboxModalInstance) {
            lightboxModalInstance.show();
        }
    }

    // Chuyển tới ảnh tiếp theo
    function nextImage() {
        currentIndex = (currentIndex + 1) % galleryData.length;
        updateAllDisplays();
    }

    // Chuyển về ảnh trước đó
    function prevImage() {
        currentIndex = (currentIndex - 1 + galleryData.length) % galleryData.length;
        updateAllDisplays();
    }

    // Chọn trực tiếp ảnh theo index
    function goToImage(index) {
        if (index >= 0 && index < galleryData.length) {
            currentIndex = index;
            updateAllDisplays();
        }
    }

    // Cập nhật cả ảnh lớn trên trang và ảnh trong Modal
    function updateAllDisplays() {
        updateMainDisplay();
        updateModalDisplay();
        updateThumbnails();
    }

    // Cập nhật ảnh to trên trang chính
    function updateMainDisplay() {
        const item = galleryData[currentIndex];
        const mainImg = document.getElementById('mainImage');
        mainImg.style.opacity = '0.3';

        setTimeout(() => {
            mainImg.src = item.url;
            document.getElementById('mainTitle').textContent = item.title;
            document.getElementById('mainCategory').textContent = item.category;
            document.getElementById('mainDesc').textContent = item.desc;
            document.getElementById('photoCounter').textContent = `Ảnh ${currentIndex + 1} / ${galleryData.length}`;
            mainImg.style.opacity = '1';
        }, 120);
    }

    // Cập nhật ảnh trong Modal phóng to
    function updateModalDisplay() {
        const item = galleryData[currentIndex];
        const modalImg = document.getElementById('modalImage');
        if (modalImg) {
            modalImg.style.opacity = '0.3';
            setTimeout(() => {
                modalImg.src = item.url;
                document.getElementById('modalTitle').textContent = item.title;
                document.getElementById('modalCategory').textContent = item.category;
                document.getElementById('modalDesc').textContent = item.desc;
                document.getElementById('modalCounter').textContent = `${currentIndex + 1} / ${galleryData.length}`;
                modalImg.style.opacity = '1';
            }, 120);
        }
    }

    // Cập nhật trạng thái viền thumbnail đang chọn & tự cuộn vào vùng nhìn
    function updateThumbnails() {
        const thumbs = document.querySelectorAll('.thumb-card');
        thumbs.forEach((el, idx) => {
            if (idx === currentIndex) {
                el.classList.add('active');
                el.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            } else {
                el.classList.remove('active');
            }
        });
    }

    // Lọc hiển thị thumbnail theo danh mục (Tất cả, Phòng ngủ, Tiện ích)
    function filterCategory(category, buttonElement) {
        document.querySelectorAll('.btn-group button').forEach(b => b.classList.remove('active'));
        buttonElement.classList.add('active');

        const thumbs = document.querySelectorAll('.thumb-card');
        let firstMatchIndex = -1;

        thumbs.forEach((el, idx) => {
            const cat = el.getAttribute('data-category');
            if (category === 'all' || cat === category) {
                el.style.display = 'block';
                if (firstMatchIndex === -1) {
                    firstMatchIndex = idx;
                }
            } else {
                el.style.display = 'none';
            }
        });

        if (firstMatchIndex !== -1) {
            goToImage(firstMatchIndex);
        }
    }

    // Tự động tính tiền khi đổi ngày
    const pricePerNight = {{ $room->effective_price }};
    const inEl  = document.getElementById('detailCheckIn');
    const outEl = document.getElementById('detailCheckOut');

    function calculateTotal() {
        const d1 = new Date(inEl.value);
        const d2 = new Date(outEl.value);
        if (d1 && d2 && d2 > d1) {
            const diffTime = Math.abs(d2 - d1);
            const nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            document.getElementById('calcNights').textContent = nights + ' đêm';
            document.getElementById('calcTotal').textContent = (nights * pricePerNight).toLocaleString('vi-VN') + 'đ';
        }
    }

    if (inEl && outEl) {
        inEl.addEventListener('change', function() {
            const next = new Date(this.value);
            next.setDate(next.getDate() + 1);
            outEl.min = next.toISOString().split('T')[0];
            if (new Date(outEl.value) <= new Date(this.value)) {
                outEl.value = next.toISOString().split('T')[0];
            }
            calculateTotal();
        });

        outEl.addEventListener('change', calculateTotal);
    }
</script>
@endpush
