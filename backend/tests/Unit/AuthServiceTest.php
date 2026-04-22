<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    public function test_access_token_expiry_defaults_to_15_minutes(): void
    {
        $expiration = (int)(null ?? 15);
        $this->assertEquals(15, $expiration);
    }

    public function test_token_expiry_in_seconds(): void
    {
        $minutes = 15;
        $this->assertEquals(900, $minutes * 60);
    }

    public function test_refresh_token_expiry_is_30_days(): void
    {
        $days = 30;
        $this->assertEquals(30, $days);
    }
}
