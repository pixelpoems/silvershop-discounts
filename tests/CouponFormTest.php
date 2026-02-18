<?php

declare(strict_types=1);

namespace SilverShop\Discounts\Tests;

use SilverShop\Model\Order;
use SilverStripe\Dev\FunctionalTest;
use SilverShop\Page\Product;
use SilverShop\Page\CheckoutPage;
use SilverShop\Page\CheckoutPageController;
use SilverShop\Discounts\Model\OrderCoupon;
use SilverShop\Discounts\Form\CouponForm;

final class CouponFormTest extends FunctionalTest
{

    protected static $fixture_file = [
        'shop.yml',
        'Page.yml'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->objFromFixture(Product::class, 'socks')->publishRecursive();
    }

    public function testCouponForm(): void
    {
        OrderCoupon::create(
            [
            'Title' => '40% off each item',
            'Code' => '5B97AA9D75',
            'Type' => 'Percent',
            'Percent' => 0.40
            ]
        )->write();

        $checkoutpage = $this->objFromFixture(CheckoutPage::class, 'checkout');
        $checkoutpage->publishRecursive();

        $controller = CheckoutPageController::create($checkoutpage);
        $order =  $this->objFromFixture(Order::class, 'cart');
        $form = CouponForm::create($controller, CouponForm::class, $order);
        $data = ['Code' => '5B97AA9D75'];
        $form->loadDataFrom($data);
        $this->assertTrue($form->validate()->isValid());
        $form->applyCoupon($data, $form);

        $coupon = $controller->getRequest()->getSession()->get('cart.couponcode');
        $this->assertEquals('5B97AA9D75', $coupon);
        $form->removeCoupon([], $form);
    }
}
