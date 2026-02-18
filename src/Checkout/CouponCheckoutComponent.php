<?php

declare(strict_types=1);

namespace SilverShop\Discounts\Checkout;

use SilverStripe\ORM\DataObject;
use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Core\Validation\ValidationException;
use SilverShop\Checkout\Component\CheckoutComponent;
use SilverShop\Discounts\Model\OrderCoupon;
use SilverShop\Discounts\Model\Modifiers\OrderDiscountModifier;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Control\Controller;
use SilverShop\Model\Order;

class CouponCheckoutComponent extends CheckoutComponent
{
    protected $validwhenblank = false;

    public function getFormFields(Order $order): FieldList
    {
        return FieldList::create(
            TextField::create(
                'Code',
                _t(
                    'CouponForm.COUPON',
                    'Enter your coupon code if you have one.'
                )
            )
        );
    }

    public function setValidWhenBlank($valid): void
    {
        $this->validwhenblank = $valid;
    }

    public function validateData(Order $order, array $data): bool
    {
        $result = ValidationResult::create();
        $code = $data['Code'];

        if ($this->validwhenblank && !$code) {
            return $result;
        }

        // check the coupon exists, and can be used
        if (($coupon = OrderCoupon::get_by_code($code)) instanceof DataObject) {
            if (!$coupon->validateOrder($order, ['CouponCode' => $code])) {
                $result->addError($coupon->getMessage(), 'Code');

                throw ValidationException::create($result);
            }
        } else {
            $result->addError(
                _t('OrderCouponModifier.NOTFOUND', 'Coupon could not be found'),
                'Code'
            );

            throw ValidationException::create($result);
        }


        return $result;
    }

    public function getData(Order $order): array
    {
        return [
            'Code' => Controller::curr()->getRequest()->getSession()->get('cart.couponcode')
        ];
    }

    public function setData(Order $order, array $data): Order
    {
        if ($data['Code']) {
            Controller::curr()->getRequest()->getSession()->set('cart.couponcode', strtoupper($data['Code']));
        }

        $order->getModifier(OrderDiscountModifier::class, true);
    }
}
