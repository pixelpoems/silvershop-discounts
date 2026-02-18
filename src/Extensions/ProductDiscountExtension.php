<?php

declare(strict_types=1);

namespace SilverShop\Discounts\Extensions;

use SilverStripe\Core\Extension;

class ProductDiscountExtension extends Extension
{
    public $owner;

    private static array $casting = [
        'TotalReduction' => 'Currency'
    ];

    /**
     * Get the difference between the original price and the new price.
     *
     * @param string $original
     *
     * @return float
     */
    public function getTotalReduction($original = 'BasePrice'): float|int
    {
        $reduction = $this->getOwner()->{$original} - $this->getOwner()->sellingPrice();
        //keep it above 0;
        $reduction = $reduction < 0 ? 0 : $reduction;
        return $reduction;
    }

    /**
     * Check if this product or variation has a reduced price.
     */
    public function IsReduced(): bool
    {
        return (bool) $this->getTotalReduction();
    }

    /**
     * @return int
     */
    public function getDiscountedProductID()
    {
        return $this->getOwner()->ID;
    }
}
