<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Trang chủ khách hàng – Hiển thị danh sách phòng với bộ lọc.
     */
    public function index(Request $request)
    {
        $roomTypes = RoomType::orderBy('base_price')->get();

        // Query phòng với eager loading
        $query = Room::with('roomType');

        // --- Lọc theo loại phòng ---
        if ($request->filled('room_type')) {
            $query->where('room_type_id', $request->room_type);
        }

        // --- Lọc theo mức giá ---
        if ($request->filled('price_range')) {
            $range = explode('-', $request->price_range);
            $min = (int) $range[0];
            $max = isset($range[1]) && $range[1] !== '' ? (int) $range[1] : null;

            $query->where(function ($q) use ($min, $max) {
                $q->whereHas('roomType', function ($sub) use ($min, $max) {
                    $sub->where('base_price', '>=', $min);
                    if ($max) {
                        $sub->where('base_price', '<=', $max);
                    }
                });
            });
        }

        // --- Lọc phòng khả dụng theo ngày check-in / check-out ---
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $checkIn  = $request->check_in;
            $checkOut = $request->check_out;

            // Loại bỏ phòng đã có booking trùng khoảng ngày
            $query->whereDoesntHave('bookings', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', ['confirmed', 'checked_in', 'pending'])
                  ->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn);
            });
        }

        // Chỉ hiển thị phòng trống hoặc đã đặt (nhưng còn available theo ngày)
        $rooms = $query->where('status', 'available')
                       ->orderBy('room_number')
                       ->get();

        // Thống kê nhanh
        $stats = [
            'total'     => Room::count(),
            'available' => Room::where('status', 'available')->count(),
            'booked'    => Room::where('status', 'booked')->count(),
            'occupied'  => Room::where('status', 'occupied')->count(),
        ];

        return view('client.home', compact('rooms', 'roomTypes', 'stats'));
    }

    /**
     * Trang xem chi tiết phòng – hiển thị cảnh quan, môi trường xung quanh, độ yên tĩnh, bộ sưu tập ảnh.
     */
    public function show(Request $request, Room $room)
    {
        $room->load('roomType');

        // Phòng gợi ý khác
        $relatedRooms = Room::with('roomType')
                            ->where('id', '!=', $room->id)
                            ->where('status', 'available')
                            ->take(4)
                            ->get();

        // Bộ sưu tập hình ảnh phòng, cảnh quan và môi trường xung quanh
        $gallery = $this->getRoomGallery($room);

        // Đánh giá môi trường, cảnh quan và độ yên tĩnh
        $environmentInfo = $this->getEnvironmentInfo($room);

        return view('client.rooms.show', compact('room', 'relatedRooms', 'gallery', 'environmentInfo'));
    }

    /**
     * Bộ sưu tập hình ảnh chi tiết theo từng hạng phòng và phòng cụ thể.
     * Chia rõ ràng: Các góc chụp phòng ngủ (đồng nhất phòng) & Tiện ích bên trong khách sạn (đồng nhất địa điểm).
     */
    private function getRoomGallery(Room $room): array
    {
        $typeName = $room->roomType->name ?? 'Standard';

        // Bộ ảnh tiện ích bên trong khách sạn Radiant Hotel (đồng nhất 100% về địa điểm 5 sao)
        $hotelFacilities = [
            [
                'url'      => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1400',
                'title'    => 'Sảnh đón tiếp Grand Lobby sang trọng',
                'category' => 'Tiện ích bên trong khách sạn',
                'desc'     => 'Khu vực sảnh chờ 5 sao lộng lẫy với trần cao, nhân viên phục vụ tận tình 24/7.'
            ],
            [
                'url'      => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=1400',
                'title'    => 'Hồ bơi vô cực nước ấm trên cao',
                'category' => 'Tiện ích bên trong khách sạn',
                'desc'     => 'Hồ bơi thư giãn với tầm nhìn ngắm toàn cảnh thành phố, nước ấm quanh năm.'
            ],
            [
                'url'      => 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?w=1400',
                'title'    => 'Nhà hàng ẩm thực Fine Dining & Buffet sáng',
                'category' => 'Tiện ích bên trong khách sạn',
                'desc'     => 'Phục vụ buffet sáng phong phú ẩm thực Á - Âu cùng thực đơn gọi món chuẩn 5 sao.'
            ],
            [
                'url'      => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1400',
                'title'    => 'Sky Lounge & Cafe thư giãn',
                'category' => 'Tiện ích bên trong khách sạn',
                'desc'     => 'Không gian ngắm hoàng hôn và nhâm nhi cocktail, trà chiều cùng âm nhạc acoustic.'
            ],
            [
                'url'      => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1400',
                'title'    => 'Phòng tập Gym & Fitness Center',
                'category' => 'Tiện ích bên trong khách sạn',
                'desc'     => 'Được trang bị máy móc tập luyện Technogym hiện đại bậc nhất, mở cửa 24/7.'
            ],
            [
                'url'      => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=1400',
                'title'    => 'Khu Spa trị liệu & Xông hơi thảo dược',
                'category' => 'Tiện ích bên trong khách sạn',
                'desc'     => 'Trung tâm chăm sóc sắc đẹp, massage thư giãn và xông hơi đá muối Himalaya.'
            ]
        ];

        // Góc chụp phòng ngủ đồng nhất theo từng hạng phòng
        $roomAngles = [
            'Standard' => [
                [
                    'url'      => $room->image ?? 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=1400',
                    'title'    => 'Toàn cảnh không gian phòng ngủ',
                    'category' => 'Góc chụp phòng ngủ',
                    'desc'     => 'Phòng được bố trí hài hoà với giường đôi Queen-size êm ái, sofa nỉ và cửa sổ kính đón ánh sáng tự nhiên.'
                ],
                [
                    'url'      => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=1400',
                    'title'    => 'Cận cảnh giường ngủ & đèn ngủ ấm cúng',
                    'category' => 'Góc chụp phòng ngủ',
                    'desc'     => 'Nệm lò xo túi êm ái đàn hồi cao, ga gối cotton Ai Cập kháng khuẩn đem lại giấc ngủ sâu tuyệt đối.'
                ],
                [
                    'url'      => 'https://images.unsplash.com/photo-1540518614846-7eded433c457?w=1400',
                    'title'    => 'Góc sofa thư giãn & Bàn làm việc',
                    'category' => 'Góc chụp phòng ngủ',
                    'desc'     => 'Khu vực làm việc tiện nghi với ghế công thái học, đèn bàn bảo vệ mắt và bàn trà tiếp khách.'
                ],
                [
                    'url'      => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=1400',
                    'title'    => 'Phòng tắm khép kín hiện đại',
                    'category' => 'Phòng tắm',
                    'desc'     => 'Vách kính cường lực ngăn khô ướt, vòi tắm hoa sen tăng áp nóng lạnh và gương soi đèn LED.'
                ]
            ],
            'Superior' => [
                [
                    'url'      => $room->image ?? 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=1400',
                    'title'    => 'Toàn cảnh phòng Superior sang trọng',
                    'category' => 'Góc chụp phòng ngủ',
                    'desc'     => 'Diện tích 30m² với giường King rộng rãi, sàn gỗ cao cấp và nội thất tông màu trang nhã.'
                ],
                [
                    'url'      => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=1400',
                    'title'    => 'Góc cận giường ngủ phong cách thanh lịch',
                    'category' => 'Góc chụp phòng ngủ',
                    'desc'     => 'Đầu giường bọc nhung cao cấp, hệ thống công tắc thông minh 1 chạm ngay đầu giường.'
                ],
                [
                    'url'      => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=1400',
                    'title'    => 'Góc đọc sách & Bàn trà ngắm phố',
                    'category' => 'Góc chụp phòng ngủ',
                    'desc'     => 'Ghế bành thư giãn êm ái bên cửa sổ lớn, nơi lý tưởng để đọc sách hoặc thưởng thức tách cà phê.'
                ],
                [
                    'url'      => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=1400',
                    'title'    => 'Phòng tắm tiện nghi sang trọng',
                    'category' => 'Phòng tắm',
                    'desc'     => 'Trang bị vòi sen cây inox 304 cao cấp, máy sấy tóc ion và đầy đủ bộ đồ dùng cá nhân chuẩn 5 sao.'
                ]
            ],
            'Deluxe' => [
                [
                    'url'      => $room->image ?? 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=1400',
                    'title'    => 'Toàn cảnh phòng Deluxe view kính tràn viền',
                    'category' => 'Góc chụp phòng ngủ',
                    'desc'     => 'Không gian mở 40m² với hệ thống kính tràn viền từ trần xuống sàn đón trọn cảnh đẹp thành phố.'
                ],
                [
                    'url'      => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=1400',
                    'title'    => 'Cận cảnh giường ngủ King bọc da cao cấp',
                    'category' => 'Góc chụp phòng ngủ',
                    'desc'     => 'Nệm lò xo túi 7 vùng nâng đỡ cột sống, bộ chăn ga gối lông vũ siêu êm ái.'
                ],
                [
                    'url'      => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=1400',
                    'title'    => 'Khu vực tiếp khách sofa bọc nỉ nhung',
                    'category' => 'Góc chụp phòng ngủ',
                    'desc'     => 'Sofa văng dài cao cấp, Smart TV 55 inch 4K phục vụ giải trí đỉnh cao.'
                ],
                [
                    'url'      => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=1400',
                    'title'    => 'Bồn tắm ngâm mình thư giãn ngắm cảnh',
                    'category' => 'Phòng tắm',
                    'desc'     => 'Bồn tắm nằm ngâm mình với muối khoáng thiên nhiên, thư giãn sau ngày dài.'
                ]
            ],
            'Suite' => [
                [
                    'url'      => $room->image ?? 'https://images.unsplash.com/photo-1591088398332-8a7791972843?w=1400',
                    'title'    => 'Phòng ngủ Master Suite Hoàng gia',
                    'category' => 'Góc chụp phòng ngủ',
                    'desc'     => 'Phòng ngủ phong cách hoàng gia 65m² với giường King siêu lớn và nội thất gỗ tự nhiên.'
                ],
                [
                    'url'      => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=1400',
                    'title'    => 'Phòng khách riêng biệt đẳng cấp',
                    'category' => 'Góc chụp phòng ngủ',
                    'desc'     => 'Phòng khách tách biệt với sofa da thật, bàn làm việc giám đốc và máy pha cafe Nespresso.'
                ],
                [
                    'url'      => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1400',
                    'title'    => 'Khu vực bàn ăn & Quầy Minibar rượu vang',
                    'category' => 'Góc chụp phòng ngủ',
                    'desc'     => 'Bàn ăn gia đình 4 chỗ và tủ bảo quản rượu vang sang trọng sẵn sàng phục vụ.'
                ],
                [
                    'url'      => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?w=1400',
                    'title'    => 'Phòng tắm Jacuzzi massage thuỷ lực',
                    'category' => 'Phòng tắm',
                    'desc'     => 'Bồn sục Jacuzzi massage đôi ốp đá cẩm thạch Ý cùng hệ vòi sen mưa nhiệt đới âm trần.'
                ]
            ],
        ];

        $currentAngles = $roomAngles[$typeName] ?? $roomAngles['Standard'];

        // Kết hợp ảnh phòng ngủ và tiện ích khách sạn thành 1 album hoàn chỉnh
        return array_merge($currentAngles, $hotelFacilities);
    }

    /**
     * Thông tin đánh giá cảnh quan, môi trường xung quanh và độ yên tĩnh của phòng.
     */
    private function getEnvironmentInfo(Room $room): array
    {
        $typeName = $room->roomType->name ?? 'Standard';

        $data = [
            'Standard' => [
                'quietness_score'      => 92,
                'quietness_badge'      => 'Rất yên tĩnh (92/100)',
                'quietness_class'      => 'success',
                'soundproof_system'    => 'Hệ thống cửa kính hộp 2 lớp chân không, tường gạch cách âm tiêu chuẩn 38dB.',
                'noise_level'          => 'Khoảng 25 - 30 dB (tương đương tiếng thì thầm trong thư viện).',
                'corridor_privacy'     => 'Nằm ở khu vực hành lang tĩnh tại tầng ' . $room->floor . ', cách xa thang máy và khu kỹ thuật.',
                'view_scenery'         => 'Hướng nhìn ra sân vườn nội khu và con phố rợp bóng cây. Không gian sáng sủa, thoáng đãng.',
                'surrounding_air'      => 'Không khí trong lành, có cây xanh bao bọc khuôn viên giúp điều hoà nhiệt độ tự nhiên.',
                'natural_lighting'     => 'Cửa sổ lớn đón trọn vẹn ánh sáng tự nhiên ban mai, trang bị rèm 2 lớp cản sáng 100% giúp ngủ ngon giấc ban ngày.',
                'night_sleep_quality'  => 'Giấc ngủ sâu không bị quấy rầy bởi tiếng còi xe hay tiếng bước chân hành lang.'
            ],
            'Superior' => [
                'quietness_score'      => 95,
                'quietness_badge'      => 'Cực kỳ yên tĩnh (95/100)',
                'quietness_class'      => 'success',
                'soundproof_system'    => 'Cửa kính E-Glass cách âm kép cao cấp, cách nhiệt và triệt tiêu 92% tạp âm đô thị.',
                'noise_level'          => 'Dưới 25 dB (không gian tĩnh lặng thư thái tuyệt đối).',
                'corridor_privacy'     => 'Hành lang trải thảm dệt tiêu âm cao cấp tại tầng ' . $room->floor . ', hạn chế tối đa tiếng bước chân.',
                'view_scenery'         => 'Ban công riêng hướng ra công viên và hồ bơi khách sạn. Buổi sáng có thể ngồi uống cà phê ngắm cảnh mây trời.',
                'surrounding_air'      => 'Gần khu vườn nhiệt đới, gió trời tự nhiên lưu thông liên tục mát mẻ quanh năm.',
                'natural_lighting'     => 'Ban công đón nắng sớm dịu nhẹ, hệ rèm tự động thông minh chắn sáng tối đa khi cần nghỉ ngơi.',
                'night_sleep_quality'  => 'Môi trường ngủ lý tưởng đạt tiêu chuẩn khách sạn nghỉ dưỡng 5 sao.'
            ],
            'Deluxe' => [
                'quietness_score'      => 98,
                'quietness_badge'      => 'Gần như tĩnh lặng tuyệt đối (98/100)',
                'quietness_class'      => 'primary',
                'soundproof_system'    => 'Hệ vách tiêu âm đa tầng chuẩn phòng thu âm kết hợp kính cường lực 3 lớp Low-E (48dB).',
                'noise_level'          => 'Khoảng 20 dB (yên ả như không gian tại khu nghỉ dưỡng ngoại ô).',
                'corridor_privacy'     => 'Khu vực tầng ' . $room->floor . ' biệt lập, có kiểm soát thẻ từ từng tầng, không có người lạ qua lại.',
                'view_scenery'         => 'Tầm nhìn Panorama không giới hạn bao trọn cảnh quan thành phố và bầu trời lộng gió từ trên cao.',
                'surrounding_air'      => 'Tầng cao thoáng khí, tránh hoàn toàn bụi mịn đường phố, không gian trong lành dễ chịu.',
                'natural_lighting'     => 'Vách kính vòm panorama ngắm cảnh hoàng hôn rực rỡ và bầu trời đêm lấp lánh sao.',
                'night_sleep_quality'  => 'Được thiết kế chuyên biệt cho khách hàng công tác và nghỉ dưỡng cần sự riêng tư và giấc ngủ sâu.'
            ],
            'Suite' => [
                'quietness_score'      => 99,
                'quietness_badge'      => 'Đỉnh cao Riêng tư & Tĩnh lặng (99/100)',
                'quietness_class'      => 'warning',
                'soundproof_system'    => 'Công nghệ cách âm chuẩn Hoàng gia 55dB, cửa gỗ tự nhiên nguyên khối có đệm khí kín khít.',
                'noise_level'          => 'Dưới 18 dB (không gian tĩnh tâm tuyệt đối, thư thái tinh thần).',
                'corridor_privacy'     => 'Tầng ' . $room->floor . ' VIP chuyên biệt với thang máy riêng, bảo mật và riêng tư 100%.',
                'view_scenery'         => 'Tầm nhìn 360 độ đắt giá nhất toà nhà ngắm nhìn toàn cảnh thành phố, sông nước và vườn chân mây.',
                'surrounding_air'      => 'Khu vườn chân mây (Sky Garden) trên cao cung cấp nguồn oxy tươi mát và hương thơm hoa cỏ tự nhiên.',
                'natural_lighting'     => 'Ánh sáng chan hoà mọi ngóc ngách, hệ thống rèm motor cao cấp điều khiển 1 chạm.',
                'night_sleep_quality'  => 'Trải nghiệm giấc ngủ đế vương trên giường nệm thủ công cao cấp nhất.'
            ],
        ];

        return $data[$typeName] ?? $data['Standard'];
    }
}

