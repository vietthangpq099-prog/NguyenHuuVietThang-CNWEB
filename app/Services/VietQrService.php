<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Support\Str;

class VietQrService
{
    /**
     * Cấu hình tài khoản ngân hàng thụ hưởng của Radiant Hotel.
     * Số tài khoản dạng số điện thoại ảo theo yêu cầu.
     */
    public const BANK_ID      = 'MB';                  // Ngân hàng TMCP Quân Đội (MBBank)
    public const BANK_NAME    = 'MBBank (Quân Đội)';
    public const ACCOUNT_NO   = '0901234567';          // Số tài khoản (dạng số điện thoại ảo)
    public const ACCOUNT_NAME = 'KHACH SAN RADIANT';   // Tên chủ tài khoản

    /**
     * Tạo URL ảnh mã VietQR chuẩn Napas 247 cho đơn đặt phòng.
     *
     * @param Booking $booking
     * @param float|int|null $amount Số tiền (null = tổng tiền phòng)
     * @param string $template Giao diện mã (compact2, compact, qr_only)
     * @return string URL ảnh QR
     */
    public static function generateBookingQrUrl(Booking $booking, $amount = null, string $template = 'compact2'): string
    {
        $payAmount = (int) ($amount ?? $booking->total_price);

        // Nội dung chuẩn Napas không dấu (tối đa 25 ký tự): RADIANT DP<ID> <SĐT>
        $cleanPhone = preg_replace('/[^0-9]/', '', $booking->guest_phone ?? '');
        $shortPhone = substr($cleanPhone, -4); // 4 số cuối SĐT
        $addInfo    = "RADIANT DP{$booking->id} {$shortPhone}";
        $addInfo    = substr($addInfo, 0, 25);

        return self::buildUrl($payAmount, $addInfo, $template);
    }

    /**
     * Tạo URL ảnh mã VietQR cho Hóa đơn thanh toán (Check-out).
     *
     * @param Invoice $invoice
     * @param string $template
     * @return string
     */
    public static function generateInvoiceQrUrl(Invoice $invoice, string $template = 'compact2'): string
    {
        $payAmount = (int) $invoice->total;
        $addInfo   = "RADIANT HD{$invoice->id}";

        return self::buildUrl($payAmount, $addInfo, $template);
    }

    /**
     * Sinh link API VietQR QuickLink.
     */
    public static function buildUrl(int $amount, string $addInfo, string $template = 'compact2'): string
    {
        $bankId      = self::BANK_ID;
        $accountNo   = self::ACCOUNT_NO;
        $accountName = urlencode(self::ACCOUNT_NAME);
        $encodedInfo = urlencode($addInfo);

        return "https://img.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.png?amount={$amount}&addInfo={$encodedInfo}&accountName={$accountName}";
    }

    /**
     * Trả về thông tin chuyển khoản dạng text để khách copy.
     */
    public static function getBankDetails(): array
    {
        return [
            'bank_id'      => self::BANK_ID,
            'bank_name'    => self::BANK_NAME,
            'account_no'   => self::ACCOUNT_NO,
            'account_name' => self::ACCOUNT_NAME,
        ];
    }
}
