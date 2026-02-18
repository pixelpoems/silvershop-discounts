<?php

declare(strict_types=1);

namespace SilverShop\Discounts\Tests;

use SilverShop\Model\Order;
use SilverStripe\Dev\SapphireTest;
use SilverShop\Tests\ShopTest;
use SilverShop\Discounts\Model\OrderCoupon;
use SilverStripe\Security\Group;
use SilverStripe\Security\Member;

final class GroupDiscountConstraintTest extends SapphireTest
{

    public $cart;

    public $othercart;

    protected static $fixture_file = [
        'shop.yml'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        ShopTest::setConfiguration();
        $this->cart = $this->objFromFixture(Order::class, 'cart');
        $this->othercart = $this->objFromFixture(Order::class, 'othercart');
    }

    public function testMemberGroup(): void
    {
        $coupon = OrderCoupon::create(
            [
            'Title' => 'Special Members Coupon',
            'Code' => 'GROUPED',
            'Type' => 'Percent',
            'Percent' => 0.9,
            'Active' => 1,
            'GroupID' => $this->objFromFixture(Group::class, 'resellers')->ID
            ]
        );
        $coupon->write();

        $context = ['CouponCode' => $coupon->Code];
        $this->assertFalse($coupon->validateOrder($this->cart, $context), 'Invalid for memberless order');
        $context = [
            'CouponCode' => $coupon->Code,
            'Member' => $this->objFromFixture(Member::class, 'bobjones')
        ];
        $this->assertTrue($coupon->validateOrder($this->othercart, $context),
            'Valid because member is in resellers group');
    }
}
