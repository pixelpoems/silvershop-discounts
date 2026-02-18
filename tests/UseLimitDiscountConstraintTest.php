<?php

declare(strict_types=1);

namespace SilverShop\Discounts\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverShop\Tests\ShopTest;
use SilverShop\Discounts\Model\OrderCoupon;
use SilverShop\Model\Order;

final class UseLimitDiscountConstraintTest extends SapphireTest
{

    public $cart;

    protected static $fixture_file = [
        'shop.yml',
        'Discounts.yml'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        ShopTest::setConfiguration();
        $this->cart = $this->objFromFixture(Order::class, 'cart');
    }

    public function testUseLimit(): void
    {
        $coupon = $this->objFromFixture(OrderCoupon::class, 'used');
        $context = ['CouponCode' => $coupon->Code];
        $this->assertFalse($coupon->validateOrder($this->cart, $context), 'Coupon is already used');
        $coupon = $this->objFromFixture(OrderCoupon::class, 'limited');
        $context = ['CouponCode' => $coupon->Code];
        $this->assertTrue($coupon->validateOrder($this->cart, $context),
            'Coupon has been used, but can continue to be used');
    }
}
