<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed toàn bộ cơ sở dữ liệu.
     *
     * Chạy: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        // =====================================================================
        // 1. TẠO VAI TRÒ
        // =====================================================================
        $adminRole = Role::create([
            'name'         => 'admin',
            'display_name' => 'Quản trị viên',
        ]);

        $receptionistRole = Role::create([
            'name'         => 'receptionist',
            'display_name' => 'Lễ tân',
        ]);

        $customerRole = Role::create([
            'name'         => 'customer',
            'display_name' => 'Khách hàng',
        ]);

        // =====================================================================
        // 2. TẠO TÀI KHOẢN TEST
        // =====================================================================
        $admin = User::create([
            'name'     => 'Nguyễn Văn Admin',
            'email'    => 'admin@hotel.com',
            'password' => Hash::make('password'),
            'role_id'  => $adminRole->id,
            'phone'    => '0901234567',
        ]);

        $staff = User::create([
            'name'     => 'Trần Thị Lễ Tân',
            'email'    => 'staff@hotel.com',
            'password' => Hash::make('password'),
            'role_id'  => $receptionistRole->id,
            'phone'    => '0912345678',
        ]);

        // Tạo thêm tài khoản khách hàng mẫu
        $customers = collect();
        $customerData = [
            ['name' => 'Lê Văn Hùng',    'email' => 'hung.le@gmail.com',     'phone' => '0987654321'],
            ['name' => 'Phạm Thị Mai',    'email' => 'mai.pham@gmail.com',    'phone' => '0976543210'],
            ['name' => 'Hoàng Đức Anh',   'email' => 'anh.hoang@gmail.com',   'phone' => '0965432109'],
            ['name' => 'Ngô Thanh Tùng',  'email' => 'tung.ngo@gmail.com',    'phone' => '0954321098'],
            ['name' => 'Vũ Minh Châu',    'email' => 'chau.vu@gmail.com',     'phone' => '0943210987'],
            ['name' => 'Đặng Quốc Bảo',   'email' => 'bao.dang@gmail.com',    'phone' => '0932109876'],
            ['name' => 'Bùi Thị Hương',   'email' => 'huong.bui@gmail.com',   'phone' => '0921098765'],
            ['name' => 'Trịnh Văn Nam',   'email' => 'nam.trinh@gmail.com',   'phone' => '0910987654'],
        ];

        foreach ($customerData as $data) {
            $customers->push(User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make('password'),
                'role_id'  => $customerRole->id,
                'phone'    => $data['phone'],
            ]));
        }

        // =====================================================================
        // 3. TẠO LOẠI PHÒNG (4 loại thực tế)
        // =====================================================================
        $standard = RoomType::create([
            'name'        => 'Standard',
            'description' => 'Phòng tiêu chuẩn với đầy đủ tiện nghi cơ bản. Bao gồm giường đôi hoặc 2 giường đơn, điều hoà, TV màn hình phẳng 32 inch, minibar, Wi-Fi miễn phí, phòng tắm riêng với vòi sen.',
            'base_price'  => 450000,
            'capacity'    => 2,
            'area'        => 22,
            'image'       => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800',
        ]);

        $superior = RoomType::create([
            'name'        => 'Superior',
            'description' => 'Phòng hạng ưu đãi rộng rãi hơn với ban công riêng. Bao gồm giường King-size, điều hoà, TV 43 inch, minibar, Wi-Fi miễn phí, bàn làm việc, phòng tắm với bồn tắm đứng và đồ dùng vệ sinh cao cấp.',
            'base_price'  => 750000,
            'capacity'    => 2,
            'area'        => 30,
            'image'       => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800',
        ]);

        $deluxe = RoomType::create([
            'name'        => 'Deluxe',
            'description' => 'Phòng sang trọng với tầm nhìn thành phố tuyệt đẹp. Bao gồm giường King-size, ghế sofa, điều hoà 2 chiều, TV 55 inch, minibar đầy đủ, Wi-Fi tốc độ cao, bàn làm việc, tủ quần áo, phòng tắm rộng với bồn tắm và vòi sen riêng biệt, áo choàng tắm và dép.',
            'base_price'  => 1200000,
            'capacity'    => 3,
            'area'        => 40,
            'image'       => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800',
        ]);

        $suite = RoomType::create([
            'name'        => 'Suite',
            'description' => 'Phòng Suite cao cấp nhất với phòng khách và phòng ngủ riêng biệt. Bao gồm giường King-size, sofa phòng khách, bàn ăn 4 người, điều hoà 2 chiều, TV 65 inch, hệ thống âm thanh, minibar cao cấp, Wi-Fi tốc độ cao, ban công rộng view toàn cảnh, phòng tắm đá cẩm thạch với bồn tắm jacuzzi, đồ dùng vệ sinh hạng sang, máy pha cà phê Nespresso.',
            'base_price'  => 2500000,
            'capacity'    => 4,
            'area'        => 65,
            'image'       => 'https://images.unsplash.com/photo-1591088398332-8a7791972843?w=800',
        ]);

        // =====================================================================
        // 4. TẠO 16 PHÒNG CỤ THỂ (4 tầng × 4 phòng)
        // =====================================================================
        $roomsData = [
            // --- Tầng 1: 4 phòng Standard ---
            ['room_number' => '101', 'room_type_id' => $standard->id, 'floor' => 1, 'status' => 'available',
             'image' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600'],
            ['room_number' => '102', 'room_type_id' => $standard->id, 'floor' => 1, 'status' => 'occupied',
             'image' => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=600'],
            ['room_number' => '103', 'room_type_id' => $standard->id, 'floor' => 1, 'status' => 'available',
             'image' => 'https://images.unsplash.com/photo-1631049552057-403cdb8f0658?w=600'],
            ['room_number' => '104', 'room_type_id' => $standard->id, 'floor' => 1, 'status' => 'cleaning',
             'image' => 'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?w=600'],

            // --- Tầng 2: 4 phòng Superior ---
            ['room_number' => '201', 'room_type_id' => $superior->id, 'floor' => 2, 'status' => 'available',
             'image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600'],
            ['room_number' => '202', 'room_type_id' => $superior->id, 'floor' => 2, 'status' => 'booked',
             'image' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=600'],
            ['room_number' => '203', 'room_type_id' => $superior->id, 'floor' => 2, 'status' => 'available',
             'image' => 'https://images.unsplash.com/photo-1586105251261-72a756497a11?w=600'],
            ['room_number' => '204', 'room_type_id' => $superior->id, 'floor' => 2, 'status' => 'maintenance',
             'image' => 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?w=600'],

            // --- Tầng 3: 4 phòng Deluxe ---
            ['room_number' => '301', 'room_type_id' => $deluxe->id, 'floor' => 3, 'status' => 'occupied',
             'image' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600'],
            ['room_number' => '302', 'room_type_id' => $deluxe->id, 'floor' => 3, 'status' => 'available',
             'image' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=600'],
            ['room_number' => '303', 'room_type_id' => $deluxe->id, 'floor' => 3, 'status' => 'available',
             'image' => 'https://images.unsplash.com/photo-1590073242678-70ee3fc28e8e?w=600'],
            ['room_number' => '304', 'room_type_id' => $deluxe->id, 'floor' => 3, 'status' => 'booked',
             'image' => 'https://images.unsplash.com/photo-1564078516393-cf04bd96897b?w=600'],

            // --- Tầng 4: 4 phòng Suite ---
            ['room_number' => '401', 'room_type_id' => $suite->id, 'floor' => 4, 'status' => 'available',
             'image' => 'https://images.unsplash.com/photo-1591088398332-8a7791972843?w=600'],
            ['room_number' => '402', 'room_type_id' => $suite->id, 'floor' => 4, 'status' => 'occupied',
             'image' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?w=600'],
            ['room_number' => '403', 'room_type_id' => $suite->id, 'floor' => 4, 'status' => 'available',
             'image' => 'https://images.unsplash.com/photo-1631049421450-348ccd7f8949?w=600'],
            ['room_number' => '404', 'room_type_id' => $suite->id, 'floor' => 4, 'status' => 'available',
             'image' => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=600'],
        ];

        $rooms = collect();
        foreach ($roomsData as $data) {
            $rooms->push(Room::create($data));
        }

        // =====================================================================
        // 5. TẠO DANH MỤC DỊCH VỤ PHỤ
        // =====================================================================
        $servicesList = [
            ['name' => 'Bữa sáng buffet',            'description' => 'Buffet sáng tại nhà hàng tầng 1 (6h - 10h)',         'price' => 150000],
            ['name' => 'Giặt ủi quần áo',             'description' => 'Giặt và ủi phẳng, trả trong ngày',                   'price' => 80000],
            ['name' => 'Đưa đón sân bay',             'description' => 'Xe sedan đưa đón sân bay Tân Sơn Nhất (1 chiều)',    'price' => 350000],
            ['name' => 'Nước suối đóng chai',          'description' => 'Nước khoáng Lavie 500ml',                             'price' => 15000],
            ['name' => 'Minibar – Nước ngọt',          'description' => 'Coca-Cola / Pepsi / 7Up lon 330ml',                   'price' => 25000],
            ['name' => 'Minibar – Bia',                'description' => 'Bia Tiger / Heineken lon 330ml',                      'price' => 35000],
            ['name' => 'Spa & Massage',                'description' => 'Massage toàn thân 60 phút tại Spa tầng 5',           'price' => 500000],
            ['name' => 'Thuê xe máy',                  'description' => 'Thuê xe máy tay ga Honda Air Blade (1 ngày)',         'price' => 120000],
            ['name' => 'Phụ thu giường phụ',           'description' => 'Thêm 1 giường đơn phụ trong phòng',                  'price' => 200000],
            ['name' => 'Dịch vụ phòng (Room service)', 'description' => 'Gọi đồ ăn / thức uống về phòng',                    'price' => 50000],
        ];

        $services = collect();
        foreach ($servicesList as $data) {
            $services->push(Service::create($data));
        }

        // =====================================================================
        // 6. TẠO 10 ĐƠN ĐẶT PHÒNG MẪU
        // =====================================================================
        $today = Carbon::today();

        $bookingsData = [
            // --- Đặt phòng đã trả phòng (quá khứ) ---
            [
                'user_id'        => $customers[0]->id,
                'room_id'        => $rooms->where('room_number', '101')->first()->id,
                'guest_name'     => 'Lê Văn Hùng',
                'guest_phone'    => '0987654321',
                'guest_email'    => 'hung.le@gmail.com',
                'check_in_date'  => $today->copy()->subDays(10),
                'check_out_date' => $today->copy()->subDays(7),
                'guests_count'   => 2,
                'status'         => 'checked_out',
                'payment_status' => 'paid',
                'total_price'    => 450000 * 3, // 3 đêm × 450.000đ
                'notes'          => 'Khách yêu cầu phòng yên tĩnh.',
            ],
            [
                'user_id'        => $customers[1]->id,
                'room_id'        => $rooms->where('room_number', '301')->first()->id,
                'guest_name'     => 'Phạm Thị Mai',
                'guest_phone'    => '0976543210',
                'guest_email'    => 'mai.pham@gmail.com',
                'check_in_date'  => $today->copy()->subDays(8),
                'check_out_date' => $today->copy()->subDays(5),
                'guests_count'   => 1,
                'status'         => 'checked_out',
                'payment_status' => 'paid',
                'total_price'    => 1200000 * 3, // 3 đêm × 1.200.000đ
                'notes'          => null,
            ],

            // --- Đặt phòng đang ở (hiện tại) ---
            [
                'user_id'        => $customers[2]->id,
                'room_id'        => $rooms->where('room_number', '102')->first()->id,
                'guest_name'     => 'Hoàng Đức Anh',
                'guest_phone'    => '0965432109',
                'guest_email'    => 'anh.hoang@gmail.com',
                'check_in_date'  => $today->copy()->subDays(2),
                'check_out_date' => $today->copy()->addDays(1),
                'guests_count'   => 2,
                'status'         => 'checked_in',
                'payment_status' => 'unpaid',
                'total_price'    => 450000 * 3,
                'notes'          => 'Khách đi công tác, cần hoá đơn VAT.',
            ],
            [
                'user_id'        => $customers[3]->id,
                'room_id'        => $rooms->where('room_number', '301')->first()->id,
                'guest_name'     => 'Ngô Thanh Tùng',
                'guest_phone'    => '0954321098',
                'guest_email'    => 'tung.ngo@gmail.com',
                'check_in_date'  => $today->copy()->subDays(1),
                'check_out_date' => $today->copy()->addDays(3),
                'guests_count'   => 2,
                'status'         => 'checked_in',
                'payment_status' => 'partial',
                'total_price'    => 1200000 * 4,
                'notes'          => 'Đặt cọc 2.000.000đ.',
            ],
            [
                'user_id'        => $customers[4]->id,
                'room_id'        => $rooms->where('room_number', '402')->first()->id,
                'guest_name'     => 'Vũ Minh Châu',
                'guest_phone'    => '0943210987',
                'guest_email'    => 'chau.vu@gmail.com',
                'check_in_date'  => $today->copy()->subDays(1),
                'check_out_date' => $today->copy()->addDays(2),
                'guests_count'   => 3,
                'status'         => 'checked_in',
                'payment_status' => 'unpaid',
                'total_price'    => 2500000 * 3,
                'notes'          => 'Khách VIP, yêu cầu hoa tươi trong phòng.',
            ],

            // --- Đặt phòng đã xác nhận (tương lai) ---
            [
                'user_id'        => $customers[5]->id,
                'room_id'        => $rooms->where('room_number', '202')->first()->id,
                'guest_name'     => 'Đặng Quốc Bảo',
                'guest_phone'    => '0932109876',
                'guest_email'    => 'bao.dang@gmail.com',
                'check_in_date'  => $today->copy()->addDays(2),
                'check_out_date' => $today->copy()->addDays(5),
                'guests_count'   => 2,
                'status'         => 'confirmed',
                'payment_status' => 'paid',
                'total_price'    => 750000 * 3,
                'notes'          => 'Đã thanh toán online qua VNPAY.',
            ],
            [
                'user_id'        => $customers[6]->id,
                'room_id'        => $rooms->where('room_number', '304')->first()->id,
                'guest_name'     => 'Bùi Thị Hương',
                'guest_phone'    => '0921098765',
                'guest_email'    => 'huong.bui@gmail.com',
                'check_in_date'  => $today->copy()->addDays(5),
                'check_out_date' => $today->copy()->addDays(7),
                'guests_count'   => 2,
                'status'         => 'confirmed',
                'payment_status' => 'unpaid',
                'total_price'    => 1200000 * 2,
                'notes'          => 'Kỷ niệm ngày cưới, yêu cầu trang trí phòng.',
            ],

            // --- Đặt phòng chờ xác nhận ---
            [
                'user_id'        => $customers[7]->id,
                'room_id'        => $rooms->where('room_number', '203')->first()->id,
                'guest_name'     => 'Trịnh Văn Nam',
                'guest_phone'    => '0910987654',
                'guest_email'    => 'nam.trinh@gmail.com',
                'check_in_date'  => $today->copy()->addDays(7),
                'check_out_date' => $today->copy()->addDays(10),
                'guests_count'   => 1,
                'status'         => 'pending',
                'payment_status' => 'unpaid',
                'total_price'    => 750000 * 3,
                'notes'          => null,
            ],

            // --- Đặt phòng đã huỷ ---
            [
                'user_id'        => $customers[0]->id,
                'room_id'        => $rooms->where('room_number', '401')->first()->id,
                'guest_name'     => 'Lê Văn Hùng',
                'guest_phone'    => '0987654321',
                'guest_email'    => 'hung.le@gmail.com',
                'check_in_date'  => $today->copy()->subDays(3),
                'check_out_date' => $today->copy()->subDays(1),
                'guests_count'   => 4,
                'status'         => 'cancelled',
                'payment_status' => 'unpaid',
                'total_price'    => 2500000 * 2,
                'notes'          => 'Khách huỷ do thay đổi lịch trình.',
            ],

            // --- Đặt phòng tại quầy (walk-in, không có tài khoản) ---
            [
                'user_id'        => null,
                'room_id'        => $rooms->where('room_number', '103')->first()->id,
                'guest_name'     => 'David Johnson',
                'guest_phone'    => '0899887766',
                'guest_email'    => 'david.j@yahoo.com',
                'check_in_date'  => $today->copy()->addDays(1),
                'check_out_date' => $today->copy()->addDays(4),
                'guests_count'   => 1,
                'status'         => 'confirmed',
                'payment_status' => 'unpaid',
                'total_price'    => 450000 * 3,
                'notes'          => 'Khách nước ngoài, walk-in tại quầy lễ tân.',
            ],
        ];

        $bookings = collect();
        foreach ($bookingsData as $data) {
            $bookings->push(Booking::create($data));
        }

        // =====================================================================
        // 7. GẮN DỊCH VỤ PHỤ CHO MỘT SỐ ĐẶT PHÒNG
        // =====================================================================
        // Booking #1 (Lê Văn Hùng – đã trả phòng): bữa sáng + giặt ủi
        $bookings[0]->services()->attach([
            $services[0]->id => ['quantity' => 3, 'unit_price' => 150000, 'total_price' => 450000],  // 3 bữa sáng
            $services[1]->id => ['quantity' => 2, 'unit_price' => 80000,  'total_price' => 160000],  // 2 lần giặt
        ]);

        // Booking #3 (Hoàng Đức Anh – đang ở): bữa sáng + nước suối
        $bookings[2]->services()->attach([
            $services[0]->id => ['quantity' => 2, 'unit_price' => 150000, 'total_price' => 300000],
            $services[3]->id => ['quantity' => 4, 'unit_price' => 15000,  'total_price' => 60000],
        ]);

        // Booking #5 (Vũ Minh Châu – đang ở Suite): spa + đưa đón sân bay + minibar
        $bookings[4]->services()->attach([
            $services[6]->id => ['quantity' => 1, 'unit_price' => 500000, 'total_price' => 500000],  // Spa
            $services[2]->id => ['quantity' => 2, 'unit_price' => 350000, 'total_price' => 700000],  // 2 chiều sân bay
            $services[5]->id => ['quantity' => 3, 'unit_price' => 35000,  'total_price' => 105000],  // 3 bia
        ]);

        // Booking #4 (Ngô Thanh Tùng – đang ở Deluxe): bữa sáng + room service
        $bookings[3]->services()->attach([
            $services[0]->id => ['quantity' => 2, 'unit_price' => 150000, 'total_price' => 300000],
            $services[9]->id => ['quantity' => 1, 'unit_price' => 50000,  'total_price' => 50000],
        ]);

        // =====================================================================
        // 8. TẠO HOÁ ĐƠN CHO CÁC ĐẶT PHÒNG ĐÃ THANH TOÁN
        // =====================================================================

        // Hoá đơn cho Booking #1 (Lê Văn Hùng)
        $subtotal1 = 1350000 + 450000 + 160000; // phòng + bữa sáng + giặt ủi = 1.960.000
        $tax1      = round($subtotal1 * 0.1);     // VAT 10%
        $invoice1  = Invoice::create([
            'booking_id'     => $bookings[0]->id,
            'invoice_number' => 'INV-2024-0001',
            'issued_at'      => $today->copy()->subDays(7),
            'due_date'       => $today->copy()->subDays(7),
            'subtotal'       => $subtotal1,
            'tax'            => $tax1,
            'total'          => $subtotal1 + $tax1,
            'status'         => 'paid',
        ]);

        InvoiceItem::create([
            'invoice_id'  => $invoice1->id,
            'description' => 'Phòng 101 (Standard) × 3 đêm',
            'unit_price'  => 450000,
            'quantity'    => 3,
            'line_total'  => 1350000,
        ]);
        InvoiceItem::create([
            'invoice_id'  => $invoice1->id,
            'description' => 'Bữa sáng buffet × 3',
            'unit_price'  => 150000,
            'quantity'    => 3,
            'line_total'  => 450000,
        ]);
        InvoiceItem::create([
            'invoice_id'  => $invoice1->id,
            'description' => 'Giặt ủi quần áo × 2',
            'unit_price'  => 80000,
            'quantity'    => 2,
            'line_total'  => 160000,
        ]);

        // Hoá đơn cho Booking #2 (Phạm Thị Mai)
        $subtotal2 = 3600000; // 3 đêm Deluxe
        $tax2      = round($subtotal2 * 0.1);
        $invoice2  = Invoice::create([
            'booking_id'     => $bookings[1]->id,
            'invoice_number' => 'INV-2024-0002',
            'issued_at'      => $today->copy()->subDays(5),
            'due_date'       => $today->copy()->subDays(5),
            'subtotal'       => $subtotal2,
            'tax'            => $tax2,
            'total'          => $subtotal2 + $tax2,
            'status'         => 'paid',
        ]);

        InvoiceItem::create([
            'invoice_id'  => $invoice2->id,
            'description' => 'Phòng 301 (Deluxe) × 3 đêm',
            'unit_price'  => 1200000,
            'quantity'    => 3,
            'line_total'  => 3600000,
        ]);

        // Hoá đơn cho Booking #6 (Đặng Quốc Bảo – đã thanh toán online)
        $subtotal3 = 2250000; // 3 đêm Superior
        $tax3      = round($subtotal3 * 0.1);
        $invoice3  = Invoice::create([
            'booking_id'     => $bookings[5]->id,
            'invoice_number' => 'INV-2024-0003',
            'issued_at'      => $today,
            'due_date'       => $today->copy()->addDays(2),
            'subtotal'       => $subtotal3,
            'tax'            => $tax3,
            'total'          => $subtotal3 + $tax3,
            'status'         => 'paid',
        ]);

        InvoiceItem::create([
            'invoice_id'  => $invoice3->id,
            'description' => 'Phòng 202 (Superior) × 3 đêm',
            'unit_price'  => 750000,
            'quantity'    => 3,
            'line_total'  => 2250000,
        ]);

        // =====================================================================
        // HOÀN TẤT – Tóm tắt dữ liệu đã tạo
        // =====================================================================
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════╗');
        $this->command->info('║    🏨  DỮ LIỆU MẪU ĐÃ ĐƯỢC TẠO THÀNH CÔNG    ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  👤 Vai trò        : 3 (Admin, Lễ tân, Khách)  ║');
        $this->command->info('║  👥 Người dùng     : 10                        ║');
        $this->command->info('║  🏷️  Loại phòng     : 4                        ║');
        $this->command->info('║  🚪 Phòng          : 16                        ║');
        $this->command->info('║  📋 Đặt phòng      : 10                        ║');
        $this->command->info('║  🛎️  Dịch vụ        : 10                        ║');
        $this->command->info('║  🧾 Hoá đơn        : 3                         ║');
        $this->command->info('╠══════════════════════════════════════════════════╣');
        $this->command->info('║  🔑 admin@hotel.com  / password  (Quản trị)    ║');
        $this->command->info('║  🔑 staff@hotel.com  / password  (Lễ tân)      ║');
        $this->command->info('╚══════════════════════════════════════════════════╝');
        $this->command->info('');
    }
}
