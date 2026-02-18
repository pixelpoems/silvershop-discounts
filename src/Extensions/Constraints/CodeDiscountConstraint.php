<?php

declare(strict_types=1);

namespace SilverShop\Discounts\Extensions\Constraints;

use SilverShop\Discounts\Model\Discount;
use SilverStripe\ORM\DataList;

class CodeDiscountConstraint extends DiscountConstraint
{
    private static array $db = [
        'Code' => 'Varchar(25)'
    ];

    public function filter(DataList $list)
    {
        if ($code = $this->findCouponCode()) {
            return $list
                ->where(sprintf("(\"Code\" IS NULL) OR (\"Code\" = '%s')", $code));
        }

        return $list->where('"Code" IS NULL');
    }

    public function check(Discount $discount): bool
    {
        $code = strtolower($this->findCouponCode() ?? '');

        if ($discount->Code && ($code !== strtolower($discount->Code ?? ''))) {
            $this->error("Coupon code doesn't match " . $code);
            return false;
        }

        return true;
    }

    protected function findCouponCode()
    {
        return isset($this->context['CouponCode']) ? $this->context['CouponCode'] : null;
    }
}
