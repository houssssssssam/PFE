<?php

namespace Tests\Unit;

use App\Services\PaymentService;
use PHPUnit\Framework\TestCase;

class PaymentServiceTest extends TestCase
{
    public function test_expert_share_is_80_percent(): void
    {
        $amount      = 100.00;
        $expertShare = round($amount * 0.80, 2);

        $this->assertEquals(80.00, $expertShare);
    }

    public function test_platform_fee_is_20_percent(): void
    {
        $amount      = 250.00;
        $platformFee = round($amount * 0.20, 2);

        $this->assertEquals(50.00, $platformFee);
    }

    public function test_split_adds_up_to_total(): void
    {
        $amount      = 333.00;
        $expertShare = round($amount * 0.80, 2);
        $platform    = round($amount * 0.20, 2);

        $this->assertEquals($amount, $expertShare + $platform);
    }
}
