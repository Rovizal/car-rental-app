<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\PriceHelper;

class PriceHelperTest extends TestCase
{
    public function test_apply_discount()
    {
        $result = PriceHelper::applyDiscount(200, 25);
        $this->assertEquals(150, $result);
    }

    public function test_zero_discount()
    {
        $result = PriceHelper::applyDiscount(100, 0);
        $this->assertEquals(100, $result);
    }

    public function test_full_discount()
    {
        $result = PriceHelper::applyDiscount(100, 100);
        $this->assertEquals(0, $result);
    }
}
