<?php

declare(strict_types=1);

namespace SilverShop\Discounts\Extensions;

use SilverStripe\Core\Extension;
use SilverShop\Model\OrderItem;
use SilverShop\Discounts\Model\Discount;
use SilverShop\Discounts\ItemPriceInfo;

class DiscountedOrderItem extends Extension
{
    public $owner;

    private static array $db = [
        'Discount' => 'Currency'
    ];

    private static array $many_many = [
        'Discounts' => Discount::class
    ];

    private static array $many_many_extraFields = [
        'Discounts' => [
            'DiscountAmount' => 'Currency'
        ]
    ];

    /**
     * @return int
     */
    public function getDiscountedProductID()
    {
        $productKey = OrderItem::config()->buyable_relationship . 'ID';

        return $this->getOwner()->{$productKey};
    }

    public function getPriceInfoClass(): string
    {
        $class = ItemPriceInfo::class;
        $this->getOwner()->extend('updatePriceInfoClass', $class);
        return $class;
    }
}
