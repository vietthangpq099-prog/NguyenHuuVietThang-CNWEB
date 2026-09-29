<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Invoice;
use App\Services\VietQrService;
use PHPUnit\Framework\TestCase;

class VietQrServiceTest extends TestCase
{
    public function test_build_vietqr_url_contains_bank_and_account(): void
    {
        $url = VietQrService::buildUrl(500000, 'TEST PAYMENT');

        $this->assertStringContainsString('img.vietqr.io/image/MB-0901234567-compact2.png', $url);
        $this->assertStringContainsString('amount=500000', $url);
        $this->assertStringContainsString('accountName=KHACH+SAN+RADIANT', $url);
    }

    public function test_bank_details_returns_configured_data(): void
    {
        $details = VietQrService::getBankDetails();

        $this->assertEquals('MB', $details['bank_id']);
        $this->assertEquals('0901234567', $details['account_no']);
        $this->assertEquals('KHACH SAN RADIANT', $details['account_name']);
    }
}
