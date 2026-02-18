<?php

declare(strict_types=1);

namespace SilverShop\Discounts\Extensions;

use SilverStripe\Model\List\ArrayList;
use SilverStripe\Core\Extension;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordViewer;
use SilverShop\Discounts\Model\Discount;
use SilverShop\Discounts\Model\PartialUseDiscount;
use SilverShop\Discounts\Model\Modifiers\OrderDiscountModifier;

class DiscountedOrderExtension extends Extension
{
    public $owner;

    public function updateCMSFields(FieldList $fields): void
    {
        $fields->addFieldToTab(
            'Root.Discounts',
            $grid = GridField::create('Discounts', Config::inst()->get(Discount::class, 'plural_name'), $this->Discounts(), GridFieldConfig_RecordViewer::create())
        );

        $grid->setModelClass(Discount::class);
    }

    /**
     * Get all discounts that have been applied to an order.
     *
     * @return ArrayList
     */
    public function Discounts()
    {
        $finalDiscounts = ArrayList::create();

        foreach ($this->getOwner()->Modifiers() as $modifier) {
            if ($modifier instanceof OrderDiscountModifier) {
                foreach ($modifier->Discounts() as $discount) {
                    $finalDiscounts->push($discount);
                }
            }
        }

        foreach ($this->getOwner()->Items() as $item) {
            foreach ($item->Discounts() as $discount) {
                $finalDiscounts->push($discount);
            }
        }

        $finalDiscounts->removeDuplicates();

        return $finalDiscounts;
    }

    /**
     * Remove any partial discounts
     */
    public function onPlaceOrder(): void
    {
        $partials = $this->getOwner()->Discounts()->filter('ClassName', PartialUseDiscount::class);

        foreach ($partials as $discount) {
            //only bother creating a remainder discount, if savings have been made
            if ($savings = $discount->getSavingsForOrder($this->getOwner())) {
                $discount->createRemainder($savings);
                //deactivate discounts
                $discount->Active = false;
                $discount->write();
            }
        }
    }

    /**
     * Remove discounts
     */
    public function removeDiscounts(): void
    {
        foreach ($this->getOwner()->Items() as $item) {
            $item->Discounts()->removeAll();
        }

        foreach ($this->getOwner()->Modifiers() as $modifier) {
            if ($modifier instanceof OrderDiscountModifier) {
                $modifier->Discounts()->removeAll();
            }
        }
    }
}
